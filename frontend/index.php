<?php
$pageTitle = 'Szrotomoto - Portal motoryzacyjny';
include __DIR__ . '/_partials/head.php';
?>
  <?php include __DIR__ . '/_partials/nav.php'; ?>
  <main class="max-w-7xl mx-auto my-12 px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="text-center mb-16">
      <h1 class="text-5xl font-bold text-slate-900 mb-4">
        Znajdź swój wymarzony pojazd
      </h1>
      <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
        Największy wybór używanych pojazdów w jednym miejscu. Przeglądaj oferty, porównuj ceny i znajdź idealny samochód dla siebie.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/offers.php" class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl text-lg no-underline">
          Przeglądaj oferty
        </a>
        <a href="/register.php" class="px-8 py-4 bg-white border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors text-lg no-underline">
          Dodaj swoją ofertę
        </a>
      </div>
    </div>

    <!-- Features Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
      <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Szeroki wybór</h3>
        <p class="text-gray-600">Tysiące ofert pojazdów różnych marek i modeli w jednym miejscu.</p>
      </div>

      <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Bezpieczne transakcje</h3>
        <p class="text-gray-600">Weryfikowani sprzedawcy i bezpieczne metody płatności.</p>
      </div>

      <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Szybkie wyszukiwanie</h3>
        <p class="text-gray-600">Zaawansowane filtry i sortowanie pomagają znaleźć idealny pojazd.</p>
      </div>
    </div>

    <!-- Recent Offers Preview -->
    <div class="mb-16">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-slate-900">Najnowsze oferty</h2>
        <a href="/offers.php" class="text-blue-600 hover:text-blue-700 font-semibold no-underline">
          Zobacz wszystkie →
        </a>
      </div>
      <div id="recentOffers" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="col-span-full text-center py-12 text-gray-500">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
          <p>Ładowanie ofert...</p>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-12 text-center text-white">
      <h2 class="text-3xl font-bold mb-4">Masz pojazd do sprzedania?</h2>
      <p class="text-xl mb-8 text-blue-100">Dodaj swoją ofertę już dziś i dotrzyj do tysięcy potencjalnych kupujących!</p>
      <a href="/offers_create.php" class="inline-block px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl no-underline">
        Dodaj ofertę za darmo
      </a>
    </div>
  </main>

  <script>
    // Load recent offers
    (async function() {
      try {
        const res = await apiFetch('/api/offers/search.php');
        if (res.ok && Array.isArray(res.data)) {
          const offers = res.data.slice(0, 4); // Show only 4 recent offers
          const container = document.getElementById('recentOffers');
          
          if (offers.length === 0) {
            container.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">Brak dostępnych ofert</div>';
            return;
          }

          container.innerHTML = '';
          offers.forEach(o => {
            const card = document.createElement('div');
            card.className = 'bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow';
            
            const imgContainer = document.createElement('div');
            imgContainer.className = 'relative h-48 bg-gray-100';
            
            if (o.attachment_id) {
              const imgLink = document.createElement('a');
              imgLink.href = `/offer.php?offer_id=${encodeURIComponent(o.id)}`;
              const img = document.createElement('img');
              img.src = `/api/attachments/show.php?id=${encodeURIComponent(o.attachment_id)}`;
              img.alt = o.title || 'Zdjęcie pojazdu';
              img.className = 'w-full h-full object-cover';
              imgLink.appendChild(img);
              imgContainer.appendChild(imgLink);
            } else {
              const placeholder = document.createElement('div');
              placeholder.className = 'w-full h-full flex items-center justify-center text-gray-400';
              placeholder.innerHTML = '<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
              imgContainer.appendChild(placeholder);
            }
            
            const content = document.createElement('div');
            content.className = 'p-4';
            
            const titleLink = document.createElement('a');
            titleLink.href = `/offer.php?offer_id=${encodeURIComponent(o.id)}`;
            titleLink.className = 'block mb-2 no-underline';
            const title = document.createElement('h3');
            title.className = 'text-lg font-bold text-slate-900 hover:text-blue-600 transition-colors line-clamp-2';
            title.textContent = o.title || 'Bez tytułu';
            titleLink.appendChild(title);
            content.appendChild(titleLink);
            
            const meta = document.createElement('div');
            meta.className = 'text-sm text-gray-600 mb-2';
            meta.textContent = (o.brand_name || '') + ' ' + (o.model_name || '') + ' • ' + (o.production_year || '');
            content.appendChild(meta);
            
            const price = document.createElement('div');
            price.className = 'text-xl font-bold text-blue-600';
            price.textContent = (o.price || 0).toLocaleString('pl-PL') + ' PLN';
            content.appendChild(price);
            
            card.appendChild(imgContainer);
            card.appendChild(content);
            container.appendChild(card);
          });
        } else {
          document.getElementById('recentOffers').innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">Nie udało się załadować ofert</div>';
        }
      } catch (err) {
        document.getElementById('recentOffers').innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Błąd: ' + err.message + '</div>';
      }
    })();
  </script>
</body>
</html>

