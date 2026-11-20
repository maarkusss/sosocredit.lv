<?php

namespace App\Http\Controllers\Admincp\Sections;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Section;
use Illuminate\Contracts\Support\Renderable;

class IndexController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.sections.index')->with([
            'sections' => Section::query()
                ->where('lang', app()->getLocale())
                ->where('parent_section_id', null)
                ->orderBy('order')
                ->get(),
            'meta' => $this->metaTitle('Sections'),
        ]);
    }

    /**
     * Save edits
     */
    public function update()
    {
        $arr = request()->section['order'];

        if (request()->subsection) {
            $subs_arr = request()->subsection['order'];

            asort($subs_arr);

            $i = 0;

            foreach ($subs_arr as $id => $value) {
                $i++;

                Section::query()
                    ->where('id', $id)
                    ->update([
                        'order' => $i,
                    ]);
            }

            foreach (request()->subsection['active'] as $id => $value) {
                Section::query()
                    ->where('id', $id)
                    ->update([
                        'active' => $value
                    ]);
            }
        }

        asort($arr);

        $i = 0;

        foreach ($arr as $id => $value) {
            $i++;

            Section::query()
                ->where('id', $id)
                ->update([
                    'order' => $i,
                ]);
        }

        foreach (request()->section['active'] as $id => $value) {
            Section::query()
                ->where('id', $id)
                ->update([
                    'active' => $value
                ]);
        }

        $this->logAdminAction('Updated all section info on Section main page');

        return redirect()->back()->with('success', 'Sections updated!');
    }
}
