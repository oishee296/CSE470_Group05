<?php

namespace App\Console\Commands;

use App\Models\AuditTrail;
use App\Models\ReliefRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEscalations extends Command
{
    protected $signature = 'requests:check-escalations';
    protected $description = 'Automatically escalate unresolved emergency requests based on waiting time';

    public function handle(): int
    {
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        $now = now();
        $updated = 0;

        $requests = ReliefRequest::whereNotNull('request_type')
            ->whereNotIn('status', ['Resolved'])
            ->get();

        foreach ($requests as $req) {
            $hours = Carbon::parse($req->created_at)->diffInHours($now);

            // Determine escalation tier from waiting time
            $tier = 0;
            $targetIncrease = 0;
            if ($hours >= 6) {
                $tier = 3; // Urgent
                $targetIncrease = 3;
            } elseif ($hours >= 4) {
                $tier = 2;
                $targetIncrease = 2;
            } elseif ($hours >= 2) {
                $tier = 1;
                $targetIncrease = 1;
            } else {
                continue; // Less than 2 hours: no change
            }

            // Avoid repeated escalation: only apply if this tier not yet applied
            if ((int)$req->escalation_level >= $tier) {
                continue;
            }

            $currentIndex = array_search($req->priority, $priorities, true);
            if ($currentIndex === false) {
                $currentIndex = 1; // default to Medium if unexpected
            }

            // Apply increase by tier steps from original baseline logic: raise to target index
            // but ensure at least increase according to rule and never exceed Urgent.
            $targetIndex = min(3, $currentIndex + $targetIncrease);
            if ($tier === 3) {
                $targetIndex = 3;
            }

            if ($targetIndex <= $currentIndex) {
                // Still mark tier as applied to prevent repeated checks doing nothing forever
                $req->escalation_level = $tier;
                $req->last_escalated_at = $now;
                $req->save();
                continue;
            }

            $oldPriority = $req->priority;
            $newPriority = $priorities[$targetIndex];

            $req->previous_priority = $oldPriority;
            $req->priority = $newPriority;
            $req->escalation_level = $tier;
            $req->last_escalated_at = $now;
            $req->save();
            $updated++;

            AuditTrail::log(
                'Automatic Escalation',
                'Emergency request #' . $req->id . ' escalated from ' . $oldPriority . ' to ' . $newPriority . ' after ' . $hours . ' hour(s).',
                'ReliefRequest',
                $req->id
            );
        }

        $this->info("Escalation check complete. {$updated} request(s) escalated.");
        return self::SUCCESS;
    }
}
