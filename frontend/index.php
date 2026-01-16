<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Szrotomoto — Home</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
  <main class="container">
    <h1>Szrotomoto</h1>

    <nav>
      <ul>
        <li><a href="/public/login.php">Login</a></li>
        <li><a href="/public/register.php">Register</a></li>
        <li><a href="/public/vehicles.php">Browse vehicles</a></li>
      </ul>
    </nav>

    <section>
      <p>This is a minimal server-side frontend scaffold that uses the existing backend JSON API in <code>/backend/api</code>.</p>
      <p>Open the pages above to test login, register and vehicles lookups. The JS uses fetch with credentials so it works with PHP session cookies.</p>
    </section>
  </main>

  <script src="/public/assets/js/api.js"></script>
</body>
</html>
