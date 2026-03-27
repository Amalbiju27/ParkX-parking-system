<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkX Receipt - #{{ $id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 10mm;
            background-color: white;
            color: black;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .details {
            font-size: 14px;
            line-height: 1.6;
        }
        .details div {
            display: flex;
            justify-content: space-between;
        }
        .amount-section {
            margin-top: 5mm;
            padding-top: 5mm;
            border-top: 2px dashed #000;
            text-align: center;
        }
        .amount-section h2 {
            margin: 0;
            font-size: 28px;
        }
        .barcode {
            margin-top: 8mm;
            text-align: center;
        }
        .barcode-box {
            border: 2px solid #000;
            padding: 5mm;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 5px;
            display: inline-block;
        }
        .footer {
            margin-top: 10mm;
            text-align: center;
            font-size: 10px;
            font-style: italic;
        }
        @media print {
            body {
                width: 80mm;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>PARKX</h1>
        <p>SMART PARKING SOLUTION</p>
        <p>{{ strtoupper($data->space_name) }}</p>
    </div>

    <div class="details">
        <div><span>DATE:</span> <span>{{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}</span></div>
        <div><span>TIME:</span> <span>{{ \Carbon\Carbon::parse($data->date)->format('h:i A') }}</span></div>
        <div><span>VEHICLE:</span> <span><strong>{{ $data->vehicle_number }}</strong></span></div>
        <div><span>SLOT:</span> <span><strong>{{ $data->slot_number ?? 'GENERAL' }}</strong></span></div>
        <div><span>TYPE:</span> <span>{{ strtoupper($type) }}</span></div>
    </div>

    <div class="amount-section">
        <p>TOTAL AMOUNT PAID</p>
        <h2>₹{{ number_format($data->amount, 2) }}</h2>
    </div>

    <div class="barcode">
        <div class="barcode-box">
            |||| || |||| ||| ||
            <br>
            {{ $type === 'manual' ? 'V-' : 'B-' }}{{ $id }}
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing ParkX!</p>
        <p>Visit again for a seamless experience.</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; font-weight: bold;">CLOSE WINDOW</button>
    </div>
</body>
</html>
