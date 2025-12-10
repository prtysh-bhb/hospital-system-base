<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmation</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            background: #e8f7e7;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        .header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
        }

        .apt-id-box {
            text-align: center;
            background: #e8f3ff;
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
            margin-top: 10px;
        }

        .apt-id-box .title {
            font-size: 13px;
            color: #444;
        }

        .apt-id {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-top: 5px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            margin-top: 20px;
            padding-bottom: 5px;
        }

        .info-box {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 6px;
            margin: 8px 0;
        }

        .info-label {
            font-size: 12px;
            color: #666;
        }

        .info-value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 3px;
        }

        table {
            width: 100%;
            margin-top: 10px;
        }

        td {
            width: 50%;
            vertical-align: top;
            padding: 6px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>

</head>

<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <h2>Appointment Confirmed!</h2>
        </div>

        <!-- Appointment ID -->
        <div class="apt-id-box">
            <div class="title">Your Appointment ID</div>
            <div class="apt-id">{{ $appointment->appointment_number }}</div>
        </div>

        <!-- Appointment Details -->
        <div class="section-title">Appointment Details</div>

        <!-- Doctor Information -->
        <h4 style="font-size:14px; color:#666; margin-top:15px;">Doctor Information</h4>

        <table>
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Name</div>
                        <div class="info-value">
                            {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="info-box">
                        <div class="info-label">Specialty</div>
                        <div class="info-value">
                            {{ $appointment->doctor->doctorProfile->specialty->name ?? 'N/A' }}
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Experience</div>
                        <div class="info-value">
                            {{ $appointment->doctor->doctorProfile->qualification ?? '' }} •
                            {{ $appointment->doctor->doctorProfile->experience_years ?? 0 }} yrs
                        </div>
                    </div>
                </td>

                <td>
                    <div class="info-box">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $appointment->doctor->gender }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Date & Time -->
        <table>
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Date</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                            <br>
                            <span style="font-size:12px;">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l') }}
                            </span>
                        </div>
                    </div>
                </td>

                <td>
                    <div class="info-box">
                        <div class="info-label">Time</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                            <div style="font-size:12px;">
                                {{ $appointment->duration_minutes }} min slot
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Patient Information -->
        <h4 style="font-size:14px; color:#666; margin-top:15px;">Patient Information</h4>

        <table>
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Name</div>
                        <div class="info-value">
                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="info-box">
                        <div class="info-label">Mobile</div>
                        <div class="info-value">{{ $appointment->patient->phone }}</div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Age</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($appointment->patient->date_of_birth)->age ?? 'N/A' }} yrs
                        </div>
                    </div>
                </td>

                <td>
                    <div class="info-box">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $appointment->patient->gender }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Reason -->
        <div class="section-title" style="margin-top:25px;">Reason for Visit</div>

        <div class="info-box" style="font-size:14px;">
            {{ $appointment->reason_for_visit }}
        </div>

        <!-- Fee -->
        <div class="info-box" style="margin-top:20px; display:flex; justify-content:space-between;">
            <div>
                <div class="info-label">Appointment Type</div>
                <div class="info-value">{{ $appointment->appointment_type }}</div>
            </div>
            <div style="text-align:right;">
                <div class="info-label">Consultation Fee</div>
                <div class="info-value" style="color:#007bff; font-size:18px;">
                    ₹{{ $appointment->doctor->doctorProfile->consultation_fee }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is a computer-generated confirmation. No signature required.<br>
            City General Hospital • 123 Medical Center Drive • contact@cityhospital.com
        </div>

    </div>
</body>

</html>
