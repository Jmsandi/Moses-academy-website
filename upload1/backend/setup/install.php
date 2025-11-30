<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Bayan CMS</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .install-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-height: 80px;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="logo">
            <img src="../bayan/bayaLogo.png" alt="Bayan Logo">
            <h2 class="mt-3">CMS Installation</h2>
            <p class="text-muted">Bayan Children's Foundation</p>
        </div>

        <!-- Step 1: Welcome -->
        <div id="step1" class="step active">
            <h4>Welcome to the Installation</h4>
            <p>This installation wizard will help you set up the Bayan CMS.</p>
            <h5 class="mt-4">Before you begin:</h5>
            <ul>
                <li>Create a MySQL database in cPanel</li>
                <li>Note down the database name, username, and password</li>
                <li>Make sure the <code>uploads/</code> folder is writable</li>
            </ul>
            <button class="btn btn-primary" onclick="showStep(2)">Get Started →</button>
        </div>

        <!-- Step 2: Database Configuration -->
        <div id="step2" class="step">
            <h4>Database Configuration</h4>
            <p>Enter your MySQL database details:</p>
            <form id="dbForm">
                <div class="mb-3">
                    <label for="db_host" class="form-label">Database Host</label>
                    <input type="text" class="form-control" id="db_host" value="localhost" required>
                    <small class="text-muted">Usually 'localhost' for cPanel</small>
                </div>
                <div class="mb-3">
                    <label for="db_name" class="form-label">Database Name</label>
                    <input type="text" class="form-control" id="db_name" required>
                </div>
                <div class="mb-3">
                    <label for="db_user" class="form-label">Database Username</label>
                    <input type="text" class="form-control" id="db_user" required>
                </div>
                <div class="mb-3">
                    <label for="db_pass" class="form-label">Database Password</label>
                    <input type="password" class="form-control" id="db_pass" required>
                </div>
                <button type="button" class="btn btn-secondary" onclick="showStep(1)">← Back</button>
                <button type="button" class="btn btn-primary" onclick="testDatabase()">Test Connection →</button>
            </form>
            <div id="dbTestResult" class="mt-3"></div>
        </div>

        <!-- Step 3: Admin Account -->
        <div id="step3" class="step">
            <h4>Create Admin Account</h4>
            <p>Set up your administrator credentials:</p>
            <form id="adminForm">
                <div class="mb-3">
                    <label for="admin_user" class="form-label">Admin Username</label>
                    <input type="text" class="form-control" id="admin_user" value="admin" required>
                </div>
                <div class="mb-3">
                    <label for="admin_pass" class="form-label">Admin Password</label>
                    <input type="password" class="form-control" id="admin_pass" required>
                    <small class="text-muted">Use a strong password!</small>
                </div>
                <div class="mb-3">
                    <label for="admin_pass_confirm" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="admin_pass_confirm" required>
                </div>
                <div class="mb-3">
                    <label for="jwt_secret" class="form-label">JWT Secret Key</label>
                    <input type="text" class="form-control" id="jwt_secret" required>
                    <small class="text-muted">Random string for security</small>
                    <button type="button" class="btn btn-sm btn-secondary mt-1" onclick="generateSecret()">Generate Random</button>
                </div>
                <button type="button" class="btn btn-secondary" onclick="showStep(2)">← Back</button>
                <button type="button" class="btn btn-primary" onclick="install()">Install CMS →</button>
            </form>
            <div id="installResult" class="mt-3"></div>
        </div>

        <!-- Step 4: Complete -->
        <div id="step4" class="step">
            <div class="text-center">
                <h4 class="text-success">✓ Installation Complete!</h4>
                <p class="mt-3">Your Bayan CMS has been successfully installed.</p>
                <div class="alert alert-info mt-4">
                    <strong>Important:</strong> For security reasons, please delete or rename the <code>setup/</code> folder.
                </div>
                <div class="mt-4">
                    <a href="../admin-login.html" class="btn btn-primary btn-lg">Go to Admin Login →</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let dbConfig = {};

        function showStep(step) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }

        function generateSecret() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            let secret = '';
            for (let i = 0; i < 64; i++) {
                secret += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('jwt_secret').value = secret;
        }

        async function testDatabase() {
            const dbHost = document.getElementById('db_host').value;
            const dbName = document.getElementById('db_name').value;
            const dbUser = document.getElementById('db_user').value;
            const dbPass = document.getElementById('db_pass').value;
            
            const resultDiv = document.getElementById('dbTestResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing connection...</div>';
            
            try {
                const response = await fetch('install_process.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'test_db',
                        db_host: dbHost,
                        db_name: dbName,
                        db_user: dbUser,
                        db_pass: dbPass
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    dbConfig = {dbHost, dbName, dbUser, dbPass};
                    resultDiv.innerHTML = '<div class="alert alert-success">✓ Connection successful!</div>';
                    setTimeout(() => showStep(3), 1000);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">✗ ' + data.error + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger">✗ Connection failed: ' + error.message + '</div>';
            }
        }

        async function install() {
            const adminUser = document.getElementById('admin_user').value;
            const adminPass = document.getElementById('admin_pass').value;
            const adminPassConfirm = document.getElementById('admin_pass_confirm').value;
            const jwtSecret = document.getElementById('jwt_secret').value;
            
            if (adminPass !== adminPassConfirm) {
                alert('Passwords do not match!');
                return;
            }
            
            const resultDiv = document.getElementById('installResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Installing CMS...</div>';
            
            try {
                const response = await fetch('install_process.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'install',
                        ...dbConfig,
                        admin_user: adminUser,
                        admin_pass: adminPass,
                        jwt_secret: jwtSecret
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showStep(4);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">✗ ' + data.error + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger">✗ Installation failed: ' + error.message + '</div>';
            }
        }

        // Generate a random JWT secret on page load
        window.addEventListener('DOMContentLoaded', () => {
            generateSecret();
        });
    </script>
</body>
</html>

