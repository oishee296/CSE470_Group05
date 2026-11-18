<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Disaster;
use App\Models\DutyAssignment;
use App\Models\VolunteerProfile;
use App\Models\Zone;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        $volunteers = VolunteerProfile::withCount('dutyAssignments')->latest()->get();
        return view('volunteers.index', compact('volunteers'));
    }

    public function vetting()
    {
        $volunteers = VolunteerProfile::orderByRaw("FIELD(registration_status,'Pending','Approved','Rejected')")->latest()->get();
        return view('volunteers.vetting', compact('volunteers'));
    }

    public function show(VolunteerProfile $volunteer)
    {
        $volunteer->load('dutyAssignments.disaster', 'dutyAssignments.zone');
        $activeDisasters = Disaster::where('status', 'Active')->with('zones')->orderBy('name')->get();
        return view('volunteers.show', compact('volunteer', 'activeDisasters'));
    }

    public function approve(VolunteerProfile $volunteer)
    {
        $volunteer->update(['registration_status' => 'Approved']);
        AuditTrail::log('Volunteer Approved', 'Approved volunteer: ' . $volunteer->full_name, 'VolunteerProfile', $volunteer->id);
        return back()->with('success', 'Volunteer approved successfully.');
    }

    public function reject(VolunteerProfile $volunteer)
    {
        $volunteer->update(['registration_status' => 'Rejected']);
        AuditTrail::log('Volunteer Rejected', 'Rejected volunteer: ' . $volunteer->full_name, 'VolunteerProfile', $volunteer->id);
        return back()->with('success', 'Volunteer rejected.');
    }

    public function updateCertification(Request $request, VolunteerProfile $volunteer)
    {
        $validated = $request->validate([
            'certification_status' => 'required|in:Pending,Verified,Rejected',
        ]);

        $volunteer->update($validated);
        AuditTrail::log('Volunteer Certification Updated', 'Certification for ' . $volunteer->full_name . ' set to ' . $validated['certification_status'], 'VolunteerProfile', $volunteer->id);

        return back()->with('success', 'Certification status updated.');
    }

    public function roster()
    {
        $assignments = DutyAssignment::with(['volunteerProfile', 'disaster', 'zone'])->latest()->get();
        $approvedVolunteers = VolunteerProfile::where('registration_status', 'Approved')->orderBy('full_name')->get();
        $activeDisasters = Disaster::where('status', 'Active')->with('zones')->orderBy('name')->get();
        return view('volunteers.roster', compact('assignments', 'approvedVolunteers', 'activeDisasters'));
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'volunteer_profile_id' => 'required|exists:volunteer_profiles,id',
            'zone_id' => 'required|exists:zones,id',
            'assigned_responsibility' => 'required|string|max:255',
            'assignment_date' => 'required|date',
            'assignment_status' => 'required|in:Assigned,Active,Completed,Cancelled',
        ]);

        $volunteer = VolunteerProfile::findOrFail($validated['volunteer_profile_id']);
        if ($volunteer->registration_status !== 'Approved') {
            return back()->withErrors(['volunteer_profile_id' => 'Only approved volunteers can be assigned.'])->withInput();
        }

        $zone = Zone::with('disaster')->findOrFail($validated['zone_id']);
        if (!$zone->disaster || $zone->disaster->status !== 'Active') {
            return back()->withErrors(['zone_id' => 'Volunteer cannot be assigned to a zone belonging to an inactive disaster.'])->withInput();
        }

        $validated['disaster_id'] = $zone->disaster_id;
        $assignment = DutyAssignment::create($validated);

        AuditTrail::log('Volunteer Assigned', 'Assigned ' . $volunteer->full_name . ' to zone ' . $zone->area_name . ' (' . $zone->disaster->name . ')', 'DutyAssignment', $assignment->id);

        return redirect()->route('volunteers.roster')->with('success', 'Volunteer assigned successfully.');
    }

    public function updateAssignment(Request $request, DutyAssignment $assignment)
    {
        $validated = $request->validate([
            'assigned_responsibility' => 'required|string|max:255',
            'assignment_status' => 'required|in:Assigned,Active,Completed,Cancelled',
        ]);

        $assignment->update($validated);
        AuditTrail::log('Assignment Updated', 'Updated assignment #' . $assignment->id . ' status/responsibility', 'DutyAssignment', $assignment->id);

        return back()->with('success', 'Assignment updated successfully.');
    }
}
