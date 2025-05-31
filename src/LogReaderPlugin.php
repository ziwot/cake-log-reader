<?php
declare(strict_types=1);

namespace LogReader;

use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

/**
 * Plugin for LogReader
 */
class LogReaderPlugin extends BasePlugin
{
    /**
     * Add routes for the plugin.
     *
     * If your plugin has many routes and you would like to isolate them into a separate file,
     * you can create `$plugin/config/routes.php` and delete this method.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        // remove this method hook if you don't need it
        $routes->plugin(
            'LogReader',
            ['path' => '/log-reader'],
            function (RouteBuilder $builder): void {
                $builder->connect(
                    '/',
                    'LogReader::index',
                    ['_name' => 'log_reader:index'],
                );
            },
        );
        parent::routes($routes);
    }
}
