<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt - {{ $donation->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 30px; font-family: Arial, sans-serif; background: #f4f6f8; color: #253041; }
        .receipt { max-width: 800px; margin: 0 auto; padding: 35px; background: white; border: 1px solid #dce2e8; border-radius: 10px; }
        .header { display: flex; justify-content: space-between; gap: 15px; flex-wrap: wrap; border-bottom: 2px solid #176b50; padding-bottom: 18px; }
        h1 { margin: 0 0 5px; color: #17365d; }
        h2 { color: #176b50; font-size: 18px; }
        .muted { color: #687385; }
        .receipt-number { text-align: right; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 25px; margin: 25px 0; }
        .detail-label { display: block; margin-bottom: 4px; color: #687385; font-size: 12px; text-transform: uppercase; }
        .detail-value { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0 25px; }
        th, td { padding: 12px; border: 1px solid #dce2e8; text-align: left; }
        th { background: #edf3f7; color: #17365d; }
        .note { padding: 13px; background: #f4f6f8; border-radius: 6px; }
        .footer { margin-top: 35px; padding-top: 15px; border-top: 1px solid #dce2e8; color: #687385; font-size: 13px; }
        .actions { max-width: 800px; margin: 18px auto 0; display: flex; gap: 10px; }
        .button { display: inline-block; padding: 11px 16px; border: 0; border-radius: 6px; background: #176b50; color: white; text-decoration: none; cursor: pointer; font-weight: bold; }
        .secondary { background: #526579; }
        @media (max-width: 600px) {
            body { padding: 12px; }
            .receipt { padding: 20px; }
            .details { grid-template-columns: 1fr; }
            .receipt-number { text-align: left; }
        }
        @media print {
            body { padding: 0; background: white; }
            .receipt { max-width: none; border: 0; border-radius: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="header">
        <div>
            <h1>SHARE</h1>
            <div class="muted">Systematic Hazard Assessment &amp; Relief Engine</div>
            <h2>Material Donation Receipt</h2>
        </div>
        <div class="receipt-number">
            <strong>Receipt Number</strong><br>
            {{ $donation->receipt_number }}<br><br>
            <span class="muted">Registered on {{ $donation->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    <div class="details">
        <div>
            <span class="detail-label">Donor Name</span>
            <span class="detail-value">{{ $donation->donor_name }}</span>
        </div>
        <div>
            <span class="detail-label">Donation Date</span>
            <span class="detail-value">{{ $donation->donation_date->format('d M Y') }}</span>
        </div>
        <div>
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $donation->donor_email ?: 'Not provided' }}</span>
        </div>
        <div>
            <span class="detail-label">Phone</span>
            <span class="detail-value">{{ $donation->donor_phone ?: 'Not provided' }}</span>
        </div>
        <div style="grid-column: 1 / -1;">
            <span class="detail-label">Drop-off Location</span>
            <span class="detail-value">{{ $donation->drop_off_location }}</span>
        </div>
    </div>

    <h2>Items Received</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($donation->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($donation->notes)
        <div class="note">
            <strong>Additional Notes:</strong><br>
            {{ $donation->notes }}
        </div>
    @endif

    <div class="footer">
        This receipt confirms that the material items listed above have been registered in the SHARE donation tracking system.
        It is an in-kind donation receipt and does not represent a cash payment or a tax certificate.
    </div>
</div>

<div class="actions">
    <button class="button" type="button" onclick="window.print()">Print Receipt</button>
    <a class="button secondary" href="{{ route('donations.index') }}">Back to Donation Tracking</a>
</div>
</body>
</html>
