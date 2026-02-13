<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Staff Financial Report</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11pt; margin: 0; padding: 20px; color: #000; line-height: 1.4; background-color: #f4f6f9; }
        @page { size: A4; margin: 1cm; }
        
        /* Added a white background wrapper for the web view so it looks like a paper page */
        #report-content { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { font-size: 18pt; font-weight: bold; text-transform: uppercase; }
        .sub-header { font-size: 12pt; font-style: italic; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; word-wrap: break-word; }
        
        .col-sn { width: 35px; text-align: center; }
        .col-staff-no { width: 80px; }
        .col-name { width: auto; }
        .col-money { width: 90px; text-align: right; }
        .col-sign { width: 110px; }
        
        .dept-header { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; padding: 10px; }
        .subtotal-row { background-color: #f9f9f9; font-weight: bold; font-style: italic; }
        
        .no-print { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; max-width: 1000px; margin: 0 auto 20px auto; }
        @media print { 
            .no-print { display: none !important; } 
            body { background-color: white; padding: 0; }
            #report-content { padding: 0; box-shadow: none; max-width: 100%; margin: 0; }
        }
        
        /* Button Styles */
        .btn { padding: 8px 15px; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-family: sans-serif; transition: 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-blue { background: #192C57; color: white; }
        .btn-gold { background: #CEAA0C; color: #192C57; }
        .btn-green { background: #198754; color: white; }
        .btn-outline { border: 2px solid #192C57; color: #192C57; background: transparent; }

        .sig-container { margin-top: 50px; display: table; width: 100%; }
        .sig-box { display: table-cell; width: 33%; text-align: center; padding: 0 15px; }
        .sig-line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-weight: bold; font-size: 10pt; }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                ⬅️ Back to Dashboard
            </a>
        </div>

        <form action="{{ route('admin.reports.daily') }}" method="GET" style="display: flex; gap: 10px; align-items: center; margin: 0;">
            <label for="date" style="font-family: sans-serif; font-weight: bold; color: #192C57;">Select Date:</label>
            <input type="date" name="date" id="date" value="{{ $today->format('Y-m-d') }}" style="padding: 6px; border: 1px solid #ccc; border-radius: 5px; outline: none;">
            <button type="submit" class="btn btn-gold">
                View Report
            </button>
        </form>

        <div style="display: flex; gap: 10px;">
            <button onclick="downloadPDF()" class="btn btn-green">
                📥 DOWNLOAD PDF
            </button>
            <button onclick="window.print()" class="btn btn-blue">
                🖨️ PRINT REPORT
            </button>
        </div>
    </div>

    <div id="report-content">
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
                @php 
                    $globalCount = 1; 
                    $grandTotal = 0; 
                @endphp
                
                @foreach($departments as $dept)
                    @if($dept->staff->count() > 0)
                        <tr>
                            <td colspan="7" class="dept-header">{{ $dept->name }}</td>
                        </tr>

                        @php $deptTotal = 0; @endphp

                        @foreach($dept->staff as $staff)
                            @php 
                                $startingAllocation = $staff->hasRole('admin') ? 500 : 200;
                                $rawUsed = $staff->orders_sum_wallet_paid ?? 0;
                                $cappedUsed = min($rawUsed, $startingAllocation); 
                                $balance = $startingAllocation - $cappedUsed;
                                
                                $deptTotal += $cappedUsed;
                                $grandTotal += $cappedUsed;
                            @endphp
                            <tr>
                                <td class="col-sn">{{ $globalCount++ }}</td>
                                <td>{{ $staff->staff_number ?? '-' }}</td>
                                <td>{{ $staff->name }}</td>
                                <td class="col-money">{{ number_format($startingAllocation) }}</td>
                                <td class="col-money" style="{{ $cappedUsed > 0 ? 'font-weight:bold;' : 'color:#ccc;' }}">
                                    {{ $cappedUsed > 0 ? number_format($cappedUsed) : '-' }}
                                </td>
                                <td class="col-money">{{ number_format($balance) }}</td>
                                <td></td> 
                            </tr>
                        @endforeach
                        
                        <tr class="subtotal-row">
                            <td colspan="4" style="text-align: right;">{{ $dept->name }} Subtotal:</td>
                            <td class="col-money" style="color: #192C57;">KES {{ number_format($deptTotal) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>

            <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                <tr>
                    <td colspan="4" style="text-align: right; padding: 10px; font-size: 12pt;">GRAND TOTAL EXPENDITURE:</td>
                    <td class="col-money" style="border-bottom: 3px double #000; font-size: 14pt; color: #192C57;">
                        KES {{ number_format($grandTotal) }}
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
            <p style="margin: 2px 0;"><strong>Generated By:</strong> {{ Auth::user()->name ?? 'Admin' }}</p>
            <p style="margin: 2px 0;"><strong>System Date:</strong> {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>
    </div> 

    <script>
        function downloadPDF() {
            const element = document.getElementById('report-content');
            const filename = 'KCA_Financial_Report_{{ $today->format("Y_m_d") }}.pdf';
            
            const opt = {
                // [Top, Right, Bottom, Left] - Added 15mm to the left side for safety
                margin:       [10, 10, 10, 15], 
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 2, 
                    windowWidth: 1200, // Slightly wider to ensure no squishing
                    scrollX: 0,        // Forces snapshot to start at the absolute left edge
                    scrollY: 0 
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>