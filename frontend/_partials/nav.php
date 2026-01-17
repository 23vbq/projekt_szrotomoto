<?php
// Navigation partial with authentication-aware rendering
?>
<header class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo/Brand -->
      <div class="flex-shrink-0">
        <a href="/offers.php" class="text-2xl font-bold text-slate-900 hover:text-blue-600 transition-colors no-underline">
          🚗 Szrotomoto
        </a>
      </div>

      <!-- Navigation Links -->
      <nav class="hidden md:flex md:items-center md:space-x-1">
        <a href="/offers.php" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors no-underline">
          Przeglądaj oferty
        </a>
        <a href="/offers_create.php" id="navCreateOffer" class="hidden px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors no-underline">
          Dodaj ofertę
        </a>
      </nav>

      <!-- Auth Section -->
      <div class="flex items-center gap-3" id="navAuth">
        <!-- Login/Register (shown when not authenticated) -->
        <a href="/login.php" id="navLogin" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 no-underline transition-colors">
          Zaloguj się
        </a>
        <a href="/register.php" id="navRegister" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors no-underline">
          Zarejestruj się
        </a>

        <!-- User Menu (shown when authenticated) -->
        <div id="navUserMenu" class="hidden flex items-center gap-3">
          <button id="navUserBtn" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer border border-gray-200" title="Your account">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="text-gray-600">
              <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" fill="currentColor" opacity="0.9"></path>
              <path d="M2 22c0-3.866 3.582-7 10-7s10 3.134 10 7v1H2v-1z" fill="currentColor" opacity="0.9"></path>
            </svg>
            <span id="navUser" class="font-semibold text-slate-900"></span>
          </button>
          <button id="navLogout" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-red-600 hover:bg-red-50 border border-gray-200 rounded-lg transition-colors cursor-pointer">
            Wyloguj
          </button>
        </div>
      </div>

      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button id="mobileMenuBtn" class="p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 py-4">
      <div class="flex flex-col space-y-2">
        <a href="/offers.php" class="px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors no-underline">
          Przeglądaj oferty
        </a>
        <a href="/offers_create.php" id="navCreateOfferMobile" class="hidden px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors no-underline">
          Dodaj ofertę
        </a>
      </div>
    </div>
  </div>
</header>

<script>
  // Authentication state management and navigation updates
  (async function(){
    try {
      const logoutBtn = document.getElementById('navLogout');
      const navLogin = document.getElementById('navLogin');
      const navRegister = document.getElementById('navRegister');
      const navUser = document.getElementById('navUser');
      const navUserBtn = document.getElementById('navUserBtn');
      const navUserMenu = document.getElementById('navUserMenu');
      const navCreateOffer = document.getElementById('navCreateOffer');
      const navCreateOfferMobile = document.getElementById('navCreateOfferMobile');
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const mobileMenu = document.getElementById('mobileMenu');

      // Default state: show login/register, hide user menu and create offer
      if (navLogin) navLogin.classList.remove('hidden');
      if (navRegister) navRegister.classList.remove('hidden');
      if (navUserMenu) navUserMenu.classList.add('hidden');
      if (navCreateOffer) navCreateOffer.classList.add('hidden');
      if (navCreateOfferMobile) navCreateOfferMobile.classList.add('hidden');

      // Check authentication status
      const res = await window.apiFetch('/api/login/me.php', { method: 'GET' });
      
      if (res.ok && res.data && res.data.authenticated) {
        // User is authenticated
        if (navLogin) navLogin.classList.add('hidden');
        if (navRegister) navRegister.classList.add('hidden');
        if (navUserMenu) navUserMenu.classList.remove('hidden');
        if (navCreateOffer) navCreateOffer.classList.remove('hidden');
        if (navCreateOfferMobile) navCreateOfferMobile.classList.remove('hidden');
        if (navUser) navUser.textContent = res.data.user_name || 'Użytkownik';
      } else {
        // User is not authenticated
        if (navLogin) navLogin.classList.remove('hidden');
        if (navRegister) navRegister.classList.remove('hidden');
        if (navUserMenu) navUserMenu.classList.add('hidden');
        if (navCreateOffer) navCreateOffer.classList.add('hidden');
        if (navCreateOfferMobile) navCreateOfferMobile.classList.add('hidden');
        if (navUser) navUser.textContent = '';
      }
      
      // Log for debugging (remove in production)
      if (res.ok && res.data) {
        console.log('Auth status:', res.data.authenticated ? 'authenticated' : 'not authenticated', res.data);
      } else {
        console.warn('Failed to check auth status:', res.error || 'Unknown error', res);
      }

      // Logout handler
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
          const r = await window.apiFetch('/api/login/logout.php', { method: 'GET' });
          if (r.ok) {
            // Reload page to update navigation state
            window.location.href = '/offers.php';
          } else {
            alert(r.error || 'Wylogowanie nie powiodło się');
          }
        });
      }

      // User button handler
      if (navUserBtn) {
        navUserBtn.addEventListener('click', () => {
          // Navigate to user area (change to profile page if you add one)
          window.location.href = '/offers.php';
        });
      }

      // Mobile menu toggle
      if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
          const isHidden = mobileMenu.classList.contains('hidden');
          if (isHidden) {
            mobileMenu.classList.remove('hidden');
          } else {
            mobileMenu.classList.add('hidden');
          }
        });
      }
    } catch(e) {
      console.error('Navigation initialization error:', e);
    }
  })();
</script>

