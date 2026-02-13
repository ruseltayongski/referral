<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #20b2aa; min-height: 100vh; display: flex;}
        .container {display: flex; width: 100%; min-height: 100vh;}
        .sidebar {background: #2c3e50; color: #fff; padding: 60px 40px; display: flex; flex-direction: column; justify-content: center; width: 30%; min-height: 100vh;}
        .sidebar-logo {font-size: 28px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;}
        .sidebar .logo-icon {width: 40px; height: 40px; background: #20b2aa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;}
        .sidebar-description {font-size: 14px; color: #bdc3c7; line-height: 1.6; margin-bottom: 30px;}
        .sidebar-highlight {font-size: 13px; color: #ecf0f1; margin: 15px 0; line-height: 1.6;}
        .sidebar-login {margin-top: 50px}
        .sidebar-login p {font-size: 14px; margin-bottom: 15px; color: #bdc3c7;}
        .btn-login {background: transparent; border: 2px solid #20b2aa; color: #20b2aa; padding: 10px 30px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;}
        .btn-login:hover {background: #20b2aa; color: #fff;}
        .form-container {background: #fff; flex: 1; padding: 40px 50px; overflow-y: auto; max-height: 100vh;}
        .form-content {max-width: 600px;}
        .form-title {font-size: 22px; font-weight: 700; color: #2c3e50; margin-bottom: 30px;}
        .section-title {font-size: 14px; font-weight: 700; color: #2c3e50; margin-top: 25px; margin-bottom: 20px; text-transform: capitalize;}
        .form-row {display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;}
        .form-row.two-col {grid-template-columns: 1fr 1fr;}
        .form-row.full {grid-template-columns: 1fr;}
        .form-group {display: flex; flex-direction: column;}
        .field-label {font-size: 12px; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: capitalize;}
        input, select {padding: 10px 12px; border: 1px solid #d0d0d0; border-radius: 4px; font-size: 13px; font-family: inherit; transition: all 0.3s;}
        input:focus, select:focus {outline: none; border-color: #20b2aa; box-shadow: 0 0 0 2px rgba(32, 178, 170, 0.1);}
        input::placeholder {color: #999;}
        .input-group {display: flex; gap: 10px; align-items: flex-end;}
        .input-group select {width: 80px; padding: 10px 8px;}
        .input-group input {flex: 1;}
        .password-input-group {position: relative;}
        .password-input-group input {padding-right: 40px;}
        .toggle-password {position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px; color: #999;}
        .checkbox-group {display: flex; align-items: flex-start; gap: 10px; margin: 15px 0; font-size: 13px; color: #555;}
        .checkbox-group input[type="checkbox"] {width: 18px; height: 18px; cursor: pointer; accent-color: #20b2aa; margin-top: 2px; flex-shrink: 0;}
        .checkbox-group a {color: #20b2aa; text-decoration: none;}
        .checkbox-group a:hover {text-decoration: underline;}
        .form-actions {display: flex; gap: 15px; margin-top: 40px;}
        button {border: none; border-radius: 4px; padding: 12px 30px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.5px;}
        .btn-submit {background: #20b2aa; color: white; flex: 1;}
        .btn-submit:hover {background: #1a8a82; box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);}
        .btn-back {background: #e8e8e8; color: #333;}
        .btn-back:hover {background: #d4d4d4;}
        
        @media (max-width: 1024px) {
            .sidebar {width: 25%; padding: 40px 25px;}
            .form-container {padding: 30px 40px;}
            .form-row {grid-template-columns: 1fr 1fr;}
        }
        
        @media (max-width: 768px) {
            .container {flex-direction: column;}
            .sidebar {width: 100%; min-height: auto; padding: 30px 20px; justify-content: flex-start;}
            .sidebar-login {margin-top: 20px;}
            .form-container {max-height: none; padding: 30px 20px;}
            .form-row {grid-template-columns: 1fr;}
            .form-row.two-col {grid-template-columns: 1fr;}
            .form-actions {flex-direction: column;}
            .btn-submit, .btn-back {width: 100%;}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div>
                <div class="sidebar-logo">
                    <div class="logo-icon">❤️</div>
                    <span>HealthCare</span>
                </div>
                <div class="sidebar-description">The Best, Easy-to-Use Healthcare Management System for your Clinic</div>
                <div class="sidebar-highlight">Easy Appointment Scheduling, Electronic Medical Records Management & More!</div>
            </div>
            <div class="sidebar-login">
                <p>Already have an account?</p>
                <button class="btn-login">LOG IN</button>
            </div>
        </div>

        <div class="form-container">
            <div class="form-content">
                <div class="form-title">Credentials</div>

                <form method="POST" action="/register">
                    <div class="form-row two-col">
                        <div class="form-group">
                            <label class="field-label">Email Address</label>
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Password</label>
                            <div class="password-input-group">
                                <input type="password" name="password" placeholder="Password" required>
                                <span class="toggle-password">👁️</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-title" style="margin-top: 35px;">Basic Information</div>

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

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label class="field-label">PRC Number</label>
                            <input type="text" name="prc_number" placeholder="PRC Number">
                        </div>
                        <div class="form-group">
                            <label class="field-label">Specialization</label>
                            <select name="specialization">
                                <option value="">Select Specialization</option>
                                <option value="General Practice">General Practice</option>
                                <option value="Internal Medicine">Internal Medicine</option>
                                <option value="Surgery">Surgery</option>
                                <option value="Pediatrics">Pediatrics</option>
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
                        <button type="submit" class="btn-submit">SIGN UP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.querySelector('.toggle-password');
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
    </script>
</body>
</html>
