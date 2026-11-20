<?php

namespace App\Http\Controllers;

use App\Mail\NewDocumentMail;
use App\Models\Document;
use App\Rules\PersonalCode;
use App\Traits\WorksWithHeaderMenu;
use App\Traits\WorksWithMeta;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use WorksWithHeaderMenu;
    use WorksWithMeta;

    public function uploadAndSendViaEmail(Request $request): RedirectResponse
    {
        if (!$request->hasFile('pdf')) {
            return redirect()->back()
                ->with('error', __('Please attach PDF.'))
                ->withInput($request->toArray());
        }

        $validated = $request->validate([
            'name' => 'required|string|max:127',
            'personal_code' => [
                'required',
                'string',
                new PersonalCode(),
            ],
            'pdf' => [
                'required',
                File::types(['pdf'])->max(2 * 1024),
            ],
        ]);

        $document = $request->file('pdf');

        $document_filename = $document->hashName();
        $document_original_filename = $document->getClientOriginalName();
        $document->storeAs(Document::$STORAGE_PATH, $document_filename);

        $document = new Document();
        $document->uuid = Str::uuid();
        $document->token = Str::random(190);
        $document->filename = $document_filename;
        $document->original_filename = $document_original_filename;
        $document->name = $validated['name'];
        $document->personal_code = $validated['personal_code'];
        $document->save();

        Mail::send(new NewDocumentMail($document));

        return redirect()
            ->back()
            ->with([
                'document_sent_successfully' => 'Document sent successfully',
            ]);
    }

    public function downloadPage(string $uuid, string $token, Request $request): Renderable
    {
        $document = Document::query()->where('uuid', $uuid)->first();

        if (empty($document->id)) {
            abort(400, 'Not Found');
        }

        /** @var Document $document */

        if (!hash_equals($token, $document->token)) {
            abort(400, 'Token mismatch');
        }

        if (!empty($document->downloads) && count($document->downloads) >= Document::$MAX_DOWNLOADS) {
            abort(403, 'Cannot download anymore'); // already downloaded
        }

        return view('document_download', [
            'document' => $document,
        ]);
    }

    public function confirmDownload(string $uuid, string $token, Request $request): StreamedResponse
    {
        $document = Document::query()->where('uuid', $uuid)->first();

        if (empty($document->id)) {
            abort(400, 'Not Found');
        }

        /** @var Document $document */

        if (!hash_equals($token, $document->token)) {
            abort(400, 'Token mismatch');
        }

        if (!empty($document->downloads) && count($document->downloads) >= Document::$MAX_DOWNLOADS) {
            abort(403, 'Cannot download anymore'); // already downloaded
        }

        $downloads = $document->downloads ?? [];
        $downloads[] = [
            'downloaded_at' => now(),
            'downloaded_from_ip' => $request->ip(),
        ];

        $document->downloads = $downloads;
        $document->save();

        return Storage::download('/' . Document::$STORAGE_PATH . $document->filename, $document->original_filename);
    }
}
