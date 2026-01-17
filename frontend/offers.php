<?php
$pageTitle = 'Oferty - Szrotomoto';
include __DIR__ . '/_partials/head.php';
?>
  <?php include __DIR__ . '/_partials/nav.php'; ?>
  <main class="max-w-6xl mx-auto my-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-slate-900 mb-2">Oferty pojazdów</h1>
      <p class="text-gray-600">Znajdź swój wymarzony pojazd</p>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-6">
      <div class="relative">
        <input 
          type="text" 
          id="searchInput" 
          placeholder="Szukaj po tytule, opisie, marce, modelu, VIN lub numerze rejestracyjnym..." 
          class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
        >
        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <button 
          id="clearSearch" 
          class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
          title="Wyczyść wyszukiwanie"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
      <a href="/offers_create.php" id="createOfferBtn" class="hidden inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg no-underline">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Dodaj nową ofertę
      </a>

      <div class="flex flex-wrap gap-3 items-center">
        <div class="flex items-center gap-2">
          <label for="sortSelect" class="text-sm font-medium text-gray-700 whitespace-nowrap">Sortuj:</label>
          <select id="sortSelect" class="px-4 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <option value="">-- bez sortowania --</option>
            <option value="price_asc">Cena: od najniższej</option>
            <option value="price_desc">Cena: od najwyższej</option>
            <option value="odometer_asc">Przebieg: od najniższego</option>
            <option value="odometer_desc">Przebieg: od najwyższego</option>
            <option value="year_asc">Rok produkcji: od najstarszego</option>
            <option value="year_desc">Rok produkcji: od najnowszego</option>
          </select>
        </div>

        <button id="filtersToggle" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
          Filtry ▾
        </button>
      </div>
    </div>

    <div id="filtersPanel" class="hidden mb-6 bg-white p-5 border border-gray-200 rounded-xl shadow-sm">
      <h3 class="text-lg font-semibold text-slate-900 mb-4">Filtry wyszukiwania</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <div>
          <label for="brandSelect" class="block mb-2 text-sm font-medium text-gray-700">Marka</label>
          <select id="brandSelect" class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"><option value="">-- dowolna --</option></select>
        </div>
        <div>
          <label for="modelSelect" class="block mb-2 text-sm font-medium text-gray-700">Model</label>
          <select id="modelSelect" class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"><option value="">-- dowolny --</option></select>
        </div>
        <div>
          <label for="fuelSelect" class="block mb-2 text-sm font-medium text-gray-700">Paliwo</label>
          <select id="fuelSelect" class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"><option value="">-- dowolne --</option></select>
        </div>
        <div>
          <label for="transmissionSelect" class="block mb-2 text-sm font-medium text-gray-700">Skrzynia biegów</label>
          <select id="transmissionSelect" class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"><option value="">-- dowolna --</option></select>
        </div>
        <div>
          <label for="bodySelect" class="block mb-2 text-sm font-medium text-gray-700">Typ nadwozia</label>
          <select id="bodySelect" class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"><option value="">-- dowolny --</option></select>
        </div>
      </div>
      <div class="mt-4 flex gap-3">
        <button id="applyFilters" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">Zastosuj</button>
        <button id="clearFilters" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">Wyczyść</button>
      </div>
    </div>

    <section id="offersContainer">
      <ul id="offersList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></ul>
    </section>
  </main>

  <script>
    // Check authentication and show/hide create offer button
    (async function() {
      try {
        const authRes = await window.apiFetch('/api/login/me.php', { method: 'GET' });
        const createOfferBtn = document.getElementById('createOfferBtn');
        
        if (authRes.ok && authRes.data && authRes.data.authenticated) {
          // User is authenticated, show create offer button
          if (createOfferBtn) createOfferBtn.classList.remove('hidden');
        } else {
          // User is not authenticated, hide create offer button
          if (createOfferBtn) createOfferBtn.classList.add('hidden');
        }
      } catch (error) {
        console.error('Error checking authentication:', error);
        // On error, hide button
        const createOfferBtn = document.getElementById('createOfferBtn');
        if (createOfferBtn) createOfferBtn.classList.add('hidden');
      }
    })();

    // Client-side filtering and sorting for offers
    (function(){
      let offersCache = [];
      let currentSearchTerm = '';
      const brandsMap = new Map(); // id -> name
      let searchTimeout = null;

      function renderOffers(arr) {
        const list = document.getElementById('offersList');
        list.innerHTML = '';
        if (!Array.isArray(arr) || arr.length === 0) {
          list.innerHTML = '<li class="col-span-full bg-white border border-gray-200 p-8 rounded-xl text-center text-gray-500"><p class="text-lg">Nie znaleziono ofert</p><p class="text-sm mt-2">Spróbuj zmienić filtry wyszukiwania</p></li>';
          return;
        }

        arr.forEach(o => {
          const li = document.createElement('li');
          li.className = 'bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-200';
          
          // Image container
          const imgContainer = document.createElement('div');
          imgContainer.className = 'relative h-48 bg-gray-100';
          
          if (o.attachment_id) {
            const imgLink = document.createElement('a');
            imgLink.href = `/offer.php?offer_id=${encodeURIComponent(o.id)}`;
            const img = document.createElement('img');
            img.src = window.getAttachmentUrl ? window.getAttachmentUrl(o.attachment_id) : `http://localhost:3000/api/attachments/show.php?id=${encodeURIComponent(o.attachment_id)}`;
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
          
          // Content container
          const contentDiv = document.createElement('div');
          contentDiv.className = 'p-4';
          
          const a = document.createElement('a');
          a.href = `/offer.php?offer_id=${encodeURIComponent(o.id)}`;
          a.className = 'block mb-2';
          const title = document.createElement('h3');
          title.className = 'text-lg font-bold text-slate-900 hover:text-blue-600 transition-colors line-clamp-2';
          title.textContent = o.title || 'Bez tytułu';
          a.appendChild(title);
          
          const brandModel = document.createElement('p');
          brandModel.className = 'text-sm text-gray-600 mb-2';
          brandModel.textContent = (o.brand_name || '') + ' ' + (o.model_name || '');
          a.appendChild(brandModel);
          contentDiv.appendChild(a);
          
          const meta = document.createElement('div');
          meta.className = 'flex flex-wrap gap-2 text-sm text-gray-500 mb-3';
          if (o.production_year) {
            const year = document.createElement('span');
            year.className = 'px-2 py-1 bg-gray-100 rounded';
            year.textContent = o.production_year;
            meta.appendChild(year);
          }
          if (o.odometer) {
            const km = document.createElement('span');
            km.className = 'px-2 py-1 bg-gray-100 rounded';
            km.textContent = o.odometer.toLocaleString('pl-PL') + ' km';
            meta.appendChild(km);
          }
          contentDiv.appendChild(meta);
          
          const price = document.createElement('div');
          price.className = 'text-2xl font-bold text-blue-600';
          price.textContent = (o.price || 0).toLocaleString('pl-PL') + ' PLN';
          contentDiv.appendChild(price);
          
          li.appendChild(imgContainer);
          li.appendChild(contentDiv);
          list.appendChild(li);
        });
      }

      function applyFiltersAndSort() {
        let filtered = offersCache.slice();

        const brandId = document.getElementById('brandSelect').value;
        const model = document.getElementById('modelSelect').value;
        const fuel = document.getElementById('fuelSelect').value;
        const transmission = document.getElementById('transmissionSelect').value;
        const body = document.getElementById('bodySelect').value;
        const sort = document.getElementById('sortSelect').value;

        if (brandId) {
          const brandName = brandsMap.get(brandId) || '';
          filtered = filtered.filter(o => (o.brand_name || '') === brandName);
        }
        if (model) filtered = filtered.filter(o => (o.model_name || '') === model);
        if (fuel) filtered = filtered.filter(o => (o.fuel_type || '') === fuel);
        if (transmission) filtered = filtered.filter(o => (o.transmission || '') === transmission);
        if (body) filtered = filtered.filter(o => (o.body_type || '') === body);

        if (sort) {
          const [key, dir] = sort.split('_');
          const multiplier = dir === 'asc' ? 1 : -1;
          filtered.sort((a,b) => {
            const aVal = Number(a[key] || 0);
            const bVal = Number(b[key] || 0);
            return (aVal - bVal) * multiplier;
          });
        }

        renderOffers(filtered);
      }

      async function loadOffers(searchTerm = '') {
        const searchUrl = searchTerm 
          ? `/api/offers/search.php?search=${encodeURIComponent(searchTerm)}`
          : '/api/offers/search.php';
        
        const offersRes = await apiFetch(searchUrl);
        
        if (!offersRes.ok) {
          document.getElementById('offersList').innerHTML = `<li class="col-span-full bg-red-50 border border-red-200 p-4 rounded-xl text-center text-red-600">Błąd ładowania ofert: ${offersRes.error || ''}</li>`;
          return [];
        }
        
        return Array.isArray(offersRes.data) ? offersRes.data : [];
      }

      async function loadInitial() {
        // Fetch offers and value lists in parallel
        const [offersRes, brandsRes, fuelRes, transRes, bodyRes] = await Promise.all([
          loadOffers(),
          apiFetch('/api/vehicles/brands.php'),
          apiFetch('/api/values/fuelType.php'),
          apiFetch('/api/values/transmissionType.php'),
          apiFetch('/api/values/bodyType.php')
        ]);

        offersCache = offersRes;

        // Populate brands
        if (brandsRes.ok && Array.isArray(brandsRes.data)) {
          const brandSelect = document.getElementById('brandSelect');
          brandsRes.data.forEach(b => {
            const opt = document.createElement('option');
            opt.value = String(b.id);
            opt.textContent = b.name;
            brandSelect.appendChild(opt);
            brandsMap.set(String(b.id), b.name);
          });
        }

        // Populate fuels
        if (fuelRes.ok && Array.isArray(fuelRes.data)) {
          const s = document.getElementById('fuelSelect');
          fuelRes.data.forEach(f => {
            const opt = document.createElement('option');
            opt.value = f;
            opt.textContent = f;
            s.appendChild(opt);
          });
        }

        // Populate transmissions
        if (transRes.ok && Array.isArray(transRes.data)) {
          const s = document.getElementById('transmissionSelect');
          transRes.data.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            s.appendChild(opt);
          });
        }

        // Populate body types
        if (bodyRes.ok && Array.isArray(bodyRes.data)) {
          const s = document.getElementById('bodySelect');
          bodyRes.data.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b;
            opt.textContent = b;
            s.appendChild(opt);
          });
        }

        // Wire brand -> models cascade
        document.getElementById('brandSelect').addEventListener('change', async function(){
          const brandId = this.value;
          const modelSelect = document.getElementById('modelSelect');
          modelSelect.innerHTML = '<option value="">-- dowolny --</option>';
          if (!brandId) return;
          const modelsRes = await apiFetch(`/api/vehicles/models.php?brand_id=${encodeURIComponent(brandId)}`);
          if (!modelsRes.ok) return;
          modelsRes.data.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.name;
            opt.textContent = m.name;
            modelSelect.appendChild(opt);
          });
        });

        // Wire control buttons
        document.getElementById('filtersToggle').addEventListener('click', function(){
          const panel = document.getElementById('filtersPanel');
          const isVisible = !panel.classList.contains('hidden');
          if (isVisible) {
            panel.classList.add('hidden');
            this.textContent = 'Filtry ▾';
          } else {
            panel.classList.remove('hidden');
            this.textContent = 'Filtry ▴';
          }
        });

        document.getElementById('applyFilters').addEventListener('click', applyFiltersAndSort);
        document.getElementById('clearFilters').addEventListener('click', function(){
          document.getElementById('brandSelect').value = '';
          document.getElementById('modelSelect').innerHTML = '<option value="">-- dowolny --</option>';
          document.getElementById('fuelSelect').value = '';
          document.getElementById('transmissionSelect').value = '';
          document.getElementById('bodySelect').value = '';
          document.getElementById('sortSelect').value = '';
          
          // Also clear search if active
          if (currentSearchTerm) {
            document.getElementById('searchInput').value = '';
            currentSearchTerm = '';
            document.getElementById('clearSearch').classList.add('hidden');
          }
          
          renderOffers(offersCache.slice());
        });

        document.getElementById('sortSelect').addEventListener('change', applyFiltersAndSort);

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.trim();
          currentSearchTerm = searchTerm;
          
          // Show/hide clear button
          if (searchTerm) {
            clearSearchBtn.classList.remove('hidden');
          } else {
            clearSearchBtn.classList.add('hidden');
          }
          
          // Debounce search - wait 500ms after user stops typing
          if (searchTimeout) {
            clearTimeout(searchTimeout);
          }
          
          searchTimeout = setTimeout(async () => {
            // Show loading state
            document.getElementById('offersList').innerHTML = '<li class="col-span-full bg-white border border-gray-200 p-8 rounded-xl text-center text-gray-500"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div><p>Wyszukiwanie...</p></li>';
            
            // Load offers with search
            const offers = await loadOffers(searchTerm);
            offersCache = offers;
            
            // Apply client-side filters and sort
            applyFiltersAndSort();
          }, 500);
        });
        
        // Clear search
        clearSearchBtn.addEventListener('click', async function() {
          searchInput.value = '';
          currentSearchTerm = '';
          this.classList.add('hidden');
          
          // Show loading state
          document.getElementById('offersList').innerHTML = '<li class="col-span-full bg-white border border-gray-200 p-8 rounded-xl text-center text-gray-500"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div><p>Ładowanie...</p></li>';
          
          // Reload all offers
          const offers = await loadOffers();
          offersCache = offers;
          
          // Apply client-side filters and sort
          applyFiltersAndSort();
        });

        // Initial render
        renderOffers(offersCache.slice());
      }

      // Start
      loadInitial().catch(err => {
        document.getElementById('offersList').innerHTML = `<li class="col-span-full bg-red-50 border border-red-200 p-4 rounded-xl text-center text-red-600">Błąd inicjalizacji: ${err.message}</li>`;
      });
    })();
  </script>
</body>
</html>
