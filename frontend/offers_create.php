<?php
$pageTitle = 'Dodaj ofertę - Szrotomoto';
include __DIR__ . '/_partials/head.php';
?>
  <?php include __DIR__ . '/_partials/nav.php'; ?>
  <main class="max-w-3xl mx-auto my-8 px-4 sm:px-6 lg:px-8">
    <div id="authCheck" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
      <p class="text-gray-600">Sprawdzanie autoryzacji...</p>
    </div>

    <div id="offerFormContainer" class="hidden">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Dodaj nową ofertę</h1>
        <p class="text-gray-600">Wypełnij formularz, aby dodać swój pojazd</p>
      </div>

      <form id="offerForm" enctype="multipart/form-data" class="bg-white border border-gray-200 p-6 sm:p-8 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-semibold text-slate-900">Marka *</label>
            <select id="brandSelect" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">-- wybierz markę --</option>
              <option value="" disabled id="brandsLoading">Ładowanie marek...</option>
            </select>
          </div>
          
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-semibold text-slate-900">Model *</label>
            <select id="modelSelect" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
              <option value="">-- wybierz najpierw markę --</option>
            </select>
          </div>
          
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-semibold text-slate-900">Tytuł oferty *</label>
            <input name="title" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. BMW 320d w doskonałym stanie">
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Cena (PLN) *</label>
            <input type="number" name="price" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="15000">
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Rok produkcji *</label>
            <input type="number" name="production_year" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="2015">
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Przebieg (km) *</label>
            <input type="number" name="odometer" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="120000">
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Typ paliwa *</label>
            <select name="fuel_type" id="fuelSelect" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Skrzynia biegów *</label>
            <select name="transmission" id="transmissionSelect" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
          </div>
          
          <div>
            <label class="block mb-2 text-sm font-semibold text-slate-900">Typ nadwozia *</label>
            <select name="body_type" id="bodySelect" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
          </div>
          
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-semibold text-slate-900">VIN *</label>
            <input name="vin" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="WBADT43452G915989">
          </div>
          
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-semibold text-slate-900">Zdjęcia</label>
            <input type="file" name="files[]" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-2 text-sm text-gray-500">Możesz wybrać wiele plików. Maksymalny rozmiar: 10MB na plik</p>
          </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex gap-4">
          <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
            Dodaj ofertę
          </button>
          <a href="/offers.php" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors no-underline flex items-center">
            Anuluj
          </a>
        </div>
      </form>

      <div id="message" role="status" class="mt-4 p-4 rounded-lg hidden"></div>
    </div>
  </main>

  <script>
    // Check authentication before showing the form
    (async function() {
      try {
        const res = await window.apiFetch('/api/login/me.php', { method: 'GET' });
        
        if (!res.ok || !res.data || !res.data.authenticated) {
          // User is not authenticated, redirect to home
          window.location.href = '/index.php';
          return;
        }
        
        // User is authenticated, show the form
        document.getElementById('authCheck').classList.add('hidden');
        document.getElementById('offerFormContainer').classList.remove('hidden');
        
        // Initialize form functionality
        fetchValues();
      } catch (error) {
        console.error('Error checking authentication:', error);
        window.location.href = '/index.php';
      }
    })();

    async function fetchValues() {
      try {
        const [fuelRes, transRes, bodyRes, brandsRes] = await Promise.all([
          apiFetch('/api/values/fuelType.php'),
          apiFetch('/api/values/transmissionType.php'),
          apiFetch('/api/values/bodyType.php'),
          apiFetch('/api/vehicles/brands.php')
        ]);

        // Handle fuel types
        const fuelSelect = document.getElementById('fuelSelect');
        if (fuelRes.ok && Array.isArray(fuelRes.data)) {
          fuelRes.data.forEach(v => { 
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            fuelSelect.appendChild(opt);
          });
        } else {
          console.error('Failed to load fuel types:', fuelRes.error);
        }

        // Handle transmission types
        const transSelect = document.getElementById('transmissionSelect');
        if (transRes.ok && Array.isArray(transRes.data)) {
          transRes.data.forEach(v => { 
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            transSelect.appendChild(opt);
          });
        } else {
          console.error('Failed to load transmission types:', transRes.error);
        }

        // Handle body types
        const bodySelect = document.getElementById('bodySelect');
        if (bodyRes.ok && Array.isArray(bodyRes.data)) {
          bodyRes.data.forEach(v => { 
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            bodySelect.appendChild(opt);
          });
        } else {
          console.error('Failed to load body types:', bodyRes.error);
        }

        // Handle brands
        const brandSelect = document.getElementById('brandSelect');
        const brandsLoading = document.getElementById('brandsLoading');
        if (brandsLoading) {
          brandsLoading.remove();
        }
        
        if (brandsRes.ok && Array.isArray(brandsRes.data)) {
          if (brandsRes.data.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Brak dostępnych marek';
            opt.disabled = true;
            brandSelect.appendChild(opt);
          } else {
            brandsRes.data.forEach(b => { 
              const opt = document.createElement('option');
              opt.value = b.id;
              opt.textContent = b.name;
              brandSelect.appendChild(opt);
            });
          }
        } else {
          console.error('Failed to load brands:', brandsRes.error, brandsRes);
          const opt = document.createElement('option');
          opt.value = '';
          opt.textContent = 'Błąd ładowania marek';
          opt.disabled = true;
          brandSelect.appendChild(opt);
          showMessage('Nie udało się załadować marek pojazdów: ' + (brandsRes.error || 'Nieznany błąd'), 'error');
        }
      } catch (error) {
        console.error('Error fetching values:', error);
        showMessage('Błąd podczas ładowania danych: ' + error.message, 'error');
      }
    }

    document.getElementById('brandSelect').addEventListener('change', async (e) => {
      const brandId = e.target.value;
      const modelSelect = document.getElementById('modelSelect');
      
      if (!brandId) {
        modelSelect.innerHTML = '<option value="">-- wybierz najpierw markę --</option>';
        modelSelect.disabled = true;
        return;
      }
      
      modelSelect.disabled = true;
      modelSelect.innerHTML = '<option value="">Ładowanie modeli...</option>';
      
      try {
        const res = await apiFetch(`/api/vehicles/models.php?brand_id=${encodeURIComponent(brandId)}`);
        modelSelect.innerHTML = '';
        
        if (res.ok && Array.isArray(res.data)) {
          if (res.data.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Brak modeli dla tej marki';
            opt.disabled = true;
            modelSelect.appendChild(opt);
          } else {
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- wybierz model --';
            modelSelect.appendChild(defaultOpt);
            
            res.data.forEach(m => { 
              const opt = document.createElement('option');
              opt.value = m.id;
              opt.textContent = m.name;
              modelSelect.appendChild(opt);
            });
            modelSelect.disabled = false;
          }
        } else {
          console.error('Failed to load models:', res.error, res);
          const opt = document.createElement('option');
          opt.value = '';
          opt.textContent = 'Błąd ładowania modeli';
          opt.disabled = true;
          modelSelect.appendChild(opt);
          showMessage('Nie udało się załadować modeli: ' + (res.error || 'Nieznany błąd'), 'error');
        }
      } catch (error) {
        console.error('Error fetching models:', error);
        modelSelect.innerHTML = '<option value="">Błąd ładowania</option>';
        showMessage('Błąd podczas ładowania modeli: ' + error.message, 'error');
      }
    });

    document.getElementById('offerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.currentTarget;
      const formData = new FormData(form);
      const modelId = document.getElementById('modelSelect').value;
      if (!modelId) { 
        showMessage('Proszę wybrać model.', 'error');
        return; 
      }
      formData.set('model_id', modelId);

      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Dodawanie...';

      const res = await apiFetch('/api/offers/create.php', { method: 'POST', body: formData });
      if (res.ok) {
        showMessage('Oferta została pomyślnie dodana!', 'success');
        setTimeout(() => window.location.href = '/offers.php', 1500);
      } else {
        showMessage(res.error || 'Nie udało się dodać oferty', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Dodaj ofertę';
      }
    });

    function showMessage(text, type) {
      const msgEl = document.getElementById('message');
      msgEl.textContent = text;
      msgEl.className = `mt-4 p-4 rounded-lg ${type === 'error' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-green-50 text-green-600 border border-green-200'}`;
      msgEl.classList.remove('hidden');
    }
  </script>
</body>
</html>
