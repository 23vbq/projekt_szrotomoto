<?php
spl_autoload_register(function ($class) {
    $path = __DIR__.'/utils/'.$class.'.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

/**
 * Exception handler setup based on runtime environment, with default fallback to 'development'.
 */
function prepareExceptionHandler(string $runtime): void {
    set_exception_handler(function ($exception) use ($runtime) {
        error_log($exception->getMessage());

        if ($runtime === 'development') {
            Response::error($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            Response::error('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    });
}

prepareExceptionHandler('development');

prepareExceptionHandler(Env::get('RUNTIME'));