<?php

namespace App\Http\Controllers\Admincp;

use App\Models\ConnectedUrl;
use App\Models\Section;
use App\Models\Section\SectionHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SectionsController extends AdminController
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
    public function updateAll()
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

    public function create(): Renderable
    {
        $sections = Section::query()
            ->where('lang', app()->getLocale())
            ->whereNull('parent_section_id')
            ->get();

        return view('admincp.sections.edit')->with([
            'history' => new Collection(),
            'meta' => $this->metaTitle('Add a section'),
            'page_title' => 'Add a section',
            'section' => new Section(),
            'sections' => $sections,
            'templates' => $this->getSectionTemplates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'routing_name' => 'required',
            'title' => 'required',
            'common_name' => 'sometimes|nullable',
        ]);

        $parent_id = $request->section && $request->section > 0 ? (int)$request->section : null;

        $section = new Section();
        $section->active = $request->active ?? 0;
        $section->order = $this->getLastOrderId($parent_id);
        $section->parent_section_id = $parent_id;
        $section->lang = app()->getLocale();
        $section->title = $data['title'];
        $section->description = $request->description ?: null;
        $section->keywords = $request->keywords ?: null;
        $section->name = $data['name'];
        $section->route_name = $data['routing_name'];
        $section->common_name = $data['common_name'];
        $section->redirect_link = $request->link;
        $section->source = $request->sectionSource;
        $section->text = $request->sectionText;
        $section->template = $request->template ?: null;
        $section->main_page = !empty($request->main_page) ? 1 : 0;
        $section->in_header_menu = !empty($request->in_header_menu) ? 1 : 0;
        $section->in_footer_menu = !empty($request->in_footer_menu) ? 1 : 0;
        $section->h1 = !empty($request->h1) ? $request->h1 : null;
        $section->h1_description = !empty($request->h1_description) ? $request->h1_description : null;
        $section->save();

        SectionHistory::create([
            'section_id' => $section->id,
            'user_id' => auth()->id(),
            'parent_section_id' => $parent_id,
            'lang' => app()->getLocale(),
            'title' => $data['title'],
            'description' => $request->description ?: null,
            'keywords' => $request->keywords ?: null,
            'name' => $data['name'],
            'route_name' => $data['routing_name'],
            'common_name' => $data['common_name'],
            'redirect_link' => $request->link,
            'source' => $request->sectionSource,
            'text' => $request->sectionText,
            'template' => $request->template,
            'in_header_menu' => !empty($request->in_header_menu) ? 1 : 0,
            'in_footer_menu' => !empty($request->in_footer_menu) ? 1 : 0,
            'h1' => $request->h1 ?: null,
            'h1_description' => $request->h1_description ?: null,
        ]);

        $this->logAdminAction('Created section "' . $data['name'] . '"');

        flush_menus_cache();

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

    /**
     * @return Renderable|RedirectResponse
     */
    public function edit(int $section)
    {
        $section_id = $section;
        $section = $this->getSection($section_id);
        $related_urls = ConnectedUrl::getRelatedUrlsForRoute($section->canonical_url());

        if ($section->lang != app()->getLocale()) {
            return redirect()->route('admincp.sections.index');
        }

        return view('admincp.sections.edit')
            ->with([
                'section' => $section,
                'history' => $this->getSectionHistory($section_id),
                'sections' => $this->getAllSections(),
                'meta' => $this->metaTitle('Edit a section'),
                'related_urls' => $related_urls,
                'templates' => $this->getSectionTemplates(),
                'applicant_form_block1_sets' => $section->template === Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE
                    ? $this->getApplicantFormBlock1Sets()
                    : [],
            ]);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'routing_name' => 'required',
            'title' => 'required',
            'common_name' => 'sometimes|nullable',
        ]);

        $parent_section_id = $request->section > 0 ? (int) $request->section : null;

        $save = [
            'active' => $request->active ?? 0,
            'parent_section_id' => $parent_section_id,
            'title' => $data['title'],
            'description' => $request->description ?: null,
            'keywords' => $request->keywords ?: null,
            'name' => $data['name'],
            'route_name' => $data['routing_name'],
            'common_name' => $data['common_name'],
            'redirect_link' => $request->link,
            'source' => $request->sectionSource,
            'text' => $request->sectionText,
            'template' => $request->template ?: null,
            'in_header_menu' => !empty($request->in_header_menu) ? 1 : 0,
            'in_footer_menu' => !empty($request->in_footer_menu) ? 1 : 0,
            'main_page' => !empty($request->main_page) ? 1 : 0,
            'h1' => $request->h1 ?: null,
            'h1_description' => $request->h1_description ?: null,
            'json' => $request->json ?? [],
        ];

        $save_history = $save;
        $save_history['section_id'] = $id;
        $save_history['user_id'] = auth()->id();
        $save_history['lang'] = app()->getLocale();
        unset($save_history['active']);
        unset($save_history['json']);

        $section_history = SectionHistory::create($save_history);
        $section_history->json = $request->json ?? [];
        $section_history->save();

        Section::query()
            ->where('id', $id)
            ->update($save);

        $this->logAdminAction('Edited section "' . $data['name'] . '"');

        flush_menus_cache();

        return redirect()
            ->back()
            ->with('success', 'Section updated!');
    }

    /**
     * @param $id
     * @return Section|SectionHistory
     */
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

    protected function getApplicantFormBlock1Sets()
    {
        $sets = [];

        $blade_files = Storage::disk('views')->files('includes/applicant-form/block1_sets', false);

        if (!empty($blade_files)) {
            foreach ($blade_files as $blade_file) {
                if (Str::endsWith($blade_file, '.blade.php')) {
                    $template_key = Str::replace(
                        ['includes/applicant-form/block1_sets/', '.blade.php'],
                        ['', ''],
                        $blade_file
                    );

                    $template_name = Str::replace('_', ' ', $template_key);

                    $sets[$template_key] = $template_name;
                }
            }
        }

        return $sets;
    }

    public function destroy(Section $section): RedirectResponse
    {
        if ($section && $section->id) {
            $this->logAdminAction('Deleted sections ' . $section->name);

            $section->delete();

            return redirect()
                ->route('admincp.sections.index')
                ->with([
                    'success' => 'Section deleted successfully',
                ]);
        }

        return redirect()->back();
    }
}
