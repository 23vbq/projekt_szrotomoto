<?php
// Disable display of PHP errors in output (we'll handle them via exception handler)
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

spl_autoload_register(function ($class) {
    $path = __DIR__.'/utils/'.$class.'.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

/**
 * Set CORS headers to allow cross-origin requests
 */
function setCorsHeaders(): void {
    $isConsole = php_sapi_name() === 'cli';
    if ($isConsole) {
        return;
    }
    
    // Get origin from request
    $requestOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
    
    // Allow specific origins (for production, replace with actual frontend URL)
    $allowedOrigins = [
        'http://localhost',
        'http://localhost:80',
        'http://127.0.0.1',
        'http://127.0.0.1:80',
    ];
    
    // If origin is in allowed list, use it (required for credentials)
    // If no origin header, allow localhost (for same-origin requests)
    if ($requestOrigin && in_array($requestOrigin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $requestOrigin");
    } else if (!$requestOrigin) {
        // No origin header means same-origin request, allow it
        header("Access-Control-Allow-Origin: http://localhost");
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    
    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Set CORS headers for all requests
setCorsHeaders();

/**
 * Exception handler setup based on runtime environment, with default fallback to 'development'.
 */
function prepareExceptionHandler(string $runtime = 'development'): void {
    $isConsole = php_sapi_name() === 'cli';
    if ($isConsole) {
        return;
    }
    
    set_exception_handler(function ($exception) use ($runtime) {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        error_log($exception->getMessage());
        error_log($exception->getTraceAsString());

        if ($runtime === 'development') {
            Response::error($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            Response::error('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    });
    
    // Also set error handler for PHP errors/warnings
    set_error_handler(function ($severity, $message, $file, $line) use ($runtime) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        error_log("PHP Error: $message in $file on line $line");
        
        if ($runtime === 'development') {
            Response::error("PHP Error: $message", Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            Response::error('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return true;
    });
}

// Set default exception handler first
prepareExceptionHandler('development');

// Try to get runtime from env, but don't fail if .env doesn't exist
try {
    if (class_exists('Env')) {
        $runtime = Env::get('RUNTIME');
        prepareExceptionHandler($runtime);
    }
} catch (Exception $e) {
    // If .env doesn't exist or RUNTIME is not set, use default 'development'
    error_log('Could not load RUNTIME from .env, using default: ' . $e->getMessage());
}