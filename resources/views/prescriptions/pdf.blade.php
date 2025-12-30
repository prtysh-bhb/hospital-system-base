<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $prescription->prescription_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #0ea5e9;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-right {
            text-align: right;
        }

        .info-box {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .info-box h3 {
            font-size: 13px;
            color: #0ea5e9;
            margin-bottom: 5px;
        }

        .info-box p {
            font-size: 11px;
            margin: 2px 0;
        }

        .section-title {
            background-color: #0ea5e9;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .diagnosis-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-bottom: 15px;
        }

        .medications-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .medications-table th {
            background-color: #e0f2fe;
            color: #075985;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #bae6fd;
        }

        .medications-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .medications-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .instructions-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .instructions-box h4 {
            color: #0369a1;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .follow-up-box {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 10px;
            margin-bottom: 15px;
        }

        .follow-up-box strong {
            color: #92400e;
        }

        .notes-box {
            background-color: #f1f5f9;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }

        .prescription-footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Hospital Management System</h1>
        <p>Medical Prescription</p>
        <p style="margin-top: 5px;"><strong>Prescription #:</strong> {{ $prescription->prescription_number }}</p>
    </div>

    <div class="info-section">
        <div class="info-left">
            <div class="info-box">
                <h3>Doctor Information</h3>
                <p><strong>Name:</strong> Dr. {{ $doctor->full_name }}</p>
                @if ($doctorProfile && $doctorProfile->specialty)
                    <p><strong>Specialty:</strong> {{ $doctorProfile->specialty->name }}</p>
                @endif
                @if ($doctorProfile && $doctorProfile->qualification)
                    <p><strong>Qualification:</strong> {{ $doctorProfile->qualification }}</p>
                @endif
                @if ($doctorProfile && $doctorProfile->license_number)
                    <p><strong>License:</strong> {{ $doctorProfile->license_number }}</p>
                @endif
            </div>
        </div>

        <div class="info-right">
            <div class="info-box">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> {{ $patient->full_name }}</p>
                <p><strong>Email:</strong> {{ $patient->email }}</p>
                @if ($patient->phone)
                    <p><strong>Phone:</strong> {{ $patient->phone }}</p>
                @endif
                <p><strong>Date:</strong> {{ $date }}</p>
            </div>
        </div>
    </div>

    @if ($prescription->diagnosis)
        <div class="section-title">Diagnosis</div>
        <div class="diagnosis-box">
            <p>{{ $prescription->diagnosis }}</p>
        </div>
    @endif

    <div class="section-title">Prescribed Medications</div>
    @if ($prescription->medications && count($prescription->medications) > 0)
        <table class="medications-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Medication Name</th>
                    <th style="width: 20%;">Dosage</th>
                    <th style="width: 25%;">Frequency</th>
                    <th style="width: 15%;">Duration</th>
                    <th style="width: 10%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->medications as $medication)
                    <tr>
                        <td><strong>{{ $medication['name'] ?? 'N/A' }}</strong></td>
                        <td>{{ $medication['dosage'] ?? '-' }}</td>
                        <td>{{ $medication['frequency'] ?? '-' }}</td>
                        <td>{{ $medication['duration'] ?? '-' }}</td>
                        <td>{{ $medication['quantity'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #666; font-style: italic; margin-bottom: 15px;">No medications prescribed.</p>
    @endif

    @if ($prescription->instructions)
        <div class="section-title">Instructions</div>
        <div class="instructions-box">
            <p>{{ $prescription->instructions }}</p>
        </div>
    @endif

    @if ($prescription->follow_up_date)
        <div class="follow-up-box">
            <strong>Follow-up Appointment:</strong>
            {{ Carbon\Carbon::parse($prescription->follow_up_date)->format('F j, Y') }}
        </div>
    @endif

    @if ($prescription->notes)
        <div class="section-title">Additional Notes</div>
        <div class="notes-box">
            {{ $prescription->notes }}
        </div>
    @endif

    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Doctor's Signature</strong>
                    <br>Dr. {{ $doctor->full_name }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Date</strong>
                    <br>{{ $date }}
                </div>
            </div>
        </div>
    </div>
    <div class="prescription-footer">
        <p>This is a computer-generated prescription. Please consult your doctor for any clarifications.</p>
        <p style="margin-top: 5px;">For emergencies or queries, please contact the hospital immediately.</p>
    </div>
</body>
</html>