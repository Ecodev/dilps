<?php

declare(strict_types=1);
// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require 'src/Debug.php';

// Self-called anonymous function that creates its own scope and keep the global namespace clean.
call_user_func(function (): void {
    /** @var \Interop\Container\ContainerInterface $container */
    $container = require 'config/container.php';

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(\Zend\Expressive\Application::class);

    // Import programmatic/declarative middleware pipeline and routing
    // configuration statements
    require 'config/pipeline.php';
    require 'config/routes.php';

    // we only run the application if this file was NOT included (otherwise, the file was included to access misc functions)
    if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
        $app->run();
    }
});
