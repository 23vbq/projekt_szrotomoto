<?php
$pageTitle = 'Oferta - Szrotomoto';
include __DIR__ . '/_partials/head.php';
?>
  <?php include __DIR__ . '/_partials/nav.php'; ?>
  <main class="max-w-5xl mx-auto my-8 px-4 sm:px-6 lg:px-8">
    <div id="loading" class="text-center py-12">
      <p class="text-gray-500">Ładowanie oferty...</p>
    </div>

    <div id="offerContent" class="hidden">
      <div class="mb-6">
        <a href="/offers.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-4 no-underline">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          Powrót do ofert
        </a>
        <h1 id="title" class="text-3xl font-bold text-slate-900 mb-2"></h1>
        <div id="meta" class="text-lg text-gray-600 mb-4"></div>
      </div>

      <div id="gallery" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <section id="details" class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm"></section>
        </div>
        
        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm sticky top-24">
            <div id="priceSection" class="mb-4"></div>
            <div id="actions" class="flex flex-col gap-3"></div>
          </div>
        </div>
      </div>
    </div>

    <div id="error" class="hidden bg-red-50 border border-red-200 p-6 rounded-xl text-red-600 text-center">
      <p id="errorMessage"></p>
    </div>
  </main>

  <script>
    function qs(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }

    async function loadOffer() {
      const id = qs('offer_id');
      if (!id) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('errorMessage').textContent = 'Brak ID oferty w parametrach';
        return;
      }

      const res = await apiFetch(`/api/offers/show.php?offer_id=${encodeURIComponent(id)}`);
      if (!res.ok) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('errorMessage').textContent = res.error || 'Nie udało się załadować oferty';
        return;
      }

      const o = res.data;
      document.getElementById('loading').classList.add('hidden');
      document.getElementById('offerContent').classList.remove('hidden');
      
      document.getElementById('title').textContent = o.title || 'Oferta';
      document.getElementById('meta').textContent = `${o.brand_name || ''} ${o.model_name || ''} • ${o.production_year || ''} rok`;

      // Gallery
      const gallery = document.getElementById('gallery');
      gallery.innerHTML = '';
      try {
        const attachments = o.attachments ? JSON.parse(o.attachments) : null;
        if (Array.isArray(attachments) && attachments.length > 0) {
          attachments.forEach(id => {
            const img = document.createElement('img');
            img.src = window.getAttachmentUrl(id);
            img.alt = o.title || 'Zdjęcie pojazdu';
            img.className = 'w-full h-64 object-cover rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer';
            img.onclick = () => window.open(img.src, '_blank');
            gallery.appendChild(img);
          });
        } else {
          gallery.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><p>Brak zdjęć</p></div>';
        }
      } catch (e) {
        gallery.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400">Brak zdjęć</div>';
      }

      // Details
      const details = document.getElementById('details');
      details.innerHTML = '<h2 class="text-xl font-bold text-slate-900 mb-4">Szczegóły oferty</h2>';
      
      const fields = [
        ['Opis', 'description'],
        ['Przebieg', 'odometer', ' km'],
        ['Paliwo', 'fuel_type'],
        ['Skrzynia biegów', 'transmission'],
        ['Pojemność', 'displacement', ' cm³'],
        ['Moc', 'horsepower', ' KM'],
        ['Typ nadwozia', 'body_type'],
        ['Liczba drzwi', 'doors_amount'],
        ['Liczba miejsc', 'seats_amount'],
        ['VIN', 'vin'],
        ['Numer rejestracyjny', 'registration_number'],
        ['Kraj pochodzenia', 'country_of_origin'],
        ['Sprzedawca', 'user_name']
      ];

      fields.forEach(([label, key, suffix = '']) => {
        if (o[key] !== null && o[key] !== undefined && o[key] !== '') {
          const div = document.createElement('div');
          div.className = 'mb-4 pb-4 border-b border-gray-100 last:border-0';
          const labelEl = document.createElement('div');
          labelEl.className = 'text-sm font-medium text-gray-500 mb-1';
          labelEl.textContent = label;
          const valueEl = document.createElement('div');
          valueEl.className = 'text-base text-slate-900 font-medium';
          valueEl.textContent = o[key] + suffix;
          div.appendChild(labelEl);
          div.appendChild(valueEl);
          details.appendChild(div);
        }
      });

      // Price section
      const priceSection = document.getElementById('priceSection');
      priceSection.innerHTML = `
        <div class="text-3xl font-bold text-blue-600 mb-2">${(o.price || 0).toLocaleString('pl-PL')} PLN</div>
        <div class="text-sm text-gray-500">Cena</div>
      `;

      // Actions
      const actions = document.getElementById('actions');
      actions.innerHTML = '';

      if (o.isCurrentUserOwner === true) {
        const setSoldBtn = document.createElement('button');
        setSoldBtn.textContent = 'Oznacz jako sprzedane';
        setSoldBtn.className = 'w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors';
        setSoldBtn.addEventListener('click', async () => {
          if (!confirm('Czy na pewno chcesz oznaczyć tę ofertę jako sprzedaną?')) return;
          const r = await apiFetch(`/api/offers/setAsSold.php?offer_id=${encodeURIComponent(id)}`, { method: 'GET' });
          if (r.ok) {
            alert('Oferta została oznaczona jako sprzedana');
            loadOffer();
          } else {
            alert(r.error || 'Nie udało się oznaczyć oferty jako sprzedanej');
          }
        });

        const removeBtn = document.createElement('button');
        removeBtn.textContent = 'Usuń ofertę';
        removeBtn.className = 'w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors';
        removeBtn.addEventListener('click', async () => {
          if (!confirm('Czy na pewno chcesz usunąć tę ofertę? Ta operacja jest nieodwracalna.')) return;
          const r = await apiFetch(`/api/offers/setAsRemoved.php?offer_id=${encodeURIComponent(id)}`, { method: 'GET' });
          if (r.ok) {
            alert('Oferta została usunięta');
            window.location.href = '/offers.php';
          } else {
            alert(r.error || 'Nie udało się usunąć oferty');
          }
        });

        actions.appendChild(setSoldBtn);
        actions.appendChild(removeBtn);
      }
    }

    loadOffer();
  </script>
</body>
</html>
