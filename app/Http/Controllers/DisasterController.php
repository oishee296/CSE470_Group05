<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Disaster;
use App\Models\Zone;
use Illuminate\Http\Request;

class DisasterController extends Controller
{
    public function index()
    {
        $disasters = Disaster::withCount('zones')->latest()->get();
        return view('admin.disasters.index', compact('disasters'));
    }

    public function create()
    {
        return view('admin.disasters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Completed,Inactive',
        ]);

        $disaster = Disaster::create($validated);
        AuditTrail::log('Disaster Declared', 'Declared disaster: ' . $disaster->name, 'Disaster', $disaster->id);

        return redirect()->route('disasters.index')->with('success', 'Disaster created successfully.');
    }

    public function show(Disaster $disaster)
    {
        $disaster->load('zones');
        return view('admin.disasters.show', compact('disaster'));
    }

    public function edit(Disaster $disaster)
    {
        return view('admin.disasters.edit', compact('disaster'));
    }

    public function update(Request $request, Disaster $disaster)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Completed,Inactive',
        ]);

        $disaster->update($validated);
        AuditTrail::log('Disaster Updated', 'Updated disaster: ' . $disaster->name, 'Disaster', $disaster->id);

        return redirect()->route('disasters.show', $disaster)->with('success', 'Disaster updated successfully.');
    }

    public function destroy(Disaster $disaster)
    {
        $hasDependents = $disaster->zones()->exists()
            || $disaster->reliefRequests()->exists()
            || $disaster->inventories()->exists()
            || $disaster->dutyAssignments()->exists();

        if ($hasDependents) {
            $disaster->update(['status' => 'Inactive']);
            AuditTrail::log('Disaster Inactivated', 'Disaster marked inactive due to dependent records: ' . $disaster->name, 'Disaster', $disaster->id);
            return redirect()->route('disasters.index')->with('error', 'Disaster has dependent records and was marked Inactive instead of deleted.');
        }

        $name = $disaster->name;
        $id = $disaster->id;
        $disaster->delete();
        AuditTrail::log('Disaster Deleted', 'Deleted disaster: ' . $name, 'Disaster', $id);

        return redirect()->route('disasters.index')->with('success', 'Disaster deleted successfully.');
    }

    public function storeZone(Request $request, Disaster $disaster)
    {
        $validated = $request->validate([
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'severity_level' => 'required|in:Low,Medium,High,Critical',
            'priority_tier' => 'required|in:Low,Medium,High,Urgent',
        ]);

        $zone = $disaster->zones()->create($validated);
        AuditTrail::log('Zone Added', 'Added zone ' . $zone->area_name . ' to disaster ' . $disaster->name, 'Zone', $zone->id);

        return redirect()->route('disasters.show', $disaster)->with('success', 'Zone added successfully.');
    }

    public function updateZone(Request $request, Disaster $disaster, Zone $zone)
    {
        if ($zone->disaster_id !== $disaster->id) {
            abort(404);
        }

        $validated = $request->validate([
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'severity_level' => 'required|in:Low,Medium,High,Critical',
            'priority_tier' => 'required|in:Low,Medium,High,Urgent',
        ]);

        $zone->update($validated);
        AuditTrail::log('Zone Updated', 'Updated zone ' . $zone->area_name . ' in disaster ' . $disaster->name, 'Zone', $zone->id);

        return redirect()->route('disasters.show', $disaster)->with('success', 'Zone updated successfully.');
    }

    public function destroyZone(Disaster $disaster, Zone $zone)
    {
        if ($zone->disaster_id !== $disaster->id) {
            abort(404);
        }

        $hasDependents = $zone->reliefRequests()->exists()
            || $zone->inventories()->exists()
            || $zone->dutyAssignments()->exists();

        if ($hasDependents) {
            return redirect()->route('disasters.show', $disaster)->with('error', 'Zone cannot be deleted because it has dependent operational records.');
        }

        $name = $zone->area_name;
        $id = $zone->id;
        $zone->delete();
        AuditTrail::log('Zone Deleted', 'Deleted zone ' . $name . ' from disaster ' . $disaster->name, 'Zone', $id);

        return redirect()->route('disasters.show', $disaster)->with('success', 'Zone deleted successfully.');
    }
}
