<?php
$pageTitle = 'Logowanie - Szrotomoto';
include __DIR__ . '/_partials/head.php';
?>
  <?php include __DIR__ . '/_partials/nav.php'; ?>
  <main class="max-w-md mx-auto my-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Zaloguj się</h1>
        <p class="text-gray-600">Witaj z powrotem!</p>
      </div>

      <form id="loginForm" class="space-y-6">
        <div>
          <label class="block mb-2 text-sm font-semibold text-slate-900">Email</label>
          <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="twoj@email.pl">
        </div>
        
        <div>
          <label class="block mb-2 text-sm font-semibold text-slate-900">Hasło</label>
          <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="••••••••">
        </div>

        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
          Zaloguj się
        </button>
      </form>

      <div id="message" role="status" class="mt-4 p-4 rounded-lg hidden"></div>

      <div class="mt-6 pt-6 border-t border-gray-200 text-center">
        <p class="text-sm text-gray-600">
          Nie masz konta? 
          <a href="/register.php" class="text-blue-600 hover:text-blue-700 font-semibold no-underline">Zarejestruj się</a>
        </p>
      </div>
    </div>
  </main>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.currentTarget;
      const data = new URLSearchParams(new FormData(form));
      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Logowanie...';

      const res = await apiFetch('/api/login/login.php', { method: 'POST', body: data });

      if (res.ok) {
        const msgEl = document.getElementById('message');
        msgEl.textContent = res.data.user_name ? `Witaj, ${res.data.user_name}!` : res.data.message;
        msgEl.className = 'mt-4 p-4 rounded-lg bg-green-50 text-green-600 border border-green-200';
        msgEl.classList.remove('hidden');
        setTimeout(() => window.location.href = '/offers.php', 1000);
      } else {
        const msgEl = document.getElementById('message');
        msgEl.textContent = res.error || 'Logowanie nie powiodło się';
        msgEl.className = 'mt-4 p-4 rounded-lg bg-red-50 text-red-600 border border-red-200';
        msgEl.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Zaloguj się';
      }
    });
  </script>
</body>
</html>
