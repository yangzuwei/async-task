<?php
/**
 * Created by PhpStorm.
 * User: yangzuwei
 * Date: 2018-12-28
 * Time: 17:54
 */

namespace Wilson\Async\Provider;

use Illuminate\Support\ServiceProvider;
use Wilson\Async\Server\TaskServer;
use Wilson\Async\Console\ServerInstance;

class TaskServiceProvider extends ServiceProvider
{
    protected $commands = [
        ServerInstance::class,
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/swoole.php' => config_path('swoole.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton('tasksender', function () {
            return new SwooleSender();
        });
        $this->app->singleton('taskserver', function () {
            return new TaskServer();
        });

        $this->commands($this->commands);
    }
}