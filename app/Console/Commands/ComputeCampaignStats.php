<?php

namespace App\Console\Commands;

use App\Models\ActivationLead;
use App\Models\CampaignStat;
use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Event;
use Illuminate\Console\Command;

class ComputeCampaignStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compute-campaign-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute the homepage National Journey counters (divisions/districts covered, institutions activated, participants reached) for every edition, skipping any counter an admin has manually overridden.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Event::each(function (Event $event) {
            $districts = District::where('event_id', $event->id);
            $campusPrograms = CampusProgram::where('event_id', $event->id);

            // Participants reached currently sums district headcounts plus local
            // activation-lead sign-ups; Grand Summit registrations (Phase 6) will
            // be added into this total once that table exists.
            $leadCount = ActivationLead::query()
                ->where(function ($query) use ($event) {
                    $query->whereHasMorph('activatable', District::class, fn ($q) => $q->where('event_id', $event->id))
                        ->orWhereHasMorph('activatable', CampusProgram::class, fn ($q) => $q->where('event_id', $event->id));
                })
                ->count();

            $computed = [
                'divisions_covered' => (clone $districts)->distinct('division_id')->count('division_id'),
                'districts_covered' => (clone $districts)->where('status', 'completed')->count(),
                'institutions_activated' => (clone $campusPrograms)->where('status', 'completed')->count(),
                'participants_reached' => (clone $districts)->sum('participants_count') + $leadCount,
            ];

            foreach ($computed as $key => $value) {
                $stat = CampaignStat::firstOrCreate(
                    ['event_id' => $event->id, 'key' => $key],
                    ['value' => 0, 'is_manual_override' => false]
                );

                if (! $stat->is_manual_override) {
                    $stat->update(['value' => $value]);
                }
            }
        });

        $this->info('Campaign stats recomputed.');

        return self::SUCCESS;
    }
}
