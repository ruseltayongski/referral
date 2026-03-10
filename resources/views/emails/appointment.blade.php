<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 8px;
        }
        
        .header-banner {
            background: linear-gradient(135deg, #4a9b7f 0%, #2d7a63 100%);
            padding: 30px 20px;
            margin: 16px auto 18px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
            border-radius: 6px;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .header-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4a9b7f;
            font-size: 24px;
        }
        
        .header-text {
            text-align: center;
        }
        
        .header-text h1 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .header-text p {
            font-size: 13px;
            opacity: 0.95;
        }
        
        .content-wrapper {
            background-color: white;
            padding: 50px 30px;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            background-color: #fff3cd;
            color: #856404;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-badge.accepted {
            background-color: #18c941;
            color: #e5f1e8;
        }
        
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
            font-size: 14px;
            color: #004085;
            line-height: 1.6;
        }
        
        .content-wrapper h2 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .content-wrapper p {
            color: #555;
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .appointment-details {
            background-color: #f9f9f9;
            border-left: 4px solid #4a9b7f;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #4a9b7f;
            min-width: 120px;
        }
        
        .detail-value {
            color: #333;
            text-align: right;
            flex: 1;
            margin-left: 20px;
        }
        
        .action-buttons {
            margin: 30px 0;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            color: #ffffff !important;
        }

        .btn:link,
        .btn:visited,
        .btn:hover,
        .btn:active {
            color: #ffffff !important;
            text-decoration: none !important;
        }
        
        .btn-primary {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #229954;
            text-decoration: none;
        }
        
        .btn-secondary {
            background-color: #ecf0f1;
            color: #27ae60;
            border: 2px solid #27ae60;
        }
        
        .btn-secondary:hover {
            background-color: #27ae60;
            color: white;
            text-decoration: none;
        }

        .btn-calendar {
            background-color: #4285F4;
            color: white;
        }
        
        .btn-calendar:hover {
            background-color: #357ae8;
            text-decoration: none;
        }
        
        .footer {
            background-color: #f9f9f9;
            padding: 25px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        
        .footer-links {
            margin: 10px 0;
        }
        
        .footer-links a {
            color: #4a9b7f;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }
        
        @media (max-width: 600px) {
            .content-wrapper {
                padding: 30px 20px;
            }
            
            .content-wrapper h2 {
                font-size: 24px;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-value {
                text-align: left;
                margin-left: 0;
                margin-top: 5px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Banner -->
        <div class="header-banner">
            <div class="header-content">
                <div class="header-logo">
                    <img src="https://cvchd7.com/resources/img/doh.png" alt="DOH Logo" style="width: 50px; height: 50px;">
                </div>
                <div class="header-text">
                    <h1>Department Of Health</h1>
                    <p>Central Visayas Center for Health Development</p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content-wrapper">
            @if(data_get($appointment, 'status') === 'pending')
            <span class="status-badge pending"> {{ data_get($appointment, 'status', 'N/A') }} Status</span>
            @elseif(data_get($appointment, 'status') === 'accepted')
            <span class="status-badge accepted"> {{ data_get($appointment, 'status', 'N/A') }} Status</span>
            @endif
            <h2>Telemedicine Appointment Requested</h2>
            @if(data_get($appointment, 'status') === 'pending')
            <p>
                Your telemedicine consultation request has been successfully submitted and is awaiting doctor acceptance. We will notify you once the doctor has accepted your appointment request.
            </p>
            @elseif(data_get($appointment, 'status') === 'accepted')
            <p>
                Your telemedicine consultation request has been accepted by the doctor. Please find your appointment details below and use the provided video link to join the consultation at the scheduled time.
            </p>
            @endif
            
            <!-- Your Request Details -->
            <div class="appointment-details">
                <div class="detail-row">
                    <span class="detail-label"> Patient Name</span>
                    <span class="detail-value">{{ data_get($appointment, 'patient_name', 'N/A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"> Requested Date</span>
                    <span class="detail-value">{{ data_get($appointment, 'date', 'N/A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Requested Time</span>
                    <span class="detail-value">{{ data_get($appointment, 'time', 'N/A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Preferred Doctor</span>
                    <span class="detail-value">{{ data_get($appointment, 'doctor', 'N/A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Facility</span>
                    <span class="detail-value">{{ data_get($appointment, 'facility', 'N/A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Facility Address</span>
                    <span class="detail-value">{{ data_get($appointment, 'address', 'N/A') }}</span>
                </div>
            </div>

            @if(data_get($appointment, 'status') === 'accepted')
            @php
                $dateValue = data_get($appointment, 'date');
                $timeValue = data_get($appointment, 'time');
                $startTimestamp = strtotime(trim($dateValue . ' ' . $timeValue));
                $endTimestamp = $startTimestamp ? strtotime('+1 hour', $startTimestamp) : false;

                $googleCalendarUrl = '#';
                if ($startTimestamp && $endTimestamp) {
                    $calendarStart = gmdate('Ymd\\THis\\Z', $startTimestamp);
                    $calendarEnd = gmdate('Ymd\\THis\\Z', $endTimestamp);
                    $calendarTitle = 'Telemedicine Appointment - ' . data_get($appointment, 'doctor', 'Doctor');
                    $calendarLocation = trim(data_get($appointment, 'facility', '') . ' - ' . data_get($appointment, 'address', ''));
                    $calendarDescription = "Patient: " . data_get($appointment, 'patient_name', 'N/A') . "\n"
                        . "Video Link: " . data_get($appointment, 'video_link', 'N/A') . "\n"
                        . "Status: " . strtoupper(data_get($appointment, 'status', 'N/A'));

                    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                        . '&text=' . rawurlencode($calendarTitle)
                        . '&dates=' . rawurlencode($calendarStart . '/' . $calendarEnd)
                        . '&details=' . rawurlencode($calendarDescription)
                        . '&location=' . rawurlencode($calendarLocation);
                }
            @endphp
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ data_get($appointment, 'video_link', '#') }}" class="btn btn-primary" style="color: #ffffff !important; text-decoration: none;">Join Video Call</a>
            </div>
            <div class="action-buttons">
                <a href="{{ route('appointment.download-ics', data_get($appointment, 'appointment_id')) }}" download="appointment.ics">Download Calendar</a>
            </div>
            <div class="action-buttons">
                <a href="{{ $googleCalendarUrl }}" target="_blank" rel="noopener">Add to Google Calendar</a>
            </div>
            @endif
        <!-- Additional Info -->
        <div class="divider"></div>
            <!-- Info Box -->
            <div class="info-box">
                <strong>What happens next?</strong><br><br>
                Once the doctor accepts your appointment request, you will receive a confirmation email with:<br>
                • Full appointment details<br>
                • Video conference link<br>
                • Option to save the appointment to your calendar
            </div>
        
            <p style="font-size: 13px; color: #666; margin-top: 20px;">
                <strong>Need to change something?</strong><br>
                If you need to modify your request, you can do so from your dashboard before the doctor accepts it. Once accepted, you'll need to reschedule through the confirmed appointment.
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p style="margin-bottom: 15px;">
                Thank you for choosing Department of Health - Central Visayas Center for Health Development
            </p>
            <!-- <div class="footer-links">
                <a href="#">Contact Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms</a>
            </div> -->
            <p style="margin-top: 15px; opacity: 0.8;">
                © 2026 Department of Health. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
