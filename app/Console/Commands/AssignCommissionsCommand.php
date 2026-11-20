<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Consumer;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssignCommissionsCommand extends Command
{
    protected $signature = 'commissions:assign {--skip=0} {--limit=1000}';

    protected $description = 'Assign commissions to Consumer';

    public function handle(): void
    {
        $limit = (int) $this->option('limit') ?? 1000;
        $assignments = 0;
        $commissions = Commission::query()
            ->where('commission_date', '>', '2023-02-10 00:00:00')
            ->where('consumer_id', 0)
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get();

        $this->info('Extracted commissions: ' . $commissions->count());
        $this->newLine();

        $to_skip = [];
        if (!empty($this->option('skip'))) {
            $to_skip = explode(',', $this->option('skip'));
        }

        foreach ($commissions as $commission) {

            if (empty($to_skip) || !in_array(1, $to_skip)) {
                // Find by s7 (fingerprint)
                if (!empty($commission->s7)) {
                    $consumer = Consumer::query()->where('fingerprint', $commission->s7)->first();
                    if (!empty($consumer->id)) {
                        $commission->timestamps = false;
                        $commission->consumer_id = $consumer->id;
                        $commission->save();
                        $assignments++;
                        $this->info('#1 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                        $this->newLine();
                        continue;
                    }
                }
            }

            if (empty($to_skip) || !in_array(2, $to_skip)) {
                // Find by s5+s4 (utm_source + utm_content)
                if (!empty($commission->utm_source) && !empty($commission->s4)) {
                    // $this->comment('#2 Commission ' . $commission->utm_source . ':' . $commission->s4);
                    $consumers_by_s4 = Consumer::query()
                        ->where('utm_source', $commission->utm_source)
                        ->where('utm_content', $commission->s4)
                        ->get();

                    if ($consumers_by_s4->count() === 1) {
                        foreach ($consumers_by_s4 as $consumer) {
                            $commission->timestamps = false;
                            $commission->consumer_id = $consumer->id;
                            $commission->save();
                            $assignments++;
                            $this->info('#2 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                            $this->newLine();
                        }
                        continue;
                    } elseif ($consumers_by_s4->count() > 0) {
                        $this->error('Found ' . $consumers_by_s4->count() . ' consumers for ' . $commission->utm_source . ':' . $commission->s4);
                        foreach ($consumers_by_s4 as $consumer) {
                            $this->error('#2 Consumer ' . $consumer->id);
                        }
                        $this->newLine();
                    }
                }
            }

            if (empty($to_skip) || !in_array(3, $to_skip)) {
                // Find by s3 (gclid)
                if (!empty($commission->gclid) && $commission->gclid !== "email-automatic" && $commission->gclid !== "sms-automatic") {
                    $this->comment('#3 Commission ' . $commission->gclid);
                    $consumer_by_gclid = Consumer::query()->where('gclid', $commission->gclid)->get();

                    if ($consumer_by_gclid->count() === 1) {
                        foreach ($consumer_by_gclid as $consumer) {
                            $commission->timestamps = false;
                            $commission->consumer_id = $consumer->id;
                            $commission->save();
                            $assignments++;
                            $this->info('#3 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                            $this->newLine();
                        }
                        continue;
                    } elseif ($consumer_by_gclid->count() > 0) {
                        $this->error('#3 Found ' . $consumer_by_gclid->count() . ' consumers for gclid ' . Str::limit($commission->gclid, 20));
                        foreach ($consumer_by_gclid as $consumer) {
                            $this->comment('-- Consumer ' . $consumer->id . ' ' . $consumer->created_at);
                        }
                        $this->newLine();
                    }
                }
            }

            if (empty($to_skip) || !in_array(4, $to_skip)) {
                // Find by s4 (utm_content)
                if (!empty($commission->s4)) {
                    // $this->comment('#4 Commission :' . $commission->s4);
                    $consumer_by_s4 = Consumer::query()->where('utm_content', $commission->s4)->get();

                    if ($consumer_by_s4->count() === 1) {
                        foreach ($consumer_by_s4 as $consumer) {
                            $commission->timestamps = false;
                            $commission->consumer_id = $consumer->id;
                            $commission->save();
                            $assignments++;
                            $this->info('#4 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                            $this->newLine();
                        }
                        continue;
                    } else {
                        $visitors = Visitor::query()->where('utm_content', $commission->s4);
                        if ($visitors->count() === 1) {
                            $this->comment('#4 Commission utm_content ' . $commission->s4 . ' (' . $commission->id . ') ' . $commission->commission_date);
                            $visitor = $visitors->first();
                            $this->info('Visitor ' . $visitor->id . ' ' . $visitor->created_at . ' ' . $visitor->utm_source . ':' . $visitor->utm_campaign);
                            $consumers_by_ip = Consumer::query()->where('ip', $visitor->ip)->get();

                            if ($consumers_by_ip->count() === 1) {
                                $consumer = $consumers_by_ip->first();
                                $this->info('Consumer ' . $consumer->id . ' ' . $consumer->created_at . ' ' . $consumer->utm_source . ':' . $consumer->utm_campaign);

                                if (
                                    $consumer->created_at > $visitor->created_at &&
                                    $consumer->created_at->diffInHours($visitor->created_at) < 2 &&
                                    $commission->commission_date > $consumer->created_at &&
                                    $consumer->created_at->diffInDays($commission->commission_date) < 30 &&
                                    empty($commission->consumer_id)
                                ) {
                                    $commission->timestamps = false;
                                    $commission->consumer_id = $consumer->id;
                                    $commission->save();
                                    $assignments++;
                                    $this->info('#4.1 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                                    continue;
                                }
                            } else {
                                $this->error('Found ' . $consumers_by_ip->count() . ' consumers by visitor\'s ip');
                                foreach ($consumers_by_ip as $consumer) {
                                    $this->comment('-- Consumer ' . $consumer->id . ' ' . $consumer->created_at . ' ' . $consumer->utm_source . ':' . $consumer->utm_campaign);

                                    if (
                                        $consumer->created_at > $visitor->created_at &&
                                        $consumer->created_at->diffInHours($visitor->created_at) < 2 &&
                                        $commission->commission_date > $consumer->created_at &&
                                        $consumer->created_at->diffInDays($commission->commission_date) < 7 &&
                                        empty($commission->consumer_id)
                                    ) {
                                        $commission->timestamps = false;
                                        $commission->consumer_id = $consumer->id;
                                        $commission->save();
                                        $assignments++;
                                        $this->info('#4.2 Assigned consumer ' . $consumer->id . ' to Commission ' . $commission->id);
                                        break;
                                    }

                                }
                            }
                            $this->newLine();
                        }
                    }
                }
            }
        }

        $this->info('Total Assignments made: ' . $assignments);
        $this->newLine();
    }
}
