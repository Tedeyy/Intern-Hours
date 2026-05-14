<div class="auth-container">
    <div class="auth-box">
        <h2>Register for OurTracker</h2>
        <form action="../auth/register.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required oninput="checkEmailAvailability()">
                <div id="email-message" class="validation-message"></div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <svg id="password-eye" class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-input-container">
                    <input type="password" id="confirm_password" name="confirm_password" required oninput="checkPasswordMatch()">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                        <svg id="confirm_password-eye" class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div id="password-match-message" class="validation-message"></div>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Intern">Intern</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Register</button>
        </form>
        <p class="auth-switch">
            Already have an account? <a href="feed.php?page=login">Login here</a>
        </p>
    </div>
</div>

<style>
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 20px;
    }
    .auth-box {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
    }
    .auth-box h2 {
        margin: 0 0 20px 0;
        color: #1a1a1a;
        text-align: center;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #4a4a4a;
        font-weight: 500;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #1a1a1a;
    }
    .password-input-container {
        position: relative;
    }
    .password-input-container input {
        padding-right: 50px;
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .toggle-password:hover {
        opacity: 0.7;
    }
    .eye-icon {
        color: #4a4a4a;
    }
    .validation-message {
        margin-top: 5px;
        font-size: 14px;
    }
    .validation-message.success {
        color: #28a745;
    }
    .validation-message.error {
        color: #dc3545;
    }
    .btn-primary {
        width: 100%;
        padding: 12px;
        background: #1a1a1a;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-primary:hover {
        background: #4a4a4a;
    }
    .auth-switch {
        text-align: center;
        margin-top: 20px;
        color: #4a4a4a;
    }
    .auth-switch a {
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 600;
    }
    .auth-switch a:hover {
        text-decoration: underline;
    }
</style>

<script>
let emailCheckTimeout;

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById(inputId + '-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        `;
    } else {
        input.type = 'password';
        eyeIcon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        `;
    }
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const message = document.getElementById('password-match-message');
    
    if (confirmPassword === '') {
        message.textContent = '';
        message.className = 'validation-message';
        return;
    }
    
    if (password === confirmPassword) {
        message.textContent = 'Passwords match';
        message.className = 'validation-message success';
    } else {
        message.textContent = 'Passwords do not match';
        message.className = 'validation-message error';
    }
}

function checkEmailAvailability() {
    const email = document.getElementById('email').value;
    const message = document.getElementById('email-message');
    
    clearTimeout(emailCheckTimeout);
    
    if (email === '' || !email.includes('@')) {
        message.textContent = '';
        message.className = 'validation-message';
        return;
    }
    
    message.textContent = 'Checking...';
    message.className = 'validation-message';
    
    emailCheckTimeout = setTimeout(() => {
        fetch('../../api/check-email.php?email=' + encodeURIComponent(email))
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    message.textContent = 'Email is available';
                    message.className = 'validation-message success';
                } else {
                    message.textContent = 'Email is already registered';
                    message.className = 'validation-message error';
                }
            })
            .catch(error => {
                message.textContent = 'Error checking email';
                message.className = 'validation-message error';
            });
    }, 500);
}
</script>
