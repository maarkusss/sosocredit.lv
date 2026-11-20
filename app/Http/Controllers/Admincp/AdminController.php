<?php

namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\LoanType;
use App\Models\Section;
use App\Models\Setting;
use App\Traits\WorksWithMeta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * AdminController to add common stuff to admincp routes
 */
abstract class AdminController extends Controller
{
    use WorksWithMeta;

    public function logAdminAction(?string $message = '')
    {
        if (!empty($message) && auth()->user()) {
            AdminLog::create([
                'user_id' => auth()->id(),
                'log' => $message,
            ]);
        }
    }

    /**
     * @deprecated
     */
    public function getCategories(): Collection
    {
        return $this->getLenderCategories();
    }

    public function getLenderCategories(): Collection
    {
        return Section::where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function getLanguages(): Collection
    {
        return Setting::query()
            ->where('name', 'lang')
            ->get();
    }

    public function getSectionTemplates(): array
    {
        $templates = [];

        $blade_files = Storage::disk('views')->files('sections', false);
        if (!empty($blade_files)) {
            foreach ($blade_files as $blade_file) {
                if (Str::endsWith($blade_file, '.blade.php')) {
                    $template_key = Str::replace(
                        ['sections/', '.blade.php'],
                        ['', ''],
                        $blade_file
                    );

                    if ($template_key === 'section') {
                        continue; // skip "section.blade.php" which is a default template by itself
                    }

                    $template_name = ucwords(Str::replace('_', ' ', $template_key));

                    $templates[$template_key] = $template_name;
                }
            }
        }

        return $templates;
    }
}
