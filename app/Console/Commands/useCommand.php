<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class useCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'use:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @throws \Exception
     */
    public function handle()
    {
        //
        $today = Carbon::today();
        DB::beginTransaction();
        try {
            Order::where('use_state',0)
                ->whereDate('end_date','<',$today->toDateString())
                ->update(['use_state'=>-4]);
            DB::commit();
            \Log::info('成功');
        }catch (\Exception $e) {
            DB::rollBack();
            \Log::info('失败：'.$e);
        }
    }
}
