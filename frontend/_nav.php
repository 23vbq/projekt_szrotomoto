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
      <button id="navLogout" class="secondary" style="display:none">Logout</button>
      <button id="navUserBtn" class="nav-user-btn" style="display:none" title="Your account">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
          <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" fill="#0f172a" opacity="0.9"></path>
          <path d="M2 22c0-3.866 3.582-7 10-7s10 3.134 10 7v1H2v-1z" fill="#0f172a" opacity="0.9"></path>
        </svg>
        <span id="navUser" class="nav-user"></span>
      </button>
    </div>
  </div>
</header>

<script>
  // Use the new /api/login/me.php endpoint to determine auth state and update the nav.
  (async function(){
    try{
  const logoutBtn = document.getElementById('navLogout');
  const navLogin = document.getElementById('navLogin');
  const navRegister = document.getElementById('navRegister');
  const navUser = document.getElementById('navUser');
  const navUserBtn = document.getElementById('navUserBtn');

      // default visibility
      if (navLogin) navLogin.style.display = '';
      if (navRegister) navRegister.style.display = '';
      if (logoutBtn) logoutBtn.style.display = 'none';

      const res = await window.apiFetch('/api/login/me.php', { method: 'GET' });
      if (res.ok && res.data && res.data.authenticated) {
        if (navLogin) navLogin.style.display = 'none';
        if (navRegister) navRegister.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'inline-block';
        if (navUserBtn) navUserBtn.style.display = 'inline-flex';
        if (navUser) navUser.textContent = res.data.user_name || '';
      } else {
        if (navLogin) navLogin.style.display = '';
        if (navRegister) navRegister.style.display = '';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (navUserBtn) navUserBtn.style.display = 'none';
        if (navUser) navUser.textContent = '';
      }

      if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
          const r = await window.apiFetch('/api/login/logout.php', { method: 'POST' });
          if (r.ok) {
            window.location.href = '/public/offers.php';
          } else {
            alert(r.error || 'Logout failed');
          }
        });
      }

      if (navUserBtn) {
        navUserBtn.addEventListener('click', () => {
          // Navigate to user area (change to profile page if you add one)
          window.location.href = '/public/offers.php';
        });
      }
    }catch(e){
      console.error(e);
    }
  })();
</script>
