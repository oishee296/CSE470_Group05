<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Donation;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTrail::query();

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        if ($request->filled('related_type')) {
            $query->where('related_type', $request->related_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $actionTypes = AuditTrail::select('action_type')->distinct()->orderBy('action_type')->pluck('action_type');
        $relatedTypes = AuditTrail::select('related_type')->whereNotNull('related_type')->distinct()->orderBy('related_type')->pluck('related_type');

        $audits = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.audit_report', compact('audits', 'actionTypes', 'relatedTypes'));
    }

    public function show(AuditTrail $auditTrail)
    {
        return view('admin.audit_show', compact('auditTrail'));
    }

    public function donations()
    {
        $donations = Donation::orderByDesc('donation_date')->get();
        return view('admin.donations', compact('donations'));
    }

    public function storeDonation(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'donation_type' => 'required|string|max:255',
            'resource_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'donation_date' => 'required|date',
            'status' => 'required|in:Received,Allocated,Dispatched,Delivered',
        ]);

        $donation = Donation::create($validated);

        $action = match ($validated['status']) {
            'Received' => 'Donation Received',
            'Allocated' => 'Resources Allocated',
            'Dispatched' => 'Relief Goods Dispatched',
            'Delivered' => 'Recipient Confirmed Delivery',
            default => 'Donation Recorded',
        };

        AuditTrail::log(
            $action,
            $action . ': ' . $validated['resource_name'] . ' (' . $validated['quantity'] . ') from ' . $validated['donor_name'] . ' as ' . $validated['donation_type'] . '.',
            'Donation',
            $donation->id,
            $validated['quantity']
        );

        return redirect()->route('donations.index')->with('success', 'Donation/operation recorded and audit log created.');
    }
}
