<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
  <script src="/public/assets/js/api.js"></script>
</head>
<body>
  <?php include __DIR__ . '/_nav.php'; ?>
  <main class="container">
    <h1>Register</h1>

    <form id="registerForm">
      <label>Name<br><input type="text" name="name" required></label>
      <label>Email<br><input type="email" name="email" required></label>
      <label>Password<br><input type="password" name="password" required></label>
      <label>Repeat password<br><input type="password" name="repeated_password" required></label>
      <button type="submit">Register</button>
    </form>

    <div id="message" role="status"></div>

    <p><a href="/public/login.php">Already have an account?</a></p>
  <p><a href="/public/offers.php">Back</a></p>
  </main>

  <script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.currentTarget;
      const data = new URLSearchParams(new FormData(form));
  const res = await apiFetch('/api/login/register.php', { method: 'POST', body: data });

      if (res.ok) {
        document.getElementById('message').textContent = res.data.message || 'Registered successfully';
        setTimeout(() => window.location.href = '/public/login.php', 900);
      } else {
        document.getElementById('message').textContent = res.error || 'Registration failed';
      }
    });
  </script>
</body>
</html>
