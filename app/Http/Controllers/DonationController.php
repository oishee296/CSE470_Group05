<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    // Show all registered donations. This is the R6 tracking lane.
    public function index()
    {
        $donations = Donation::with('items')
            ->latest()
            ->paginate(10);

        return view('donations.index', compact('donations'));
    }

    // Show the form used by a humanitarian donor.
    public function create()
    {
        return view('donations.form');
    }

    // Validate and save one donation with its item rows.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_phone' => 'nullable|string|max:30',
            'drop_off_location' => 'required|string|max:255',
            'donation_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
        ]);

        // A transaction makes sure the donation and all its items are saved together.
        $donation = DB::transaction(function () use ($validated) {
            $donation = Donation::create([
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'] ?? null,
                'donor_phone' => $validated['donor_phone'] ?? null,
                'drop_off_location' => $validated['drop_off_location'],
                'donation_date' => $validated['donation_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Generate a readable receipt number after Laravel has created the ID.
            $receiptNumber = 'SHARE-R6-' . now()->format('Ymd') . '-' . str_pad(
                (string) $donation->id,
                6,
                '0',
                STR_PAD_LEFT
            );

            $donation->update(['receipt_number' => $receiptNumber]);

            // Save each item under this donation.
            foreach ($validated['items'] as $item) {
                $donation->items()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                ]);
            }

            return $donation;
        });

        return redirect()
            ->route('donations.receipt', $donation->id)
            ->with('success', 'Donation registered successfully.');
    }

    // Show the receipt for one donation.
    public function receipt(Donation $donation)
    {
        $donation->load('items');

        return view('donations.receipt', compact('donation'));
    }
}
