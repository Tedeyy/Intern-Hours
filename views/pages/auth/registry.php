<div class="auth-container">
    <div class="auth-box">
        <h2>Register for OurTracker</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="validation-message error" style="margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <form action="../api/register.php" method="POST">
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

            <?php
            // Fetch offices
            $offices = $pdo->query("SELECT * FROM office ORDER BY office_name")->fetchAll();
            ?>
            <div class="form-group">
                <label for="office_id">Office</label>
                <select id="office_id" name="office_id" required onchange="toggleNewInput('office')">
                    <option value="">Select Office</option>
                    <?php foreach ($offices as $o): ?>
                        <option value="<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['office_name']); ?></option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New Office</option>
                </select>
                <input type="text" id="new_office_name" name="new_office_name" placeholder="Enter new office name" style="display: none; margin-top: 10px;">
            </div>

            <?php
            // Fetch organizations
            $orgs = $pdo->query("SELECT * FROM organization ORDER BY organization_name")->fetchAll();
            ?>
            <div class="form-group">
                <label for="organization_id">Organization</label>
                <select id="organization_id" name="organization_id" required onchange="toggleNewInput('organization')">
                    <option value="">Select Organization</option>
                    <?php foreach ($orgs as $org): ?>
                        <option value="<?php echo $org['id']; ?>"><?php echo htmlspecialchars($org['organization_name']); ?></option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New Organization</option>
                </select>
                <input type="text" id="new_organization_name" name="new_organization_name" placeholder="Enter new organization name" style="display: none; margin-top: 10px;">
            </div>

            <button type="submit" class="btn-primary">Register</button>
        </form>
        <p class="auth-switch">
            Already have an account? <a href="feed.php?page=login">Login here</a>
        </p>
    </div>
</div>

<link rel="stylesheet" href="../assets/css/auth.css">

<script src="../assets/js/register.js"></script>
