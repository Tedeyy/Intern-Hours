<div class="auth-container">
    <div class="auth-box">
        <h2>Login to OurTracker</h2>
        <form action="/auth/login" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <p class="auth-switch">
            Don't have an account? <a href="feed.php?page=register">Register here</a>
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
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }
    .form-group input:focus {
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
