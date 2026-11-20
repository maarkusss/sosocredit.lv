<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Faq;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FaqsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        $faqs = Faq::query()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->paginate($per_page);

        return view('admincp.faqs.index')
            ->with([
                'faqs' => $faqs,
                'meta' => $this->metaTitle('FAQs'),
            ]);
    }

    public function updateAll(): RedirectResponse
    {
        $arr = request()->faqs['order'];
        asort($arr);

        $i = 0;
        foreach ($arr as $id => $value) {
            $i++;
            Faq::query()
                ->where('id', $id)
                ->update([
                    'order' => $i,
                ]);
        }

        foreach (request()->faqs['active'] as $id => $value) {
            Faq::query()
                ->where('id', $id)
                ->update([
                    'active' => $value
                ]);
        }

        $this->logAdminAction('Updated all faqs info on Faqs main page');

        return redirect()
            ->back()
            ->with('success', 'Faqs updated!');
    }

    public function create(): Renderable
    {
        return view('admincp.faqs.edit')
            ->with([
                'meta' => $this->metaTitle('Add a FAQ'),
                'page_title' => 'Add a FAQ',
                'faq' => new Faq(),
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = new Faq();

        $faq->active = !empty($request->active) ? 1 : 0;
        $faq->order = $this->getLastOrderId();
        $faq->lang = app()->getLocale();
        $faq->question = $data['question'];
        $faq->answer = $data['answer'];

        $faq->save();

        $this->logAdminAction('Created faq "' . $data['question'] . '"');

        return redirect()->route('admincp.faqs.index')
            ->with([
                'success' => 'Faq has been created!',
            ]);
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(Faq $faq)
    {
        if ($faq->lang != app()->getLocale()) {
            return redirect()->route('admincp.faqs.index');
        }

        return view('admincp.faqs.edit')->with([
            'faq' => $faq,
            'meta' => $this->metaTitle('Edit a FAQ'),
        ]);
    }

    public function update(Faq $faq, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq->update([
            'active' => !empty($request->active) ? 1 : 0,
            'lang' => app()->getLocale(),
            'question' => $data['question'],
            'answer' => $data['answer'],

        ]);

        $this->logAdminAction('Edited faq "' . $data['question'] . '"');

        return redirect()
            ->back()
            ->with('success', 'Faq updated!');
    }

    public function destroy($id): RedirectResponse
    {
        Faq::query()
            ->where('id', $id)
            ->delete();

        $this->logAdminAction('Deleted faq "' . $id . '"');

        return redirect(route('admincp.faqs.index'))
            ->with('success', 'Faq has been deleted!');
    }

    public function getLastOrderId(): int
    {
        $faq = Faq::query()
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'DESC')
            ->first();

        if ($faq) {
            return $faq->order + 1;
        }

        return 1;
    }
}
