<?php

namespace App\Console\Commands;

use App\Contracts\HoiFmServiceInterface;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerDevice;
use Illuminate\Console\Command;

class ExportPushConsumersToHoi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumers:export-push-to-hoi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporting PUSH consumers to Hoi.fm Push Project';

    public function __construct(protected HoiFmServiceInterface $hoi_fm)
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
        $consumer_ids_to_export = ConsumerDevice::query()
            ->whereNull('exported_at')
            ->groupBy('consumer_id')
            ->orderBy('consumer_id', 'ASC')
            ->limit(25)
            ->get('consumer_id')
            ->toArray();

        $this->comment('Consumers found: ' . count($consumer_ids_to_export));

        if (!empty($consumer_ids_to_export)) {
            $consumers = Consumer::query()
                ->with('devices_firebase')
                ->whereIn('id', $consumer_ids_to_export)
                ->get();

            /** @var Consumer $consumer */
            foreach ($consumers as $consumer) {
                $status = $this->hoi_fm->addPushConsumer($consumer);

                // Consumer has been already created - so update
                if ($status === 302) {
                    $status = $this->hoi_fm->updatePushConsumer($consumer);
                }

                if ($status === 201) {
                    // Mark all devices as exported
                    $consumer->devices_firebase()->update([
                        'exported_at' => now(),
                    ]);
                }

                $this->info('Exported consumer ' . $consumer->phone . ' [' . $status . ']');
            }
        }

        return 0;
    }
}
