<?php
/**
 * Created by PhpStorm.
 * User: yangzuwei
 * Date: 2019-01-13
 * Time: 22:05
 */

namespace Wilson\Async\Console;

use Illuminate\Console\Command;
use Wilson\Async\Server\TaskServer;

class ServerInstance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:task {status?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '启动swoole task server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $validStatus = ['start', 'stop', 'reload', 'restart'];
        $status = $this->argument('status');

        if ($status == null||$status == 'start') {
            $server = new TaskServer();
            $server->run();
        }

        if(in_array($status,$validStatus) === false){
            $this->info('invalid option status!');
            return;
        }

        $shellPath = base_path().'/vendor/wilson_yang/sendtask';
        $shell = 'cd ' . $shellPath . ' && ' . './bin/' . $status . '.sh';
        exec($shell);
    }
}