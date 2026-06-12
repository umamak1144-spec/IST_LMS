<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Sign Up | IST LMS</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>

<div class="lms-box">

<div class="lms-logo">&#128216; IST <span>LMS</span></div>

    <h1>Admin Sign Up</h1>
    <h2>Create Admin Account</h2>

    <form id="signupForm">
        <input type="text" id="name" class="input-box" placeholder="Admin Name" required>
        <input type="email" id="email" class="input-box" placeholder="Email" required>
        <input type="password" id="password" class="input-box" placeholder="Password" required>

        <p id="msg" style="color:red; margin:6px 0; font-size:14px;"></p>

        <div class="lms-buttons">
            <button type="submit" class="btn-box">Register</button>
            <a href="signup_type.html" class="btn-box">Back</a>
        </div>
    </form>

</div>

<script>
document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('msg');
    msg.textContent = 'Registering...';
    msg.style.color = '#00eaff';

    const res = await fetch('backend/admin_auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'signup',
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        })
    });

    const data = await res.json();

    if (data.success) {
        msg.style.color = '#2ecc71';
        msg.textContent = 'Registered! Redirecting to login...';
        setTimeout(() => window.location.href = 'login.html', 1500);
    } else {
        msg.style.color = 'red';
        msg.textContent = data.message;
    }
});
</script>

</body>
</html>
