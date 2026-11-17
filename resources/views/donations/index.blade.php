<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Tracking - SHARE</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 30px; font-family: Arial, sans-serif; background: #f4f6f8; color: #253041; }
        .container { max-width: 1100px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 22px; }
        h1 { margin: 0 0 6px; color: #17365d; }
        .muted { color: #687385; }
        .button { display: inline-block; padding: 11px 16px; border: 0; border-radius: 6px; background: #176b50; color: white; text-decoration: none; cursor: pointer; font-weight: bold; }
        .button:hover { background: #10543e; }
        .panel { background: white; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.06); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 750px; }
        th, td { padding: 13px 11px; border-bottom: 1px solid #e4e8ed; text-align: left; vertical-align: top; }
        th { background: #edf3f7; color: #17365d; }
        ul { margin: 0; padding-left: 18px; }
        .status { display: inline-block; padding: 5px 9px; background: #e6f4ed; color: #176b50; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .alert { margin-bottom: 16px; padding: 12px; background: #e6f4ed; color: #176b50; border-radius: 6px; }
        .empty { padding: 30px; text-align: center; color: #687385; }
        .pagination { margin-top: 18px; }
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <div>
            <h1>Material Donation Tracking</h1>
            <div class="muted">SHARE - Supply Donation Logistics (R6)</div>
        </div>
        <a class="button" href="{{ route('donations.create') }}">+ Register a Donation</a>
    </div>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if ($donations->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Receipt No.</th>
                        <th>Donor</th>
                        <th>Donation Date</th>
                        <th>Drop-off Location</th>
                        <th>Items Received</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donations as $donation)
                        <tr>
                            <td>{{ $donation->receipt_number }}</td>
                            <td>{{ $donation->donor_name }}</td>
                            <td>{{ $donation->donation_date->format('d M Y') }}</td>
                            <td>{{ $donation->drop_off_location }}</td>
                            <td>
                                <ul>
                                    @foreach ($donation->items as $item)
                                        <li>{{ $item->item_name }} - {{ $item->quantity }} {{ $item->unit }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td><span class="status">Registered</span></td>
                            <td><a href="{{ route('donations.receipt', $donation->id) }}">View / Print</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $donations->links() }}</div>
        @else
            <div class="empty">
                <h3>No donations registered yet</h3>
                <p>Use the button above to register the first material donation.</p>
            </div>
        @endif
    </div>
</div>
</body>
</html>
