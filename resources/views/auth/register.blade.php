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
                            <label for="terms">I agree to the <a href="#">Terms of Service and Privacy Policy.</a></label>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="checkbox-group">
                            <input type="checkbox" name="agree_data" id="data">
                            <label for="data">I agree to share my anonymized data and participate in building a National Registry.<br><span style="color: #20b2aa; font-size: 12px;">By agreeing to this you will have access to exclusive data.</span></label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="sign_up_btn">SIGN UP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const agree_terms = document.querySelector('input[name="agree_terms"]');
        const agree_data = document.querySelector('input[name="agree_data"]');
        const sign_up_btn = document.getElementById('sign_up_btn');

        sign_up_btn.disabled = true;

        function updateButtonState() {
            sign_up_btn.disabled = !(agree_terms.checked && agree_data.checked);
        }

        agree_terms.addEventListener('change', updateButtonState);
        agree_data.addEventListener('change', updateButtonState);

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
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
                    alert('Registration successful!');
                    // window.location.href = '/referral/login';   // redirect on success
                } else {
                    // Show validation errors
                    const errors = result.errors;
                    if (errors) {
                        Object.entries(errors).forEach(([field, messages]) => {
                            console.error(`${field}: ${messages.join(', ')}`);
                            // optionally show under each field
                        });
                    }
                    alert(result.message || 'Registration failed.');
                }

            } catch (error) {
                console.error('Network error:', error);
                alert('Something went wrong. Please try again.');
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
</body>
</html>