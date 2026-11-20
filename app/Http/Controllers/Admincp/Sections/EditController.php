<?php

namespace App\Http\Controllers\Admincp\Sections;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Section;
use App\Models\Section\SectionHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditController extends AdminController
{
    public function index($section_id): Renderable
    {
        return view('admincp.sections.edit')
            ->with([
                'section' => $this->getSection($section_id),
                'history' => $this->getSectionHistory($section_id),
                'sections' => $this->getAllSections(),
                'meta' => $this->metaTitle('Edit a section'),
            ]);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'routing_name' => 'required',
            'title' => 'required',
        ]);

        if ($request->section > 0) {
            $parent_id = $request->section;
        } else {
            $parent_id = null;
        }

        SectionHistory::create([
            'section_id' => $id,
            'user_id' => auth()->id(),
            'parent_section_id' => $parent_id,
            'main_page' => $request->homepage ?? 0,
            'contact_us' => $request->contact_us ?? 0,
            'lang' => App()->getLocale(),
            'title' => $data['title'],
            'description' => $request->googleDescription,
            'keywords' => $request->googleKeywords,
            'name' => $data['name'],
            'route_name' => $data['routing_name'],
            'redirect_link' => $request->link,
            'source' => $request->sectionSource,
            'text' => $request->sectionText,
        ]);

        Section::where('id', $id)->update([
            'active' => $request->active ?? 0,
            'parent_section_id' => $parent_id,
            'main_page' => $request->homepage ?? 0,
            'contact_us' => $request->contact_us ?? 0,
            'lang' => App()->getLocale(),
            'title' => $data['title'],
            'description' => $request->googleDescription,
            'keywords' => $request->googleKeywords,
            'name' => $data['name'],
            'route_name' => $data['routing_name'],
            'redirect_link' => $request->link,
            'source' => $request->sectionSource,
            'text' => $request->sectionText,
        ]);

        $this->logAdminAction('Edited section "' . $data['name'] . '"');

        return redirect()
            ->back()
            ->with('success', 'Section updated!');
    }

    public function getSection($id)
    {
        if (request()->history) {
            return SectionHistory::query()
                ->where('section_id', $id)
                ->where('id', request()->history)
                ->firstOrFail();
        }

        return Section::findOrFail($id);
    }

    /**
     * Get all sections
     */
    public function getAllSections()
    {
        return Section::query()
            ->where('lang', app()->getLocale())
            ->where('parent_section_id', null)
            ->get();
    }

    /**
     * Get exact section history
     */
    public function getSectionHistory($section_id)
    {
        return SectionHistory::query()
            ->where('section_id', $section_id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}
