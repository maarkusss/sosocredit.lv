<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class LocalhostLvRuSeeder extends Seeder
{
    /**
     * Run the database seeds for local development (LV & RU locales with sections and steps).
     */
    public function run(): void
    {
        $this->command->info('Seeding Localhost LV & RU settings, sections, and steps...');

        // 1. Ensure basic settings exist
        Setting::updateOrCreate(['name' => 'name'], ['value' => 'Sosocredit.lv']);
        Setting::updateOrCreate(['name' => 'country_code'], ['value' => 'lv']);
        Setting::updateOrCreate(['name' => 'phone_extension'], ['value' => '371']);

        foreach (['lv', 'ru'] as $loc) {
            Setting::firstOrCreate([
                'name' => 'lang',
                'value' => $loc,
            ]);
        }

        // 2. Sections structure for LV and RU
        $sections = [
            'paterina-kredits' => [
                'lv' => [
                    'name' => 'Patēriņa kredīts',
                    'route_name' => 'paterina-kredits',
                    'title' => 'Patēriņa kredīts internetā līdz 10 000 €',
                    'description' => 'Izdevīgākais patēriņa kredīts no uzticamiem aizdevējiem Latvijā. Piesakies online dažu minūšu laikā.',
                ],
                'ru' => [
                    'name' => 'Потребительский кредит',
                    'route_name' => 'potrebitelskij-kredit',
                    'title' => 'Потребительский кредит в интернете до 10 000 €',
                    'description' => 'Выгодный потребительский кредит в Латвии. Подай заявку онлайн за несколько минут.',
                ],
                'order' => 10,
            ],
            'auto-kredits' => [
                'lv' => [
                    'name' => 'Auto kredīts',
                    'route_name' => 'auto-kredits',
                    'title' => 'Auto kredīts un auto līzings bez pirmās iemaksas',
                    'description' => 'Saņem auto kredītu automašīnas iegādei ar izdevīgiem procentiem.',
                ],
                'ru' => [
                    'name' => 'Автокредит',
                    'route_name' => 'avtokredit',
                    'title' => 'Автокредит и автолизинг без первого взноса',
                    'description' => 'Получи кредит на покупку авто с выгодной процентной ставкой.',
                ],
                'order' => 20,
            ],
            'parcreditesana' => [
                'lv' => [
                    'name' => 'Kredītu apvienošana',
                    'route_name' => 'kreditu-apvienosana',
                    'title' => 'Kredītu apvienošana vienā maksājumā',
                    'description' => 'Apvieno visus ātros kredītus vienā izdevīgākā maksājumā ar zemāku procentu likmi.',
                ],
                'ru' => [
                    'name' => 'Объединение кредитов',
                    'route_name' => 'obedinenie-kreditov',
                    'title' => 'Объединение кредитов в один платеж',
                    'description' => 'Объедини все быстрые кредиты в один удобный ежемесячный платеж.',
                ],
                'order' => 30,
            ],
            'hipotekarais-kredits' => [
                'lv' => [
                    'name' => 'Hipotekārais kredīts',
                    'route_name' => 'hipotekarais-kredits',
                    'title' => 'Hipotekārais kredīts mājokļa iegādei vai remontam',
                    'description' => 'Izdevīgs hipotekārais aizdevums nekustamā īpašuma iegādei.',
                ],
                'ru' => [
                    'name' => 'Ипотечный кредит',
                    'route_name' => 'ipotechnyj-kredit',
                    'title' => 'Ипотечный кредит на покупку или ремонт жилья',
                    'description' => 'Ипотечный кредит на выгодных условиях в Латвии.',
                ],
                'order' => 40,
            ],
            'kredits-uznemumam' => [
                'lv' => [
                    'name' => 'Kredīts uzņēmumiem',
                    'route_name' => 'kredits-uznemumam',
                    'title' => 'Biznesa kredīts un finansējums uzņēmumiem',
                    'description' => 'Aizdevums uzņēmuma apgrozāmajiem līdzekļiem vai investīcijām.',
                ],
                'ru' => [
                    'name' => 'Кредит для бизнеса',
                    'route_name' => 'kredit-dlja-biznesa',
                    'title' => 'Кредит для бизнеса и финансирование компаний',
                    'description' => 'Кредит для предприятий на оборотные средства и инвестиции.',
                ],
                'order' => 50,
            ],
        ];

        // 3. Create Home pages for LV and RU
        foreach (['lv' => 'Sākums', 'ru' => 'Главная'] as $lang => $name) {
            Section::updateOrCreate(
                [
                    'lang' => $lang,
                    'main_page' => 1,
                ],
                [
                    'name' => $name,
                    'route_name' => 'home',
                    'common_name' => 'home',
                    'title' => $name . ' - Sosocredit.lv',
                    'description' => 'Kredītu salīdzināšana un pieteikšanās internetā.',
                    'template' => Section::TEMPLATE_HOME,
                    'active' => 1,
                    'order' => 1,
                ]
            );
        }

        // 4. Create Loan Sections and Steps for LV and RU
        foreach ($sections as $commonName => $data) {
            foreach (['lv', 'ru'] as $lang) {
                $sectionData = $data[$lang];

                $parentSection = Section::updateOrCreate(
                    [
                        'lang' => $lang,
                        'common_name' => $commonName,
                    ],
                    [
                        'name' => $sectionData['name'],
                        'route_name' => $sectionData['route_name'],
                        'title' => $sectionData['title'],
                        'description' => $sectionData['description'],
                        'template' => Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE,
                        'main_page' => 0,
                        'contact_us' => 0,
                        'active' => 1,
                        'order' => $data['order'],
                    ]
                );

                // Create steps 1 to 5 for each loan section
                for ($step = 1; $step <= Application::MAX_STEPS; $step++) {
                    $stepRoute = 'step' . $step;

                    Section::updateOrCreate(
                        [
                            'parent_section_id' => $parentSection->id,
                            'route_name' => $stepRoute,
                        ],
                        [
                            'name' => $sectionData['name'] . ' - Step ' . $step,
                            'title' => $sectionData['name'] . ' - Solis ' . $step,
                            'lang' => $lang,
                            'common_name' => $commonName . '-' . $stepRoute,
                            'description' => $sectionData['description'],
                            'template' => Section::TEMPLATE_APPLICANT_FORM_STEP,
                            'active' => 1,
                            'main_page' => 0,
                            'contact_us' => 0,
                            'order' => $step * 10,
                        ]
                    );
                }
            }
        }

        $this->command->info('Localhost LV & RU seeding completed successfully!');
    }
}
