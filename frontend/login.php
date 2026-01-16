<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
  <main class="container">
    <h1>Login</h1>

    <form id="loginForm">
      <label>Email<br><input type="email" name="email" required></label>
      <label>Password<br><input type="password" name="password" required></label>
      <button type="submit">Login</button>
    </form>

    <div id="message" role="status"></div>

    <p><a href="/public/register.php">Create an account</a></p>
    <p><a href="/public/index.php">Back</a></p>
  </main>

  <script src="/public/assets/js/api.js"></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.currentTarget;
      const data = new URLSearchParams(new FormData(form));
      const res = await apiFetch('/backend/api/login/login.php', { method: 'POST', body: data });

      if (res.ok) {
        document.getElementById('message').textContent = res.data.user_name ? `Welcome, ${res.data.user_name}` : res.data.message;
        // Redirect to vehicles page after a short pause
        setTimeout(() => window.location.href = '/public/vehicles.php', 700);
      } else {
        document.getElementById('message').textContent = res.error || 'Login failed';
      }
    });
  </script>
</body>
</html>
