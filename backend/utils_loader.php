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


Response::setCorsHeaders();

function prepareExceptionHandler(string $runtime = 'development'): void {
    $isConsole = php_sapi_name() === 'cli';
    if ($isConsole) {
        return;
    }
    
    set_exception_handler(function ($exception) use ($runtime) {
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
    
    set_error_handler(function ($severity, $message, $file, $line) use ($runtime) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
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

prepareExceptionHandler('development');

prepareExceptionHandler( Env::get('RUNTIME'));