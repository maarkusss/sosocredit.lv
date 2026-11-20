<?php

namespace App\Http\Controllers;

use App\Contracts\HoiFmServiceInterface;
use App\Models\HoiLog;
use App\Traits\WorksWithLoadAvg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HoiController extends Controller
{
    use WorksWithLoadAvg;

    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
    }

    public function downloadAndSendEmail(Request $request): JsonResponse
    {
        // 1. LoadAvg pārbaude
        if (!empty(config('app.load_avg_threshold')) && empty($request->force)) {
            $load_avg = $this->getLoadAvg();
            if ($load_avg > config('app.load_avg_threshold')) {
                return new JsonResponse(['status' => 'busy'], 503, [
                    'Retry-After' => 900,
                ]);
            }
        }

        // 2. Obligāto parametru pārbaude
        if (!$request->filled(['email', 'htmllink', 'redirectlink', 'subject'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Missing parameters'], 400);
        }

        $email = trim($request->email);
        $subjectRaw = urldecode($request->subject);
        $today = now()->format('Y-m-d 00:00:00');

        // 3. Ātrā deduplikācijas pārbaude caur Cache / DB
        $existingLog = HoiLog::where('to', $email)
            ->where('subject', $request->subject)
            ->where('created_at', '>=', $today)
            ->first();

        if ($existingLog) {
            $existingLog->increment('attempts');
            return new JsonResponse(['status' => 'already_sent'], 429);
        }

        // 4. HTML šablona lejupielāde AR KEŠOŠANU un 3 sekunžu timeout
        $cacheKey = 'hoi_tpl_' . md5($request->htmllink);
        $content = Cache::remember($cacheKey, 600, function () use ($request) {
            $ctx = stream_context_create([
                'http' => ['timeout' => 3],
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);
            return @file_get_contents($request->htmllink, false, $ctx) ?: null;
        });

        if (!$content) {
            return new JsonResponse(['status' => 'error', 'message' => 'Failed to load HTML template'], 502);
        }

        // 5. E-pasta sagatavošana
        $subject = '=?UTF-8?B?' . base64_encode(stripslashes($subjectRaw)) . '?=';
        $body = str_replace(['{link}', '{email}'], [$request->redirectlink, $email], $content);
        $from_header = $this->hoi_fm->fromHeader();
        $domain = $this->hoi_fm->senderDomain();

        // 6. Nosūtīšana un reģistrēšana
        $this->sendHoiEmail($email, $subject, $body, $from_header, $domain);
        HoiLog::logMail($email, $request->subject, $request->htmllink);

        return new JsonResponse(['status' => 'done']);
    }

    public function sendEmail(Request $request): JsonResponse
    {
        if ($request->filled(['from', 'to', 'subject', 'body'])) {
            $from_domain = $this->hoi_fm->senderDomain();
            $from_header = $request->from . ' <info@' . $from_domain . ">";
            $subject = '=?UTF-8?B?' . base64_encode(stripslashes(urldecode($request->subject))) . '?=';

            $attachments = [];
            if ($request->has('attachments') && is_array($request->attachments)) {
                foreach ($request->attachments as $file) {
                    if (isset($file['filename'], $file['content'])) {
                        $attachments[] = [
                            'filename' => $file['filename'],
                            'content'  => $file['content'],
                            'type'     => $file['type'] ?? 'application/octet-stream',
                        ];
                    }
                }
            }

            $this->sendHoiEmail($request->to, $subject, $request->body, $from_header, $from_domain, $attachments);

            return new JsonResponse(['status' => 'done'], 201);
        }

        return new JsonResponse(['status' => 'error'], 422);
    }

    public function sendHoiEmail($to, $subject, $message, $from, $domain, $attachments = []): void
    {
        $boundary = md5(uniqid() . microtime());
        $boundary_alt = "alt-" . $boundary;

        $headers = "From: " . $from . "\r\n";
        $headers .= "List-Unsubscribe: <" . config('app.url') . "/" . default_locale() . "/unsubscribe/email>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: multipart/alternative; boundary=\"$boundary_alt\"\r\n\r\n";

        $body .= "--$boundary_alt\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode(strip_tags($message))) . "\r\n";

        $body .= "--$boundary_alt\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message)) . "\r\n";
        $body .= "--$boundary_alt--\r\n";

        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (!isset($attachment['filename'], $attachment['content'])) {
                    continue;
                }

                $filename = $attachment['filename'];
                $content = $attachment['content'];
                $type = $attachment['type'] ?? 'application/octet-stream';

                if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $content)) {
                    $content = base64_encode($content);
                }

                $body .= "--$boundary\r\n";
                $body .= "Content-Type: $type; name=\"$filename\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
                $body .= chunk_split($content) . "\r\n";
            }
        }

        $body .= "--$boundary--";

        $recipients = is_array($to) ? $to : [$to];
        foreach ($recipients as $recipient) {
            @mail($recipient, $subject, $body, $headers, '-f info@' . $domain);
        }
    }
}