<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Staff Consumption Report</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #192C57; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; font-size: 1.2em; background-color: #e8e8e8; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { border-top: 1px solid #000; width: 200px; padding-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Download / Print PDF</button>
    </div>

    <div class="header">
        <div class="logo">PEAKS HOTEL CAFETERIA</div>
        <h3>KCA University - Staff Consumption Report</h3>
        <p>Date: {{ now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Staff Name</th>
                <th>Staff ID</th>
                <th>Department</th>
                <th style="text-align: right;">Wallet Amount (KES)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data['name'] }}</td>
                <td>{{ $data['staff_number'] }}</td>
                <td>{{ $data['department'] }}</td>
                <td style="text-align: right;">{{ number_format($data['total_spent'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTAL CLAIM:</td>
                <td style="text-align: right;">KES {{ number_format($totalClaim, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p>Prepared By (Peaks Hotel):</p>
            <div class="signature-box">Signature & Date</div>
        </div>
        <div>
            <p>Received By (KCA Finance):</p>
            <div class="signature-box">Signature & Date</div>
        </div>
    </div>

</body>
</html>