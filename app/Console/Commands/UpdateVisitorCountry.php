<?php

namespace App\Console\Commands;

use App\Models\Visitor;
use App\Services\GeoIp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdateVisitorCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitors:update-country {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill value for visitors with empty `visitors.country` column via GeoIP';

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
        $visitors = $this->visitors_with_empty_country($limit);

        if ($visitors->isNotEmpty()) {
            /** @var Visitor $visitor */
            foreach ($visitors as $visitor) {
                if (empty($visitor->country) && !empty($visitor->ip) && $country = $this->service->getCountryCodeByIp($visitor->ip)) {
                    $this->comment('Updating visitor ' . $visitor->id);
                    Visitor::query()
                        ->where('id', $visitor->id)
                        ->update([
                            'country' => $country,
                        ]);

                    usleep(50000);
                }
            }
        }

        return Command::SUCCESS;
    }

    protected function visitors_with_empty_country(int $limit = 20): Collection
    {
        $last_two_days = Carbon::today();
        $last_two_days->subDays(2);

        return Visitor::query()
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
