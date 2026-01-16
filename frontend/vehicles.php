<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Vehicles — Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
  <main class="container">
    <h1>Vehicles</h1>

    <div id="authControls">
      <button id="logoutBtn">Logout</button>
    </div>

    <section>
      <label for="brandSelect">Brand</label>
      <select id="brandSelect">
        <option value="">-- choose brand --</option>
      </select>

      <div id="modelsContainer">
        <h2>Models</h2>
        <ul id="modelsList"></ul>
      </div>
    </section>

    <p><a href="/public/index.php">Back</a></p>
  </main>

  <script src="/public/assets/js/api.js"></script>
  <script>
    async function loadBrands() {
  const res = await apiFetch('/api/vehicles/brands.php');
      const select = document.getElementById('brandSelect');
      if (res.ok && Array.isArray(res.data)) {
        res.data.forEach(b => {
          const opt = document.createElement('option');
          opt.value = b.id;
          opt.textContent = b.name;
          select.appendChild(opt);
        });
      } else {
        document.getElementById('modelsList').innerHTML = `<li>${res.error || 'Failed to load brands'}</li>`;
      }
    }

    async function loadModels(brandId) {
  const url = brandId ? `/api/vehicles/models.php?brand_id=${encodeURIComponent(brandId)}` : '/api/vehicles/models.php';
      const res = await apiFetch(url);
      const list = document.getElementById('modelsList');
      list.innerHTML = '';
      if (res.ok && Array.isArray(res.data)) {
        if (res.data.length === 0) {
          list.innerHTML = '<li>No models found</li>';
          return;
        }
        res.data.forEach(m => {
          const li = document.createElement('li');
          li.textContent = m.name;
          list.appendChild(li);
        });
      } else {
        list.innerHTML = `<li>${res.error || 'Failed to load models'}</li>`;
      }
    }

    document.getElementById('brandSelect').addEventListener('change', (e) => {
      loadModels(e.target.value);
    });

    document.getElementById('logoutBtn').addEventListener('click', async () => {
  const res = await apiFetch('/api/login/logout.php', { method: 'POST' });
      if (res.ok) {
        window.location.href = '/public/index.php';
      } else {
        alert(res.error || 'Logout failed');
      }
    });

    // init
    loadBrands();
    loadModels();
  </script>
</body>
</html>
