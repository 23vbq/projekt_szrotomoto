// Minimal API helper used by the server-rendered pages in /public
async function apiFetch(path, opts = {}) {
  const options = Object.assign({
    credentials: 'same-origin',
    headers: {}
  }, opts);

  // If body is URLSearchParams or FormData, leave it as-is. JSON isn't used by backend for POSTs.
  try {
    const res = await fetch(path, options);
    const contentType = res.headers.get('Content-Type') || '';
    let data = null;
    if (contentType.includes('application/json')) {
      data = await res.json();
    } else {
      data = await res.text();
    }

    if (!res.ok) {
      return { ok: false, status: res.status, error: data && data.message ? data.message : (typeof data === 'string' ? data : 'Unknown error') };
    }

    return { ok: true, status: res.status, data };
  } catch (err) {
    return { ok: false, status: 0, error: err.message };
  }
}

// Export for inline scripts
window.apiFetch = apiFetch;
