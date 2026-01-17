<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Offers - Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
  <script src="/public/assets/js/api.js"></script>
</head>
<body>
  <?php include __DIR__ . '/_nav.php'; ?>
  <main class="container">
    <h1>Offers</h1>
    
    <p><a href="/public/offers_create.php">Create new offer</a></p>

    <div class="offers-controls" style="margin:14px 0; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      <div>
        <label for="sortSelect">Sort</label>
        <select id="sortSelect">
          <option value="">-- no sorting --</option>
          <option value="price_asc">Price: low → high</option>
          <option value="price_desc">Price: high → low</option>
          <option value="odometer_asc">Mileage: low → high</option>
          <option value="odometer_desc">Mileage: high → low</option>
          <option value="year_asc">Production year: low → high</option>
          <option value="year_desc">Production year: high → low</option>
        </select>
      </div>

      <div>
        <button id="filtersToggle" class="secondary">Filters ▾</button>
      </div>

      <div id="filtersPanel" style="display:none; background:var(--card); padding:12px; border:1px solid #eef2f6; border-radius:8px;">
        <div style="display:flex; gap:10px; align-items:end; flex-wrap:wrap; max-width:760px">
          <div style="min-width:160px">
            <label for="brandSelect">Brand</label>
            <select id="brandSelect"><option value="">-- any --</option></select>
          </div>
          <div style="min-width:160px">
            <label for="modelSelect">Model</label>
            <select id="modelSelect"><option value="">-- any --</option></select>
          </div>
          <div style="min-width:160px">
            <label for="fuelSelect">Fuel</label>
            <select id="fuelSelect"><option value="">-- any --</option></select>
          </div>
          <div style="min-width:160px">
            <label for="transmissionSelect">Transmission</label>
            <select id="transmissionSelect"><option value="">-- any --</option></select>
          </div>
          <div style="min-width:160px">
            <label for="bodySelect">Body type</label>
            <select id="bodySelect"><option value="">-- any --</option></select>
          </div>
        </div>
        <div style="margin-top:8px; display:flex; gap:8px;">
          <button id="applyFilters" class="primary">Apply</button>
          <button id="clearFilters" class="secondary">Clear</button>
        </div>
      </div>

    </div>

    <section id="offersContainer">
      <ul id="offersList"></ul>
    </section>

  <p><a href="/public/offers.php">Back</a></p>
  </main>

  <script>
    // Client-side filtering and sorting for offers
    (function(){
      let offersCache = [];
      const brandsMap = new Map(); // id -> name

      function renderOffers(arr) {
        const list = document.getElementById('offersList');
        list.innerHTML = '';
        if (!Array.isArray(arr) || arr.length === 0) {
          list.innerHTML = '<li>No offers found</li>';
          return;
        }

        arr.forEach(o => {
          const li = document.createElement('li');
          li.className = 'offer-card';
          const a = document.createElement('a');
          a.href = `/public/offer.php?offer_id=${encodeURIComponent(o.id)}`;
          a.textContent = o.title + ' — ' + (o.brand_name || '') + ' ' + (o.model_name || '');
          a.style.fontWeight = '600';
          li.appendChild(a);
          const meta = document.createElement('div');
          meta.className = 'muted';
          meta.textContent = `${o.production_year || ''} • ${o.odometer || ''} km • ${o.price || ''} PLN`;
          li.appendChild(meta);

          if (o.attachment_id) {
            const img = document.createElement('img');
            img.src = `/api/attachments/show.php?id=${encodeURIComponent(o.attachment_id)}`;
            img.alt = o.title || 'attachment';
            img.className = 'offer-thumb';
            // wrap image with link to detail page
            const imgLink = document.createElement('a');
            imgLink.href = `/public/offer.php?offer_id=${encodeURIComponent(o.id)}`;
            imgLink.appendChild(img);
            li.appendChild(imgLink);
          }

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

      async function loadInitial() {
        // Fetch offers and value lists in parallel
        const [offersRes, brandsRes, fuelRes, transRes, bodyRes] = await Promise.all([
          apiFetch('/api/offers/search.php'),
          apiFetch('/api/vehicles/brands.php'),
          apiFetch('/api/values/fuelType.php'),
          apiFetch('/api/values/transmissionType.php'),
          apiFetch('/api/values/bodyType.php')
        ]);

        if (!offersRes.ok) return document.getElementById('offersList').innerHTML = `<li>Error loading offers: ${offersRes.error || ''}</li>`;
        offersCache = Array.isArray(offersRes.data) ? offersRes.data : [];

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
          modelSelect.innerHTML = '<option value="">-- any --</option>';
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
          const visible = panel.style.display !== 'none';
          panel.style.display = visible ? 'none' : 'block';
          this.textContent = visible ? 'Filters ▾' : 'Filters ▴';
        });

        document.getElementById('applyFilters').addEventListener('click', applyFiltersAndSort);
        document.getElementById('clearFilters').addEventListener('click', function(){
          document.getElementById('brandSelect').value = '';
          document.getElementById('modelSelect').innerHTML = '<option value="">-- any --</option>';
          document.getElementById('fuelSelect').value = '';
          document.getElementById('transmissionSelect').value = '';
          document.getElementById('bodySelect').value = '';
          document.getElementById('sortSelect').value = '';
          renderOffers(offersCache.slice());
        });

        document.getElementById('sortSelect').addEventListener('change', applyFiltersAndSort);

        // Initial render
        renderOffers(offersCache.slice());
      }

      // Start
      loadInitial().catch(err => {
        document.getElementById('offersList').innerHTML = `<li>Error initializing offers: ${err.message}</li>`;
      });
    })();
  </script>
</body>
</html>
