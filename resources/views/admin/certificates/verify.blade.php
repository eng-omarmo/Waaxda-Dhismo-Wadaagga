<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - BRA</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 30px auto; background: white; padding: 24px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .status { font-size: 22pt; font-weight: 700; text-align: center; margin: 10px 0; }
        .valid { color: #28a745; }
        .invalid { color: #dc3545; }
        .details { background: #f8f9fa; padding: 16px; border-radius: 5px; margin: 16px 0; }
        .detail-row { margin: 8px 0; display: flex; flex-wrap: wrap; }
        .label { font-weight: bold; min-width: 160px; color: #1a4a8e; }
        .value { flex: 1; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12pt; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #1a4a8e; text-align: center;">Certificate Verification</h1>
        @if($valid)
        <div class="status valid" role="status" aria-live="polite">✓ CERTIFICATE VERIFIED</div>
        @else
        <div class="status invalid" role="alert" aria-live="assertive">✗ INVALID CERTIFICATE</div>
        @endif

        <div class="details">
            @if($certificate)
                <div class="detail-row">
                    <span class="label">Certificate ID:</span>
                    <span class="value">{{ $certificate->certificate_uid }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Recipient:</span>
                    <span class="value">{{ $certificate->issued_to }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Service:</span>
                    <span class="value">{{ $certificate->service->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Issued Date:</span>
                    <span class="value">{{ $certificate->issued_at->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value" style="{{ $valid ? 'color:#28a745;font-weight:bold;' : 'color:#dc3545;font-weight:bold;' }}">{{ $valid ? 'VALID' : 'INVALID' }}</span>
                </div>
            @else
                <div class="detail-row">
                    <span class="label">Certificate ID:</span>
                    <span class="value">{{ $uid }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value" style="color:#dc3545;font-weight:bold;">INVALID</span>
                </div>
            @endif
        </div>

        <div class="footer">
            This certificate was issued by Banaadir Regional Administration<br>
            Waaxda Dhismo Wadaagga • Apartment Department
        </div>
    </div>
</body>
</html>
