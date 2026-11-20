<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Services\GeoIp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdateClickCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clicks:update-country {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill value for clicks with empty `clicks.country` column via GeoIP';

    public function __construct(protected GeoIp $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = $this->argument('limit') ?? 20;
        $clicks = $this->clicks_with_empty_country($limit);

        if ($clicks->isNotEmpty()) {
            /** @var Click $click */
            foreach ($clicks as $click) {
                if (empty($click->country) && !empty($click->ip) && $country = $this->service->getCountryCodeByIp($click->ip)) {
                    $this->comment('Updating click ' . $click->id);
                    Click::query()
                        ->where('id', $click->id)
                        ->update([
                            'country' => $country,
                        ]);

                    usleep(50000);
                }
            }
        }

        return Command::SUCCESS;
    }

    protected function clicks_with_empty_country(int $limit = 20): Collection
    {
        $last_two_days = Carbon::today();
        $last_two_days->subDays(2);

        return Click::query()
            ->whereNull('country')
            ->whereNotNull('ip')
            ->where('ip', '!=', '')
            ->where('created_at', '>=', $last_two_days->format('Y-m-d 00:00:00'))
            ->select([
                'id',
                'ip',
                'country',
            ])
            ->take($limit)
            ->orderBy('id', 'ASC')
            ->get();
    }
}
