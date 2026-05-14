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
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
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
