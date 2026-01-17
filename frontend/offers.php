<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Offers — Szrotomoto</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
  <script src="/public/assets/js/api.js"></script>
</head>
<body>
  <?php include __DIR__ . '/_nav.php'; ?>
  <main class="container">
    <h1>Offers</h1>

    <p><a href="/public/offers_create.php">Create new offer</a></p>

    <section id="offersContainer">
      <ul id="offersList"></ul>
    </section>

  <p><a href="/public/offers.php">Back</a></p>
  </main>

  <script>
    async function loadOffers() {
      const res = await apiFetch('/api/offers/search.php');
      const list = document.getElementById('offersList');
      list.innerHTML = '';
      if (!res.ok) {
        list.innerHTML = `<li>${res.error || 'Failed to load offers'}</li>`;
        return;
      }

      if (!Array.isArray(res.data) || res.data.length === 0) {
        list.innerHTML = '<li>No offers found</li>';
        return;
      }

      res.data.forEach(o => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = `/public/offer.php?offer_id=${encodeURIComponent(o.id)}`;
        a.textContent = o.title + ' — ' + (o.brand_name || '') + ' ' + (o.model_name || '');
        a.style.fontWeight = '600';
        li.appendChild(a);
        const meta = document.createElement('div');
        meta.textContent = `${o.production_year || ''} • ${o.price || ''} PLN`;
        li.appendChild(meta);

        if (o.attachment_id) {
          const img = document.createElement('img');
          img.src = `/api/attachments/show.php?id=${encodeURIComponent(o.attachment_id)}`;
          img.alt = o.title || 'attachment';
          img.style.maxWidth = '200px';
          img.style.display = 'block';
          img.style.marginTop = '6px';
          // wrap image with link to detail page
          const imgLink = document.createElement('a');
          imgLink.href = `/public/offer.php?offer_id=${encodeURIComponent(o.id)}`;
          imgLink.appendChild(img);
          li.appendChild(imgLink);
        }

        list.appendChild(li);
      });
    }

    loadOffers();
  </script>
</body>
</html>
