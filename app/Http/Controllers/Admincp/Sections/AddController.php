<?php

namespace App\Http\Controllers\Admincp\Sections;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Section;
use App\Models\Section\SectionHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.sections.add')->with([
            'sections' => Section::query()
                ->where('lang', app()->getLocale())
                ->where('parent_section_id', null)
                ->get(),
            'meta' => $this->metaTitle('Add a section'),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'routing_name' => 'required',
            'title' => 'required',
        ]);

        $parent_id = $request->section && $request->section > 0 ? (int) $request->section : null;

        $section = new Section();
        $section->active = $request->active ?? 0;
        $section->order = $this->getLastOrderId($parent_id);
        $section->parent_section_id = $parent_id;
        $section->main_page = $request->homepage ?? 0;
        $section->contact_us = $request->contact_us ?? 0;
        $section->lang = app()->getLocale();
        $section->title = $data['title'];
        $section->description = $request->googleDescription;
        $section->keywords = $request->googleKeywords;
        $section->name = $data['name'];
        $section->route_name = $data['routing_name'];
        $section->redirect_link = $request->link;
        $section->source = $request->sectionSource;
        $section->text = $request->sectionText;
        $section->save();

        SectionHistory::create([
            'section_id' => $section->id,
            'user_id' => auth()->id(),
            'parent_section_id' => $parent_id,
            'main_page' => $request->homepage ?? 0,
            'contact_us' => $request->contact_us ?? 0,
            'lang' => app()->getLocale(),
            'title' => $data['title'],
            'description' => $request->googleDescription,
            'keywords' => $request->googleKeywords,
            'name' => $data['name'],
            'route_name' => $data['routing_name'],
            'redirect_link' => $request->link,
            'source' => $request->sectionSource,
            'text' => $request->sectionText,
        ]);

        $this->logAdminAction('Created section "' . $data['name'] . '"');

        return redirect(route('admincp.sections.index'))
            ->with('success', 'Section has been created!');
    }

    public function getLastOrderId($parent_id): int
    {
        if ($parent_id > 0) {
            $section = Section::query()
                ->where('parent_section_id', $parent_id)
                ->where('lang', app()->getLocale())
                ->orderBy('order', 'DESC')
                ->first();
        } else {
            $section = Section::query()
                ->orderBy('order', 'DESC')
                ->where('lang', app()->getLocale())
                ->first();
        }

        if ($section) {
            return $section->order + 1;
        }

        return 1;
    }
}
