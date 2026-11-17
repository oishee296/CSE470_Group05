<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Donation - SHARE</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 30px; font-family: Arial, sans-serif; background: #f4f6f8; color: #253041; }
        .container { max-width: 850px; margin: 0 auto; }
        h1 { margin-top: 0; color: #17365d; }
        .muted { color: #687385; margin-bottom: 20px; }
        .panel { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, textarea { width: 100%; padding: 11px; border: 1px solid #cbd3dc; border-radius: 6px; font-size: 15px; }
        textarea { min-height: 85px; resize: vertical; }
        .section-title { margin: 24px 0 12px; padding-bottom: 8px; border-bottom: 1px solid #e4e8ed; color: #17365d; }
        .item-row { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 12px; }
        .item-row .field { margin: 0; }
        .button { display: inline-block; padding: 11px 16px; border: 0; border-radius: 6px; background: #176b50; color: white; text-decoration: none; cursor: pointer; font-weight: bold; }
        .button:hover { background: #10543e; }
        .secondary { background: #526579; }
        .secondary:hover { background: #3d4d5e; }
        .remove { background: #a63d40; padding: 10px 12px; }
        .remove:hover { background: #842d30; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
        .errors { padding: 12px 18px; margin-bottom: 18px; background: #fff0f0; color: #9b2525; border-radius: 6px; }
        .hint { color: #687385; font-size: 13px; margin-top: 5px; }
        @media (max-width: 650px) {
            body { padding: 15px; }
            .grid { grid-template-columns: 1fr; gap: 0; }
            .item-row { grid-template-columns: 1fr 1fr; }
            .item-row .name-field { grid-column: 1 / -1; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Register a Material Donation</h1>
    <div class="muted">Enter the donor details and the materials being dropped off.</div>

    <div class="panel">
        @if ($errors->any())
            <div class="errors">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('donations.store') }}">
            @csrf

            <h3 class="section-title">Donor Information</h3>
            <div class="field">
                <label for="donor_name">Donor Name *</label>
                <input id="donor_name" name="donor_name" type="text" value="{{ old('donor_name') }}" required>
            </div>

            <div class="grid">
                <div class="field">
                    <label for="donor_email">Email (optional)</label>
                    <input id="donor_email" name="donor_email" type="email" value="{{ old('donor_email') }}">
                </div>
                <div class="field">
                    <label for="donor_phone">Phone (optional)</label>
                    <input id="donor_phone" name="donor_phone" type="text" value="{{ old('donor_phone') }}">
                </div>
            </div>

            <div class="grid">
                <div class="field">
                    <label for="drop_off_location">Drop-off Location *</label>
                    <input id="drop_off_location" name="drop_off_location" type="text" value="{{ old('drop_off_location') }}" placeholder="Example: Mirpur Relief Collection Point" required>
                </div>
                <div class="field">
                    <label for="donation_date">Donation Date *</label>
                    <input id="donation_date" name="donation_date" type="date" value="{{ old('donation_date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <h3 class="section-title">Donated Items</h3>
            <p class="hint">Add each type of material separately. Example: 20 blankets, 50 water bottles, 10 medical packs.</p>

            @php($oldItems = old('items', [['item_name' => '', 'quantity' => '', 'unit' => 'pieces']]))
            <div id="items-container">
                @foreach ($oldItems as $index => $item)
                    <div class="item-row">
                        <div class="field name-field">
                            <label>Item Name *</label>
                            <input type="text" name="items[{{ $index }}][item_name]" value="{{ $item['item_name'] ?? '' }}" placeholder="Blankets / Clean Water / Medical Packs" required>
                        </div>
                        <div class="field">
                            <label>Quantity *</label>
                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}" min="1" step="1" required>
                        </div>
                        <div class="field">
                            <label>Unit *</label>
                            <input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'pieces' }}" placeholder="pieces / packs / liters" required>
                        </div>
                        <button class="button remove" type="button" onclick="removeItemRow(this)">Remove</button>
                    </div>
                @endforeach
            </div>

            <button class="button secondary" type="button" onclick="addItemRow()">+ Add Another Item</button>

            <div class="field" style="margin-top: 22px;">
                <label for="notes">Additional Notes (optional)</label>
                <textarea id="notes" name="notes" placeholder="Any useful details about the donation">{{ old('notes') }}</textarea>
            </div>

            <div class="actions">
                <button class="button" type="submit">Register Donation and Generate Receipt</button>
                <a class="button secondary" href="{{ route('donations.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Continue numbering from the rows that were already displayed by Blade.
    let itemIndex = {{ count($oldItems) }};

    function addItemRow() {
        const container = document.getElementById('items-container');

        const newRow = `
            <div class="item-row">
                <div class="field name-field">
                    <label>Item Name *</label>
                    <input type="text" name="items[${itemIndex}][item_name]" placeholder="Blankets / Clean Water / Medical Packs" required>
                </div>
                <div class="field">
                    <label>Quantity *</label>
                    <input type="number" name="items[${itemIndex}][quantity]" min="1" step="1" required>
                </div>
                <div class="field">
                    <label>Unit *</label>
                    <input type="text" name="items[${itemIndex}][unit]" placeholder="pieces / packs / liters" required>
                </div>
                <button class="button remove" type="button" onclick="removeItemRow(this)">Remove</button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', newRow);
        itemIndex++;
    }

    function removeItemRow(button) {
        button.closest('.item-row').remove();
    }
</script>
</body>
</html>
