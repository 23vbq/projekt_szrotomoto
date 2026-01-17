<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Offer - Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
  <script src="/public/assets/js/api.js"></script>
</head>
<body>
  <?php include __DIR__ . '/_nav.php'; ?>
  <main class="container">
    <h1>Create Offer</h1>

    <form id="offerForm" enctype="multipart/form-data">
      <label>Brand<br>
        <select id="brandSelect"><option value="">-- choose brand --</option></select>
      </label>
      <label>Model<br>
        <select id="modelSelect"><option value="">-- choose model --</option></select>
      </label>
      <label>Title<br><input name="title" required></label>
      <label>Price (PLN)<br><input type="number" name="price" required></label>
      <label>Production year<br><input type="number" name="production_year" required></label>
      <label>Odometer (km)<br><input type="number" name="odometer" required></label>
      <label>Fuel type<br>
        <select name="fuel_type" id="fuelSelect"></select>
      </label>
      <label>Transmission<br>
        <select name="transmission" id="transmissionSelect"></select>
      </label>
      <label>Body type<br>
        <select name="body_type" id="bodySelect"></select>
      </label>
      <label>VIN<br><input name="vin" required></label>

      <label>Images<br><input type="file" name="files[]" multiple accept="image/*"></label>

      <button type="submit">Create</button>
    </form>

    <div id="message" role="status"></div>
    <p><a href="/public/offers.php">Back</a></p>
  </main>

  <script>
    async function fetchValues() {
      const [fuelRes, transRes, bodyRes, brandsRes] = await Promise.all([
        apiFetch('/api/values/fuelType.php'),
        apiFetch('/api/values/transmissionType.php'),
        apiFetch('/api/values/bodyType.php'),
        apiFetch('/api/vehicles/brands.php')
      ]);

      const fuelSelect = document.getElementById('fuelSelect');
      if (fuelRes.ok && Array.isArray(fuelRes.data)) {
        fuelRes.data.forEach(v => { fuelSelect.appendChild(Object.assign(document.createElement('option'), { value: v, textContent: v })); });
      }

      const transSelect = document.getElementById('transmissionSelect');
      if (transRes.ok && Array.isArray(transRes.data)) {
        transRes.data.forEach(v => { transSelect.appendChild(Object.assign(document.createElement('option'), { value: v, textContent: v })); });
      }

      const bodySelect = document.getElementById('bodySelect');
      if (bodyRes.ok && Array.isArray(bodyRes.data)) {
        bodyRes.data.forEach(v => { bodySelect.appendChild(Object.assign(document.createElement('option'), { value: v, textContent: v })); });
      }

      const brandSelect = document.getElementById('brandSelect');
      if (brandsRes.ok && Array.isArray(brandsRes.data)) {
        brandsRes.data.forEach(b => { const opt = document.createElement('option'); opt.value = b.id; opt.textContent = b.name; brandSelect.appendChild(opt); });
      }
    }

    document.getElementById('brandSelect').addEventListener('change', async (e) => {
      const brandId = e.target.value;
      const modelSelect = document.getElementById('modelSelect');
      modelSelect.innerHTML = '<option value="">-- choose model --</option>';
      if (!brandId) return;
      const res = await apiFetch(`/api/vehicles/models.php?brand_id=${encodeURIComponent(brandId)}`);
      if (res.ok && Array.isArray(res.data)) {
        res.data.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = m.name; modelSelect.appendChild(opt); });
      }
    });

    document.getElementById('offerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.currentTarget;
      const formData = new FormData(form);
      // model_id is required by backend; ensure it's passed
      const modelId = document.getElementById('modelSelect').value;
      if (!modelId) { document.getElementById('message').textContent = 'Please choose a model.'; return; }
      formData.set('model_id', modelId);

      const res = await apiFetch('/api/offers/create.php', { method: 'POST', body: formData });
      if (res.ok) {
        document.getElementById('message').textContent = res.data.message || 'Offer created';
        setTimeout(() => window.location.href = '/public/offers.php', 900);
      } else {
        document.getElementById('message').textContent = res.error || 'Failed to create offer';
      }
    });

    fetchValues();
  </script>
</body>
</html>
