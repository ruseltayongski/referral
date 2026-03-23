<!doctype html>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #20b2aa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            min-height: 100vh;
        }

        /* === Sidebar Section === */
        .sidebar {
            background: #2c3e50;
            color: #fff;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 25%;
            min-height: 100vh;
        }

        .sidebar-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .logo-icon {
            width: 80px;
            height: 80px;
            background: #20b2aa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .sidebar-description {
            font-size: 13px;
            color: #bdc3c7;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .sidebar-highlight {
            font-size: 12px;
            color: #ecf0f1;
            margin: 10px 0;
            line-height: 1.5;
        }

        .sidebar-login {
            margin-top: auto;
            padding-top: 20px;
        }

        .sidebar-login p {
            font-size: 13px;
            margin-bottom: 12px;
            color: #bdc3c7;
        }

        .btn-login {
            background: transparent;
            border: 2px solid #20b2aa;
            color: #20b2aa;
            padding: 10px 35px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #20b2aa;
            color: #fff;
        }

        .btn-submit:disabled {
            background-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* === Form Section === */
        .form-container {
            background: #fff;
            flex: 1;
            padding: 30px 40px 40px 40px;
            overflow-y: visible;
            min-height: 100vh;
        }

        /* Custom Scrollbar */
        .form-container::-webkit-scrollbar {
            width: 8px;
        }

        .form-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .form-container::-webkit-scrollbar-thumb {
            background: #20b2aa;
            border-radius: 4px;
        }

        .form-container::-webkit-scrollbar-thumb:hover {
            background: #1a8a82;
        }

        .form-content {
            max-width: 100%;
            margin: 0 auto;
        }

        .form-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
            display: block;
        }

        .note {
            color: #999;
            font-weight: 400;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-row.two-col {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            font-size: 13px;
            transition: all 0.3s;
            font-family: inherit;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #20b2aa;
            box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.1);
        }

        select:disabled {
            background-color: #f5f5f5;
            color: #aaa;
            cursor: not-allowed;
            border-color: #e0e0e0;
        }

        /* Input Group for Phone Number */
        .input-group {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 8px;
        }

        .input-group select {
            padding: 10px 8px;
        }

        /* Password Toggle */
        .password-input-group {
            position: relative;
            width: 100%;
        }

        .password-input-group input {
            padding-right: 40px;
        }

        .password-input-group .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            color: #999;
            user-select: none;
        }

        /* Checkbox Group */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 8px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .checkbox-group label {
            font-size: 12px;
            color: #555;
            line-height: 1.4;
            cursor: pointer;
        }

        .checkbox-group a {
            color: #20b2aa;
            text-decoration: none;
        }

        .checkbox-group a:hover {
            text-decoration: underline;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .btn-submit {
            background: #20b2aa;
            color: white;
            padding: 12px 35px;
            font-weight: 700;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-submit .btn-loader {
            display: none;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.5);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-submit.loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit.loading .btn-loader {
            display: inline-block;
        }

        .btn-submit:hover {
            background: #1a8a82;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* === Responsive Breakpoints === */

        /* Large Laptops */
        @media (max-width: 1440px) {
            .sidebar {
                width: 24%;
                padding: 35px 25px;
            }

            .form-container {
                padding: 25px 35px 35px 35px;
            }

            .form-row {
                gap: 16px;
                margin-bottom: 16px;
            }
        }

        /* Standard Laptops */
        @media (max-width: 1280px) {
            .sidebar {
                width: 23%;
                padding: 30px 20px;
            }

            .form-container {
                padding: 25px 30px 30px 30px;
            }

            .form-row {
                gap: 14px;
                margin-bottom: 14px;
            }
        }

        /* Small Laptops / Tablets Landscape */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                height: auto;
                padding: 15px 25px;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
            }

            .sidebar-content {
                flex: 1;
            }

            .sidebar-description {
                display: none;
            }

            .sidebar-highlight {
                display: none;
            }

            .sidebar-login {
                margin-top: 0;
                padding-top: 0;
                text-align: right;
                flex-shrink: 0;
            }

            .sidebar-login p {
                display: none;
            }

            .form-container {
                width: 100%;
                min-height: auto;
                height: auto;
                padding: 20px 30px 30px 30px;
            }

            .form-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
                margin-bottom: 14px;
            }

            .form-row.two-col {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-title {
                font-size: 20px;
                margin-bottom: 15px;
            }
        }

        /* Tablets Portrait */
        @media (max-width: 768px) {
            .sidebar {
                flex-direction: row;
                padding: 15px 20px;
                text-align: left;
            }

            .sidebar-logo {
                font-size: 18px;
            }

            .sidebar .logo-icon {
                width: 30px;
                height: 30px;
                font-size: 16px;
            }

            .sidebar-login {
                text-align: right;
                flex-shrink: 0;
            }

            .form-container {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 12px;
                margin-bottom: 12px;
            }

            .form-row.two-col {
                grid-template-columns: 1fr;
            }

            .input-group {
                grid-template-columns: 90px 1fr;
                gap: 8px;
            }

            .form-title {
                font-size: 18px;
                margin-bottom: 12px;
            }

            .btn-submit {
                width: 100%;
                padding: 12px 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            input,
            select {
                padding: 10px 12px;
                font-size: 16px; /* Prevents iOS zoom on focus */
            }
        }

        /* Mobile Devices */
        @media (max-width: 480px) {
            .sidebar {
                padding: 12px 15px;
            }

            .sidebar-logo {
                font-size: 15px;
                gap: 8px;
            }

            .sidebar .logo-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .form-container {
                padding: 16px 15px 30px 15px;
            }

            .form-title {
                font-size: 16px;
                margin-bottom: 10px;
            }

            input,
            select {
                font-size: 16px; /* Prevents iOS zoom on focus */
                padding: 10px 10px;
            }

            .field-label {
                font-size: 11px;
            }

            .input-group {
                grid-template-columns: 85px 1fr;
            }

            .input-group select {
                font-size: 13px;
                padding: 10px 6px;
            }

            .btn-login {
                padding: 8px 16px;
                font-size: 11px;
                letter-spacing: 0.5px;
            }

            .btn-submit {
                padding: 13px 20px;
                font-size: 13px;
                width: 100%;
            }

            .form-actions {
                flex-direction: column;
                margin-bottom: 10px;
            }

            .checkbox-group label {
                font-size: 11px;
            }

            .form-row {
                gap: 10px;
                margin-bottom: 10px;
            }

            /* Stack password fields vertically with proper spacing */
            .password-input-group + .field-label {
                margin-top: 10px;
            }
        }

        /* Extra small screens */
        @media (max-width: 360px) {
            .sidebar-logo span {
                display: none;
            }

            .sidebar {
                padding: 10px 12px;
            }

            .form-container {
                padding: 14px 12px 24px 12px;
            }
        }

        /* === Custom Alert Styles === */
        .custom-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #20b2aa 0%, #1a8a82 100%);
            color: white;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            text-align: center;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }

        .custom-alert h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        .custom-alert p {
            margin: 10px 0 20px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .custom-alert button {
            background: white;
            color: #20b2aa;
            border: none;
            padding: 10px 30px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .custom-alert button:hover {
            transform: scale(1.05);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Privacy Notice Modal */
        .privacy-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .privacy-modal-overlay.active {
            display: flex;
        }

        .privacy-modal {
            background: #fff;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: privacyFadeIn 0.3s ease-out;
        }

        @keyframes privacyFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .privacy-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .privacy-modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }

        .privacy-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            line-height: 1;
        }

        .privacy-modal-close:hover {
            color: #333;
        }

        .privacy-modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
            font-size: 13px;
            line-height: 1.6;
            color: #444;
        }

        .privacy-modal-body .notice_category {
            font-weight: 700;
            color: #2c3e50;
            margin-top: 15px;
        }

        .privacy-modal-footer {
            padding: 12px 20px;
            border-top: 1px solid #e0e0e0;
            text-align: right;
        }

        .privacy-modal-footer button {
            background: #20b2aa;
            color: #fff;
            border: none;
            padding: 8px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .privacy-modal-footer button:hover {
            background: #1a8a82;
        }
    </style>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="sidebar-logo">
                    <img src="{{ asset('resources/img/doh.png') }}" class="logo-icon" alt="DOH Logo">
                    <span>Telemedicine</span>
                </div>
                <div class="sidebar-description">Department of Health Central Visayas Center for Health and Development</div>
                <div class="sidebar-highlight">Easy Appointment Scheduling, Electronic Medical Records Management & More!</div>
            </div>
            <div class="sidebar-login">
                <p>Already have an account?</p>
                <button class="btn-login" onclick="window.location.href='/referral/login'">LOG IN</button>
            </div>
        </div>
        <div class="form-container">
            <div class="form-content">
                <div class="form-title">Register</div>
                <!-- <form method="POST" action="/register"> -->
                <form id="registerForm">
                    <!-- Credentials -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Email Address</label>
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Username</label>
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Password</label>
                            <div class="password-input-group">
                                <input type="password" name="password" placeholder="Password" required>
                                <span class="toggle-password">👁️</span>
                            </div>
                            <label class="field-label" style="margin-top: 12px;">Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="form-title" style="margin-top: 25px;">Basic Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">First Name</label>
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Middle Name</label>
                            <input type="text" name="middle_name" placeholder="Middle Name">
                        </div>
                        <div class="form-group">
                            <label class="field-label">Last Name</label>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Suffix</label>
                            <input type="text" name="suffix" placeholder="Suffix">
                        </div>
                        <div class="form-group">
                            <label class="field-label">Contact Number</label>
                            <div class="input-group">
                                <select name="country_code">
                                    <option value="+63">🇵🇭 +63</option>
                                </select>
                                <input type="tel" name="contact_number" placeholder="Contact Number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Date of Birth</label>
                            <input type="date" name="birth_date">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Sex</label>
                            <select name="sex">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Civil Status</label>
                            <select name="civil_status">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">PhilHealth Status</label>
                            <select name="philhealth_status">
                                <option value="">Select Status</option>
                                <option value="None">None</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">PhilHealth ID <span class="note">(optional)</span></label>
                            <input type="text" name="philhealth_id" placeholder="If available">
                        </div>
                        <div class="form-group">
                            <label class="field-label">National ID <span class="note">(optional)</span></label>
                            <input type="text" name="national_id" placeholder="If available">
                        </div>
                        <div class="form-group">
                            <label class="field-label">Region</label>
                            <select name="region" required>
                                <option value="">Select Region</option>
                                <option value="Region VII">Region VII</option>
                                <option value="Negros Island Region">Negros Island Region (NIR)</option>
                                <option value="NCR">NCR</option>
                                <option value="CAR">CAR</option>
                                <option value="Region I">Region I</option>
                                <option value="Region II">Region II</option>
                                <option value="Region III">Region III</option>
                                <option value="Region IV-A">Region IV-A</option>
                                <option value="MIMAROPA">Mimaropa</option>
                                <option value="Region V">Region V</option>
                                <option value="Region VI">Region VI</option>
                                <option value="Region VIII">Region VIII</option>
                                <option value="Region IX">Region IX</option>
                                <option value="Region X">Region X</option>
                                <option value="Region XI">Region XI</option>
                                <option value="Region XII">Region XII</option>
                                <option value="Region XIII">Region XIII</option>
                                <option value="BARMM">BARMM</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="field-label">Province</label>
                            <select name="province" required disabled>
                                <option value="">Select Province</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Municipality/City</label>
                            <select name="municipality_city">
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Barangay</label>
                            <select name="barangay">
                                <option value="">Select Barangay</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="checkbox-group">
                            <input type="checkbox" name="agree_terms" id="terms" required>
                            <label for="terms">I agree to the <a href="#" id="privacyPolicyLink">Terms of Service and Privacy Policy.</a></label>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="checkbox-group">
                            <input type="checkbox" name="agree_data" id="data">
                            <label for="data">I agree to share my anonymized data and participate in building a National Registry.<br><span style="color: #20b2aa; font-size: 12px;">By agreeing to this you will have access to exclusive data.</span></label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="sign_up_btn">
                            <span class="btn-loader" aria-hidden="true"></span>
                            <span class="btn-text">SIGN UP</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Custom Alert Function
        function showCustomAlert(message, title = 'Success', callback = null) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'custom-alert';
            alertDiv.innerHTML = `
                <h2>${title}</h2>
                <p>${message}</p>
                <button onclick="this.parentElement.remove();">OK</button>
            `;
            document.body.appendChild(alertDiv);
            
            alertDiv.querySelector('button').addEventListener('click', function() {
                if (callback) callback();
            });
        }

        const agree_terms = document.querySelector('input[name="agree_terms"]');
        const agree_data = document.querySelector('input[name="agree_data"]');
        const sign_up_btn = document.getElementById('sign_up_btn');
        const signUpBtnText = sign_up_btn.querySelector('.btn-text');

        function setRegisterLoading(isLoading) {
            sign_up_btn.classList.toggle('loading', isLoading);
            sign_up_btn.disabled = isLoading || !(agree_terms.checked && agree_data.checked);
            signUpBtnText.textContent = isLoading ? 'REGISTERING...' : 'SIGN UP';
        }

        sign_up_btn.disabled = true;

        function updateButtonState() {
            sign_up_btn.disabled = !(agree_terms.checked && agree_data.checked);
        }

        agree_terms.addEventListener('change', updateButtonState);
        agree_data.addEventListener('change', updateButtonState);

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            setRegisterLoading(true);
            const formData = new FormData(this);

            // Convert to plain object (or use FormData directly)
            const payload = Object.fromEntries(formData.entries());

            console.log('Form data to submit:', payload);

             try {
                const response = await fetch('api/register/patient', {   // your API endpoint
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    showCustomAlert('Registration successful!\nPlease check your email for verification instructions.', 'Welcome!');
                    // window.location.href = '/referral/login';   // redirect on success
                } else {
                    // Show validation errors
                    const errors = result.errors;
                    if (errors) {
                        Object.entries(errors).forEach(([field, messages]) => {
                            showCustomAlert(messages.join(', ') || 'Registration failed.', `${field}`);
                            // optionally show under each field
                        });
                    }
                    
                }

            } catch (error) {
                console.error('Network error:', error);
                showCustomAlert('Something went wrong. Please try again.', 'Error');
            } finally {
                setRegisterLoading(false);
            }
        });
                

        const toggleBtn = document.querySelector('.toggle-password');
        const APP_URL = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            });
        }

        // ── Province dropdown control ──────────────────────────────────────────
        const regionSelect      = document.querySelector('select[name="region"]');
        const provinceSelect    = document.querySelector('select[name="province"]');
        const municipalitySelect = document.querySelector('select[name="municipality_city"]');
        const barangaySelect    = document.querySelector('select[name="barangay"]');

        // Provinces that belong to Region VII (value="9")
        const regionVIIProvinces = [
            { value: '1', text: 'Bohol' },
            { value: '2', text: 'Cebu' },
            { value: '3', text: 'Negros Oriental' },
            { value: '4', text: 'Siquijor' },
        ];

        function resetProvince() {
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            provinceSelect.disabled = true;
            provinceSelect.value = '';
        }

        function resetMunicipality() {
            municipalitySelect.innerHTML = '<option value="">Select City</option>';
        }

        function resetBarangay() {
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        }

        function populateRegionVIIProvinces() {
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            regionVIIProvinces.forEach(p => {
                const option = document.createElement('option');
                option.value = p.value;
                option.textContent = p.text;
                provinceSelect.appendChild(option);
            });
            provinceSelect.disabled = false;
        }

        // On region change: show/hide province options
        regionSelect.addEventListener('change', function () {
            resetMunicipality();
            resetBarangay();

            if (this.value === 'Region VII') {       // Region VII
                populateRegionVIIProvinces();
            } else {
                resetProvince();
            }
        });
        // ── End province control ───────────────────────────────────────────────

        // Fetch Municipalities when Province changes
        provinceSelect.addEventListener('change', async function () {
            const provinceId = this.value;

            resetMunicipality();
            resetBarangay();

            if (!provinceId) return;

            municipalitySelect.innerHTML = '<option value="">Loading...</option>';

            try {
                const response = await fetch(`${APP_URL}/location/muncity/${provinceId}`);

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const municipalities = await response.json();

                municipalitySelect.innerHTML = '<option value="">Select City</option>';
                municipalities.forEach(muncity => {
                    const option = document.createElement('option');
                    option.value = muncity.id;
                    option.textContent = muncity.description;
                    municipalitySelect.appendChild(option);
                });

            } catch (error) {
                console.error('Error fetching municipalities:', error);
                municipalitySelect.innerHTML = '<option value="">Failed to load. Try again.</option>';
            }
        });

        // Fetch Barangays when Municipality changes
        municipalitySelect.addEventListener('change', async function () {
            const muncityId = this.value;

            resetBarangay();

            if (!muncityId) return;

            barangaySelect.innerHTML = '<option value="">Loading...</option>';

            try {
                const response = await fetch(`${APP_URL}/location/barangay/${muncityId}`);

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const barangays = await response.json();

                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                barangays.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay.id;
                    option.textContent = barangay.description;
                    barangaySelect.appendChild(option);
                });

            } catch (error) {
                console.error('Error fetching barangays:', error);
                barangaySelect.innerHTML = '<option value="">Failed to load. Try again.</option>';
            }
        });
    </script>

    <!-- Privacy Notice Modal -->
    <div class="privacy-modal-overlay" id="privacyModalOverlay">
        <div class="privacy-modal">
            <div class="privacy-modal-header">
                <h3>Privacy Notice</h3>
                <button class="privacy-modal-close" id="privacyModalClose">&times;</button>
            </div>
            <div class="privacy-modal-body">
                <p>This Privacy Policy will enable you to better understand how DOHCVCHD collects, processes, retains and uses your data. We hope you read through the policy.</p>

                <p class="notice_category">Statement of Policy</p>
                <p>The DOHCVCHD is the regional arm in Central Visayas of the Department of Health. It is the principal health agency in the country and is responsible for the enforcement of laws on health, ensuring access to basic public health services and quality health care, and regulation of health facilities, goods and services.</p>
                <p>Guided by the Data Privacy Principles, we collect, process, retain, use and share your data when you visit our office premises, avail of our services and systems, file for applications/renewals, submit requests and inquiries, lodge complaints, or when it is necessary in the performance of our statutory and regulatory mandates, including the operation of health information services, and implementation of disease surveillance and response initiatives, among others, subject to your consent or when expressly allowed by law.</p>
                <p>The DOHCVCHD faithfully adheres to the requirements of the Data Privacy Act, its implementing rules, and the regulations promulgated by the National Privacy Commission. We highly value the security of your data and your rights as data subjects.</p>

                <p class="notice_category">Collection and Use of Data</p>
                <p>Data is collected when the DOHCVCHD performs its governmental functions such as, but not limited to, provision of technical assistance to government and private partners, disease surveillance and health events response per Republic Act No. 11332 and related statutes, management of public health information systems, enforcement of regulatory authority (e.g. receipt of applications of health facilities), handling of complaints, and operations of health and laboratory services. Data is also collected when you avail of our programs and services such as the E-health Referral System and the DOHCVCHD Telemedicine, provided you have granted your consent.</p>
                <p>The manner by which these data is collected may be through the access of online portals, filling out of forms and information sheets, e-mail or in person through our receiving officers, and recording in the closed circuit television (CCTV) systems for office transactions. The online systems may require you of your name, address, contact information and birthday.</p>
                <p>The data shall be used for regulation, surveillance, analysis, policy formulation and guidance, health emergency and response efforts, provision of appropriate technical assistance, clarification of questions, conduct of investigation, identification and communication, safety and security, and continual service improvement.</p>
                <p>At all times, the data we collect shall be equal to the requirements needed to fulfill an intended purpose. The collection and use of data shall always be guided by the principles of transparency, legitimate purpose and proportionality.</p>

                <p class="notice_category">Data Sharing</p>
                <p>Data collected by DOHCVCHD is shared with the DOH Central Office, local government units, and other operating partners when required by laws and government regulation, particularly on management of public health information systems, and surveillance of notifiable diseases abd health events, among others. In all other instances, the DOHCVCHD executes a Data Sharing Agreement following the requirements of the NPC to ensure that your data is protected from unlawful uses and disclosures.</p>

                <p class="notice_category">Data Retention, Protection and Disposition</p>
                <p>For the services and systems available to the public, the DOHCVCHD may necessarily store and retain your data as part of its inherent and operational functions, without prejudice to the enforcement of the relevant rights of data subjects.</p>
                <p>Data collected are retained depending on the nature of the data being handled. Physical data are retained by the respective end-users or program managers through proper record filing and keeping. Electronic data which passes through online systems are saved in our local and cloud servers using encryption, firewall, or similar security features. It may also require entering a One-Time Password (OTP) as an added layer of protection. Access to these data is granted only to select personnel, all of whom are required to execute a Non-Disclosure Agreement.</p>
                <p>The DOHCVCHD does not warrant a foolproof or 100% breach-free data system. However, it commits to continually update its security features, review existing data protection policies, coordinate with the NPC for any data incidents, and keep you informed in all stages.</p>
                <p>The data subject may request for the deletion of his/her data, subject to the provisions of the data privacy act. As such, upon the data subject's request or when necessitated by the circumstances, the DOHCVCHD shall fully dispose of the data retained in the most prompt manner. The length of time in the retention and subsequent disposition of data, as the case may be, shall be in accordance with the records retention and disposition schedule of the National Archives of the Philippines and pertinent internal office protocols, taking into account the legitimate purpose(s) of the collection. When applicable, data shall be returned to the data owners. At all times, the data subject shall be informed that the data has been deleted and disposed of by issuing a certification to such effect.</p>

                <p class="notice_category">Data Subject's Rights</p>
                <p>Pursuant to the DPA, the data subject is entitled to the following rights:</p>
                <p>
                    <strong>Right to be informed:</strong>
                    <br>The data subject has a right to be informed whether personal data pertaining to him or her shall be, are being, or have been processed, including the existence of automated decision-making and profiling.
                    <br>The data subject shall be notified and furnished with information indicated hereunder before the entry of his or her personal data into the processing system of the personal information controller, or at the next practical opportunity:
                    <br>Description of the personal data to be entered into the system;
                    <br>Purposes for which they are being or will be processed, including processing for direct marketing, profiling or historical, statistical or scientific purpose;
                    <br>Basis of processing, when processing is not based on the consent of the data subject;
                    <br>Scope and method of the personal data processing;
                    <br>Methods utilized for automated access, if the same is allowed by the data subject, and the extent to which such access is authorized, including meaningful information about the logic involved, as well as the significance and the envisaged consequences of such processing for the data subject;
                    <br>The identity and contact details of the personal data controller or its representative;
                    <br>The period for which the information will be stored; and
                    <br>The existence of their rights as data subjects, including the right to access, correction, and object to the processing, as well as the right to lodge a complaint before the Commission.
                    Right to object. The data subject shall have the right to object to the processing of his or her personal data, including processing for direct marketing, automated processing or profiling. The data subject shall also be notified and given an opportunity to withhold consent to the processing in case of changes or any amendment to the information supplied or declared to the data subject in the preceding paragraph.
                </p>
                <p>When the data subject objects or withholds consent, the personal information controller shall no longer process the personal data, unless:</p>
                <p>The personal data is needed pursuant to a subpoena;
                    <br>The collection and processing are for obvious purposes, including, when it is necessary for the performance of or in relation to a contract or service to which the data subject is a party, or when necessary or desirable in the context of an employer-employee relationship between the collector and the data subject; or
                    <br>The information is being collected and processed as a result of a legal obligation.</p>
                <p>Right to access. The data subject has the right to reasonable access, upon demand, the following:</p>
                <p>Contents of his or her personal data that were processed;
                    <br>Sources from which personal data were obtained;
                    <br>Names and addresses of recipients of the personal data;
                    <br>Manner by which such data were processed;
                    <br>Reasons for the disclosure of the personal data to recipients, if any;
                    <br>Information on automated processes where the data will, or is likely to, be made as the sole basis for any decision that significantly affects or will affect the data subject;
                    <br>Date when his or her personal data concerning the data subject were last accessed and modified; and
                    The designation, name or identity, and address of the personal information controller.</p>
                <p>Right to rectification. The data subject has the right to dispute the inacuracy or error in the personal data and have the personal information controller correct it immediately and accordingly, unless the request is vexatious or otherwise unreasonable. If the personal data has been corrected, the personal information controller shall ensure the accessibility of both the new and retracted information and the simultaneous receipt of the new and retracted information by the intended recipients thereof: Provided,
                    That recipients or third parties who have previously received such processed personal shall be informed of its inaccuracy and its rectification, upon reasonable request of the data subject.
                    Right to erasure or blocking. The data subject shall have the right to suspend, withdraw or order the blocking, removal or destruction of his or her personal data from the personal information controller's filing system.
                    <br>This right may be exercised upon discovery and substantial proof of any of the following:
                    <br>The personal data is incomplete, outdated, false, or unlawfully obtained;
                    <br>The personal data is being used for purpose not authorized by the data subject;
                    <br>The personal data is no longer necessary for the purposes for which they were collected;
                    <br>The data subject withdraws consent or objects to the processing, and there is no other legal ground or overriding legitimate interest for the processing;
                    <br>The personal data concerns private information that is prejudicial to data subject, unless justified by freedom of speech, of expression, or of the press or otherwise authorized;
                    <br>The processing is unlawful;
                    <br>The personal information controller or personal information processor violated the rights of the data subject.
                    <br>The personal information controller may notify third parties who have previously received such processed personal information
                    <br>Right to damages. The data subject shall be indemnified for any damages sustained due to such inaccurate, incomplete, outdated, false, unlawfully obtained or unauthorized use of personal data, taking into account any violation of his or her rights and freedoms as data subject.
                </p>

                <p class="notice_category">Responsibility of Data Subjects</p>
                <p>As we commit to ensuring the best service to our clients, data subjects are concomitantly urged to be circumspect and vigilant that the online systems it is accessing is legitimate and valid. If unsure, you may call or coordinate with our office through the client feedback information provided herein.</p><br>

                <h4>Client Feedback</h4>
                <p>For requests, questions, complaints, or reports of any data breach or incidents, you may contact our Data Protection Officer through the following contact information:</p><br>
                <strong>Name :</strong> Data Protection Officer <br>
                <strong>Title/Office :</strong> Legal Section/Data Protection Office <br>
                <strong>Contact No. :</strong> (032) 260-9740 loc. 104 <br>
                <strong>Email :</strong> legal@ro7.doh.gov.ph
            </div>
            <div class="privacy-modal-footer">
                <button id="privacyModalOk">Close</button>
            </div>
        </div>
    </div>

    <script>
        const privacyLink = document.getElementById('privacyPolicyLink');
        const privacyOverlay = document.getElementById('privacyModalOverlay');

        privacyLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            privacyOverlay.classList.add('active');
        });

        privacyOverlay.addEventListener('click', function(e) {
            if (e.target === privacyOverlay) {
                privacyOverlay.classList.remove('active');
            }
        });

        document.getElementById('privacyModalClose').addEventListener('click', function() {
            privacyOverlay.classList.remove('active');
        });

        document.getElementById('privacyModalOk').addEventListener('click', function() {
            privacyOverlay.classList.remove('active');
        });
    </script>
</body>
</html>