<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Disaster;
use App\Models\ReliefRequest;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EscalationController extends Controller
{
    public function index(Request $request)
    {
        $disasters = Disaster::orderBy('name')->get();
        $zones = Zone::when($request->disaster_id, fn($q) => $q->where('disaster_id', $request->disaster_id))
            ->orderBy('area_name')->get();

        $requests = ReliefRequest::with(['disaster', 'zone'])
            ->whereNotNull('request_type')
            ->when($request->disaster_id, fn($q) => $q->where('disaster_id', $request->disaster_id))
            ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->orderBy('created_at')
            ->get()
            ->map(function ($req) {
                $end = $req->resolved_at ? Carbon::parse($req->resolved_at) : now();
                $req->waiting_minutes = Carbon::parse($req->created_at)->diffInMinutes($end);
                return $req;
            });

        return view('admin.escalations', compact('requests', 'disasters', 'zones'));
    }

    public function run()
    {
        Artisan::call('requests:check-escalations');
        $output = trim(Artisan::output());
        AuditTrail::log('Escalation Check Run', 'Ran escalation check. ' . $output, 'System', null);
        return redirect()->route('escalations.index')->with('success', 'Escalation check completed. ' . $output);
    }

    public function updatePriority(Request $request, ReliefRequest $reliefRequest)
    {
        if (!$reliefRequest->request_type) {
            return back()->with('error', 'Only emergency requests can be escalated from this page.');
        }

        $validated = $request->validate([
            'priority' => 'required|in:Low,Medium,High,Urgent',
        ]);

        $old = $reliefRequest->priority;
        $new = $validated['priority'];

        if ($old !== $new) {
            $reliefRequest->previous_priority = $old;
            $reliefRequest->priority = $new;
            $reliefRequest->save();

            AuditTrail::log(
                'Manual Priority Update',
                'Emergency request #' . $reliefRequest->id . ' priority changed from ' . $old . ' to ' . $new,
                'ReliefRequest',
                $reliefRequest->id
            );
        }

        return back()->with('success', 'Priority updated successfully.');
    }
}
