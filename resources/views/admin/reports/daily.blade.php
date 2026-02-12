<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Staff Financial Report</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11pt; margin: 0; padding: 20px; color: #000; line-height: 1.4; }
        
        /* A4 Page Setup */
        @page { size: A4; margin: 1cm; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { font-size: 18pt; font-weight: bold; text-transform: uppercase; }
        .sub-header { font-size: 12pt; font-style: italic; }
        
        /* The Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; word-wrap: break-word; }
        
        /* Column Widths */
        .col-sn { width: 35px; text-align: center; }
        .col-staff-no { width: 80px; }
        .col-name { width: auto; }
        .col-money { width: 90px; text-align: right; }
        .col-sign { width: 110px; }
        
        /* Department Header Row */
        .dept-header { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; padding: 10px; }
        
        /* Buttons (Hide when printing) */
        .no-print { position: fixed; top: 20px; right: 20px; background: #fff; padding: 10px; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0,0,0,0.1); z-index: 1000; }
        @media print { .no-print { display: none; } }

        /* Signature Section */
        .sig-container { margin-top: 50px; display: table; width: 100%; }
        .sig-box { display: table-cell; width: 33%; text-align: center; padding: 0 15px; }
        .sig-line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-weight: bold; font-size: 10pt; }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer; background: #192C57; color: white; border: none; border-radius: 5px;">
            <i class="fas fa-print"></i> 🖨️ PRINT REPORT
        </button>
    </div>

    <div class="header">
        <div class="logo">KCA University Cafeteria</div>
        <div class="sub-header">Daily Staff Financial Report - {{ $today->format('l, d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr style="background: #eee;">
                <th class="col-sn">S/No.</th>
                <th class="col-staff-no">Staff No.</th>
                <th class="col-name">Employee Name</th>
                <th class="col-money">Allocation</th>
                <th class="col-money">Used Today</th>
                <th class="col-money">Balance</th>
                <th class="col-sign">Sign / Remark</th>
            </tr>
        </thead>
        
        <tbody>
            @php $globalCount = 1; @endphp
            
            @foreach($departments as $dept)
                @if($dept->staff->count() > 0)
                    <tr>
                        <td colspan="7" class="dept-header">{{ $dept->name }}</td>
                    </tr>

                    @foreach($dept->staff as $staff)
                        @php 
                            $used = $staff->orders_sum_total_amount ?? 0;
                            $balance = $staff->daily_allocation - $used;
                        @endphp
                        <tr>
                            <td class="col-sn">{{ $globalCount++ }}</td>
                            <td>{{ $staff->staff_number ?? '-' }}</td>
                            <td>{{ $staff->name }}</td>
                            <td class="col-money">{{ number_format($staff->daily_allocation) }}</td>
                            <td class="col-money" style="{{ $used > 0 ? 'font-weight:bold;' : 'color:#ccc;' }}">
                                {{ $used > 0 ? number_format($used) : '-' }}
                            </td>
                            <td class="col-money">{{ number_format($balance) }}</td>
                            <td></td> 
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>

        <tfoot style="background-color: #f8f9fa; font-weight: bold;">
            <tr>
                <td colspan="4" style="text-align: right; padding: 10px;">GRAND TOTAL EXPENDITURE FOR TODAY:</td>
                <td class="col-money" style="border-bottom: 3px double #000; font-size: 13pt;">
                    KES {{ number_format($departments->sum(fn($d) => $d->staff->sum('orders_sum_total_amount'))) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="sig-container">
        <div class="sig-box">
            <div class="sig-line">CAFETERIA ADMINISTRATOR</div>
            <div style="font-size: 9pt;">Signature & Date</div>
        </div>
        <div class="sig-box">
            <div class="sig-line">FINANCE DEPARTMENT</div>
            <div style="font-size: 9pt;">Stamp & Date</div>
        </div>
        <div class="sig-box">
            <div class="sig-line">INTERNAL AUDIT</div>
            <div style="font-size: 9pt;">Review Signature</div>
        </div>
    </div>

    <div style="margin-top: 40px; font-size: 9pt; border-top: 1px dashed #ccc; padding-top: 10px;">
        <p style="margin: 2px 0;"><strong>Generated By:</strong> {{ Auth::user()->name }}</p>
        <p style="margin: 2px 0;"><strong>System Date:</strong> {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

</body>
</html>