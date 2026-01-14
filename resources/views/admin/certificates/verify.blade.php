<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - BRA</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .verified { color: #28a745; font-size: 24pt; font-weight: bold; text-align: center; margin: 20px 0; }
        .details { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .detail-row { margin: 10px 0; display: flex; }
        .label { font-weight: bold; width: 150px; color: #1a4a8e; }
        .value { flex: 1; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12pt; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #1a4a8e; text-align: center;">Certificate Verification</h1>
        <div class="verified">✓ CERTIFICATE VERIFIED</div>

        <div class="details">
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
                <span class="value" style="color: #28a745; font-weight: bold;">VALID</span>
            </div>
        </div>

        <div class="footer">
            This certificate was issued by Banaadir Regional Administration<br>
            Waaxda Dhismo Wadaagga • Apartment Department
        </div>
    </div>
</body>
</html>
