/**
 * Enhanced API Fetch Utility
 * Provides centralized API communication with improved error handling, timeouts, and retry logic
 */

const API_CONFIG = {
  baseURL: 'http://localhost:3000/api',
  timeout: 30000, // 30 seconds
  retries: 1,
  retryDelay: 1000, // 1 second
};

/**
 * Get error message from response data
 */
function getErrorMessage(data, defaultMessage = 'Wystąpił nieoczekiwany błąd') {
  if (!data) return defaultMessage;
  
  if (typeof data === 'string') {
    return data || defaultMessage;
  }
  
  if (data.message) {
    return data.message;
  }
  
  if (data.error) {
    return data.error;
  }
  
  return defaultMessage;
}

/**
 * Get user-friendly error message based on HTTP status
 */
function getStatusErrorMessage(status) {
  const messages = {
    400: 'Nieprawidłowe żądanie',
    401: 'Wymagane logowanie',
    403: 'Brak uprawnień',
    404: 'Nie znaleziono',
    409: 'Konflikt danych',
    422: 'Błąd walidacji',
    500: 'Błąd serwera',
    503: 'Serwis niedostępny',
  };
  
  return messages[status] || `Błąd HTTP ${status}`;
}

/**
 * Create timeout promise
 */
function createTimeoutPromise(timeout) {
  return new Promise((_, reject) => {
    setTimeout(() => reject(new Error('Przekroczono limit czasu żądania')), timeout);
  });
}

/**
 * Enhanced API fetch with timeout, retry, and better error handling
 * @param {string} path - API endpoint path (will be prefixed with /api if not absolute)
 * @param {object} opts - Fetch options
 * @returns {Promise<{ok: boolean, status: number, data: any, error: string}>}
 */
async function apiFetch(path, opts = {}) {
  // Normalize path - if path is already a full URL, use it; otherwise prepend baseURL
  let normalizedPath;
  if (path.startsWith('http://') || path.startsWith('https://')) {
    normalizedPath = path;
  } else if (path.startsWith('/api/')) {
    // If path already starts with /api/, remove it and use baseURL (which already has /api)
    normalizedPath = API_CONFIG.baseURL + path.substring(4); // Remove '/api' from start
  } else if (path.startsWith('/')) {
    normalizedPath = API_CONFIG.baseURL + path;
  } else {
    normalizedPath = `${API_CONFIG.baseURL}/${path}`;
  }
  
  // Merge options
  const options = {
    credentials: 'include', // Include cookies for cross-origin requests (frontend:80, backend:3000)
    headers: {},
    ...opts,
  };

  // Set Content-Type for JSON if body is an object (not FormData or URLSearchParams)
  if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData) && !(options.body instanceof URLSearchParams)) {
    options.headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(options.body);
  }

  let lastError = null;
  let attempts = 0;
  const maxAttempts = API_CONFIG.retries + 1;

  while (attempts < maxAttempts) {
    attempts++;
    
    try {
      // Create race between fetch and timeout
      const fetchPromise = fetch(normalizedPath, options);
      const timeoutPromise = createTimeoutPromise(API_CONFIG.timeout);
      
      const res = await Promise.race([fetchPromise, timeoutPromise]);
      
      // Determine content type and parse response
      const contentType = res.headers.get('Content-Type') || '';
      let data = null;
      
      try {
        if (contentType.includes('application/json')) {
          data = await res.json();
        } else if (contentType.startsWith('text/')) {
          data = await res.text();
        } else {
          // For binary content (images, etc.), return blob
          data = await res.blob();
        }
      } catch (parseError) {
        // If parsing fails, try to get text
        try {
          data = await res.text();
        } catch {
          data = null;
        }
      }

      // Handle error responses
      if (!res.ok) {
        const errorMessage = getErrorMessage(data, getStatusErrorMessage(res.status));
        
        // Don't retry on client errors (4xx) except 408, 429
        if (res.status >= 400 && res.status < 500 && ![408, 429].includes(res.status)) {
          return {
            ok: false,
            status: res.status,
            error: errorMessage,
            data: data,
          };
        }
        
        // Retry on server errors (5xx) or specific client errors
        lastError = {
          ok: false,
          status: res.status,
          error: errorMessage,
          data: data,
        };
        
        // If this was the last attempt, return error
        if (attempts >= maxAttempts) {
          return lastError;
        }
        
        // Wait before retry
        await new Promise(resolve => setTimeout(resolve, API_CONFIG.retryDelay * attempts));
        continue;
      }

      // Success response
      return {
        ok: true,
        status: res.status,
        data: data,
      };
      
    } catch (err) {
      lastError = {
        ok: false,
        status: 0,
        error: err.message || 'Błąd połączenia z serwerem',
      };
      
      // Don't retry on timeout or network errors if it's the last attempt
      if (attempts >= maxAttempts) {
        return lastError;
      }
      
      // Wait before retry
      await new Promise(resolve => setTimeout(resolve, API_CONFIG.retryDelay * attempts));
    }
  }

  return lastError;
}

/**
 * Convenience method for GET requests
 */
apiFetch.get = function(path, opts = {}) {
  return apiFetch(path, { ...opts, method: 'GET' });
};

/**
 * Convenience method for POST requests
 */
apiFetch.post = function(path, body, opts = {}) {
  return apiFetch(path, { ...opts, method: 'POST', body });
};

/**
 * Convenience method for PUT requests
 */
apiFetch.put = function(path, body, opts = {}) {
  return apiFetch(path, { ...opts, method: 'PUT', body });
};

/**
 * Convenience method for DELETE requests
 */
apiFetch.delete = function(path, opts = {}) {
  return apiFetch(path, { ...opts, method: 'DELETE' });
};

// Export for inline scripts
window.apiFetch = apiFetch;

// Also export for potential module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = apiFetch;
}
