<?php

namespace App\Http\Controllers;

use App\Entity\GoLink;
use App\Models\Click;
use App\Models\Lender;
use App\Models\Visitor;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;

class GoController extends Controller
{
    public function __construct(protected GoLink $go_link)
    {
    }

    public function index(int $lender_id, int $position): Renderable
    {
        $lender = Lender::findOrFail($lender_id);
        /** @var Lender $lender */

        $visitor = $this->getVisitor();

        $user = Auth::guard('consumers')->user();
        $this->registerClicks(lender: $lender, visitor: $visitor);

        $link = $this->go_link->build(
            lender: $lender,
            position: $position,
            user: $user
        );

        return view('go')->with([
            'link' => $link
        ]);
    }

    protected function getVisitor(): ?Visitor
    {
        $visitor_id = (int) request()->cookie('visitor_id');

        $visitor = Visitor::find($visitor_id);

        return $visitor;
    }

    protected function registerClicks(Lender $lender, ?Visitor $visitor): void
    {
        Click::create([
            'visitor_id' => request()->cookie('visitor_id') ?? 0,
            'lender_id' => $lender->id,
            'referer' => request()->header('referer'),
            'utm_source' => $this->go_link->utm_data->utm_source,
            'utm_medium' => request()->cookie('utm_medium'),
            'utm_campaign' => $this->go_link->utm_data->utm_campaign,
            'utm_content' => $this->go_link->utm_data->utm_content,
            'gclid' => $this->go_link->utm_data->gclid,
            'ip' => request()->ip(),
            'user_agent' => request()->header('user-agent'),
        ]);

        if ($visitor) {
            $visitor->increment('clicks');
        }

        $lender->increment('clicks');
    }
}
