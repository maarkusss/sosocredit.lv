<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Consumer\ConsumersHoiToken;
use App\Models\Consumer;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConsumersController extends AdminController
{
    public function index(Request $request): Renderable
    {
        $PER_PAGE = 50;
        $columns = [
            'id' => 'ID',
            'phone' => 'phone',
            'email' => 'email',
            'amount' => 'amount',
            'subscribed' => 'subscribed',
            'ip' => 'IP Address',
            'utm_source' => 'utm_source',
            'utm_medium' => 'utm_medium',
            'utm_campaign' => 'utm_campaign',
            'utm_content' => 'utm_content',
            'gclid' => 'gclid / fbc',
            'lang' => 'lang',
            'created_at' => 'created_at',
        ];

        $consumers = Consumer::query();

        if ($request->input('search')) {
            $consumers = $consumers->where(function ($q) use ($request) {
                $q->where('email', 'like', $request->input('search') . '%')
                  ->orWhere('phone', 'like', $request->input('search') . '%');
            });
        }

        if ($id = $request->input('id')) {
            $consumers = $consumers->where('id', $id);
        }

        if ($hoi_token = $request->input('hoi_token')) {
            $consumers_hoi_token = ConsumersHoiToken::query()
                ->where('hoi_token', $hoi_token)
                ->select('id', 'consumer_id')
                ->first();

            if ($consumers_hoi_token && $consumers_hoi_token->id) {
                $consumers = $consumers->where('id', $consumers_hoi_token->consumer_id);
            } else {
                $consumers = $consumers->where('id', 0);
            }
        }

        if ($fingerprint = $request->input('fingerprint')) {
            $consumers = $consumers->where('fingerprint', $fingerprint);
        }

        if ($lang = $request->input('lang')) {
            $consumers = $consumers->where('lang', $lang);
        }

        if ($fbc = $request->input('fbc')) {
            $consumers = $consumers->where('fbc', $fbc);
        }

        if ($fbp = $request->input('fbp')) {
            $consumers = $consumers->where('fbp', $fbp);
        }

        if ($version = $request->input('version')) {
            $consumers = $consumers->where('version', (int) $version);
        }

        if ($visitor_id = $request->input('visitor_id')) {
            $consumers = $consumers->where('visitor_id', $visitor_id);
        }

        if ($request->has_commissions) {
            $consumers = $consumers->whereHas('commissions');
        }

        $consumers = $consumers->orderBy('id', 'DESC')
            ->paginate($PER_PAGE)
            ->appends($request->toArray());

        return view('admincp.consumers.index')->with([
            'consumers' => $consumers,
            'meta' => $this->metaTitle('Consumers'),
            'columns' => $columns,
        ]);
    }

    public function show(Consumer $consumer): Renderable
    {
        $fields = [
            'fingerprint',
            'visitor_id',
            'first_name',
            'last_name',
            'birth',
            'email',
            'email_verified_at',
            'phone',
            'amount',
            'hoi_token',
            'type',
            'confirmed_at',
            'subscribed',
            'lang',
            'ip',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'gclid',
            'fbp',
            'fbc',
            'app_instance_id',
            'fcm_token',
            'created_at',
            'updated_at',
        ];

        $applications = [];
        if ($consumer->applications()->count() > 0) {
            $applications = $consumer->applications()->orderBy('id', 'DESC')->limit(10)->get();
        }

        $sendings = [];
        if ($consumer->sendings()->count() > 0) {
            $sendings = $consumer->sendings()->orderBy('id', 'DESC')->limit(10)->get();
        }

        return view('admincp.consumers.show')
            ->with([
                'applications' => $applications,
                'consumer' => $consumer,
                'fields' => $fields,
                'meta' => $this->metaTitle('View Consumer'),
                'sendings' => $sendings,
            ]);
    }

    public function confirmPhone(int $consumer_id): RedirectResponse
    {
        $consumer = Consumer::find($consumer_id);

        if ($consumer && $consumer->id && empty($consumer->confirmed)) {
            /** @var Consumer $consumer */
            $consumer->markPhoneAsConfirmed();

            return redirect()
                ->back()
                ->with([
                    'success' => 'Consumer\'s phone number ' . $consumer->phone . ' confirmed successfully',
                ]);
        }

        return redirect()
            ->back();
    }

    public function confirmEmail(int $consumer_id): RedirectResponse
    {
        $consumer = Consumer::find($consumer_id);

        if ($consumer && $consumer->id && empty($consumer->email_verified_at)) {
            /** @var Consumer $consumer */
            $consumer->markEmailAsVerified();

            return redirect()
                ->back()
                ->with([
                    'success' => 'Consumer\'s email ' . $consumer->email . ' confirmed successfully',
                ]);
        }

        return redirect()
            ->back();
    }

    public function destroy($id): RedirectResponse
    {
        Consumer::query()
            ->where('id', $id)
            ->delete();

        $this->logAdminAction('Deleted consumer "' . $id . '"');

        return redirect()
            ->back()
            ->with('success', 'Consumer deleted!');
    }
}
