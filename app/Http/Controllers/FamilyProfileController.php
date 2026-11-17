<?php

namespace App\Http\Controllers;

use App\Models\FamilyProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamilyProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->familyProfile;
        $regional = $profile ? FamilyProfile::regionalSummary($profile->area) : null;

        return view('profile.family', [
            'profile' => $profile,
            'regional' => $regional,
            'areas' => FamilyProfile::AREAS,
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'area' => ['required', Rule::in(FamilyProfile::AREAS)],
            'household_size' => ['required', 'integer', 'min:1', 'max:50'],
            'infants_count' => ['required', 'integer', 'min:0', 'lte:household_size'],
            'elderly_count' => ['required', 'integer', 'min:0', 'lte:household_size'],
            'injured_count' => ['required', 'integer', 'min:0', 'lte:household_size'],
        ]);

        // Business rule: infants and elderly are different people, so they can't exceed the household.
        if ($data['infants_count'] + $data['elderly_count'] > $data['household_size']) {
            return back()->withInput()->withErrors([
                'household_size' => 'Infants + elderly cannot be more than the household size.',
            ]);
        }

        // Creates the profile the first time, updates it afterwards.
        $request->user()->familyProfile()->updateOrCreate([], $data);

        return redirect()->route('family.show')->with('success', 'Family profile saved.');
    }

    public function destroy(Request $request)
    {
        $request->user()->familyProfile()?->delete();

        return redirect()->route('family.show')->with('success', 'Family profile deleted.');
    }
}