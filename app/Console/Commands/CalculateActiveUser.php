<?php

namespace App\Console\Commands;

use App\Models\Traits\ActiveUserHelper;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{
    use ActiveUserHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larabbs:calculate-active-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成活跃用户';

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
        $this->info('开始生成');
        $this->cacheAndCalculateActiveUsers();
        $this->info('生成成功');
    }
}
