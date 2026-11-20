<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Section;
use Illuminate\Console\Command;

class GenerateSectionStepsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sections:generate-steps {--dry-run : Only show what would be created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create all required steps (step1 - step5) for applicant landing page sections';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $landingSections = Section::query()
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->orderBy('id', 'ASC')
            ->get();

        if ($landingSections->isEmpty()) {
            $this->info('No applicant landing page sections found.');
            return 0;
        }

        $createdCount = 0;

        foreach ($landingSections as $landingSection) {
            $this->line("Processing Section #{$landingSection->id} [{$landingSection->lang}]: {$landingSection->name} ({$landingSection->route_name})");

            for ($step = 1; $step <= Application::MAX_STEPS; $step++) {
                $routeName = 'step' . $step;

                $stepExists = Section::query()
                    ->where('parent_section_id', $landingSection->id)
                    ->where('route_name', $routeName)
                    ->exists();

                if ($stepExists) {
                    continue;
                }

                $stepData = [
                    'parent_section_id' => $landingSection->id,
                    'main_page' => 0,
                    'contact_us' => 0,
                    'lang' => $landingSection->lang,
                    'title' => $landingSection->name . ' - Step ' . $step,
                    'description' => $landingSection->description,
                    'keywords' => $landingSection->keywords,
                    'route_name' => $routeName,
                    'name' => $landingSection->name . ' - Step ' . $step,
                    'template' => Section::TEMPLATE_APPLICANT_FORM_STEP,
                    'order' => $step * 10,
                    'active' => 1,
                ];

                if ($isDryRun) {
                    $this->comment("  [DRY-RUN] Would create: {$routeName} for section #{$landingSection->id}");
                } else {
                    Section::create($stepData);
                    $this->info("  [CREATED] Step section: {$routeName} for section #{$landingSection->id}");
                    $createdCount++;
                }
            }
        }

        $this->info("Finished! Total step sections created: {$createdCount}");

        return 0;
    }
}
