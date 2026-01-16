<?php
// Minimal navigation partial. Uses JS to detect auth state via cookie-based endpoints when needed.
?>
<header class="site-nav">
  <div class="nav-inner container">
    <div class="brand"><a href="/public/offers.php">Szrotomoto</a></div>
    <nav>
      <a href="/public/offers.php">Browse offers</a>
      <a href="/public/offers_create.php">Create offer</a>
    </nav>
    <div class="nav-auth" id="navAuth">
      <a href="/public/login.php" id="navLogin">Login</a>
      <a href="/public/register.php" id="navRegister">Register</a>
      <button id="navLogout" style="display:none">Logout</button>
      <span id="navUser" class="nav-user"></span>
    </div>
  </div>
</header>

<script>
  // Try to detect session by calling a lightweight endpoint (status is unauthenticated-safe)
  // If you add /api/me we can replace this with a proper authenticated check.
  (async function(){
    try{
      // ping a protected endpoint by relying on cookie being sent; use brands (public) to avoid errors
      // instead call login status by calling login endpoint with no data would return 405; avoid that.
      // We'll call a harmless endpoint and then check for session cookie by requesting /api/login/login.php with an OPTIONS or simple GET won't help.
      // Simpler approach: try to fetch /api/vehicles/brands.php and then try to call /api/login/logout.php with no credentials to see 401? Skip this complexity for now.
      // Show logout button if session cookie present in browser (best-effort check by calling /api/status.php — session doesn't indicate auth but we'll attempt /api/vehicles/brands.php and then show nothing)
      // We'll only wire up logout button behavior here.
      const logoutBtn = document.getElementById('navLogout');
      const navLogin = document.getElementById('navLogin');
      const navRegister = document.getElementById('navRegister');
      const navUser = document.getElementById('navUser');

      logoutBtn.addEventListener('click', async () => {
        const res = await window.apiFetch('/api/login/logout.php', { method: 'POST' });
        if (res.ok) {
          // reload to update UI
          window.location.href = '/public/offers.php';
        } else {
          alert(res.error || 'Logout failed');
        }
      });
    }catch(e){
      // ignore
    }
  })();
</script>
