<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Offer — Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
  <script src="/public/assets/js/api.js"></script>
</head>
<body>
  <?php include __DIR__ . '/_nav.php'; ?>
  <main class="container">
    <h1 id="title">Offer</h1>
    <div id="meta"></div>
    <div id="gallery"></div>
    <section id="details"></section>

    <div id="actions"></div>

    <p><a href="/public/offers.php">Back to offers</a></p>
  </main>

  <script>
    function qs(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }

    async function loadOffer() {
      const id = qs('offer_id');
      if (!id) {
        document.getElementById('details').textContent = 'No offer_id provided in query string';
        return;
      }

      const res = await apiFetch(`/api/offers/show.php?offer_id=${encodeURIComponent(id)}`);
      if (!res.ok) {
        document.getElementById('details').textContent = res.error || 'Failed to load offer';
        return;
      }

      const o = res.data;
      document.getElementById('title').textContent = o.title || 'Offer';

      const meta = document.getElementById('meta');
      meta.textContent = `${o.brand_name || ''} ${o.model_name || ''} — ${o.production_year || ''} • ${o.price || ''} PLN`;

      const gallery = document.getElementById('gallery');
      gallery.innerHTML = '';
      try {
        const attachments = o.attachments ? JSON.parse(o.attachments) : null;
        if (Array.isArray(attachments)) {
          attachments.forEach(id => {
            const img = document.createElement('img');
            img.src = `/api/attachments/show.php?id=${encodeURIComponent(id)}`;
            img.alt = o.title || 'attachment';
            img.style.maxWidth = '300px';
            img.style.marginRight = '8px';
            gallery.appendChild(img);
          });
        }
      } catch (e) {
        // attachments may be null or not JSON — ignore
      }

      const details = document.getElementById('details');
      details.innerHTML = '';
      const fields = [
        ['Description', 'description'],
        ['Odometer', 'odometer'],
        ['Fuel', 'fuel_type'],
        ['Transmission', 'transmission'],
        ['Displacement', 'displacement'],
        ['Horsepower', 'horsepower'],
        ['Body type', 'body_type'],
        ['Doors', 'doors_amount'],
        ['Seats', 'seats_amount'],
        ['VIN', 'vin'],
        ['Registration', 'registration_number'],
        ['Country', 'country_of_origin'],
        ['Seller', 'user_name']
      ];

      fields.forEach(([label, key]) => {
        const div = document.createElement('div');
        div.innerHTML = `<strong>${label}:</strong> ${o[key] !== null && o[key] !== undefined ? o[key] : ''}`;
        details.appendChild(div);
      });

      const actions = document.getElementById('actions');
      actions.innerHTML = '';

  const setSoldBtn = document.createElement('button');
      setSoldBtn.textContent = 'Set as sold';
  setSoldBtn.className = 'secondary';
      setSoldBtn.addEventListener('click', async () => {
        const r = await apiFetch(`/api/offers/setAsSold.php?offer_id=${encodeURIComponent(id)}`, { method: 'GET' });
        if (r.ok) {
          alert('Offer set as sold');
          loadOffer();
        } else {
          alert(r.error || 'Failed to set as sold');
        }
      });

  const removeBtn = document.createElement('button');
      removeBtn.textContent = 'Remove offer';
  removeBtn.className = 'danger';
      removeBtn.addEventListener('click', async () => {
        const r = await apiFetch(`/api/offers/setAsRemoved.php?offer_id=${encodeURIComponent(id)}`, { method: 'GET' });
        if (r.ok) {
          alert('Offer removed');
          loadOffer();
        } else {
          alert(r.error || 'Failed to remove offer');
        }
      });

      actions.appendChild(setSoldBtn);
      actions.appendChild(removeBtn);
    }

    loadOffer();
  </script>
</body>
</html>
