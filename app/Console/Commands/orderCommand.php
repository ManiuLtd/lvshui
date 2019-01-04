<?php

namespace App\Console\Commands;

use App\Models\MallGood;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class orderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:date';

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
        //订单支付超时-状态设为取消订单
        $orders = Order::where('pay_state',0)->with('orderGoods')->get();
        $now = Carbon::now();
        $news = [] ;
        foreach ($orders as $order){
            if($order->pay_state == 0){
                $date = Carbon::parse($order->created_at)->addMinutes(15);
                if($date->lt($now)){
                    $news[]=$order;
                }
            }
        }
        \Log::info('订单是否超时处理'.time());

       DB::beginTransaction();
        try {
            foreach ($news as $new) {
                $goods = $new->orderGoods;
                Order::where('id', $new->id)->update(['pay_state' => -1]);
                foreach ($goods as $good) {
                    MallGood::where('id', $good->good_id)->increment('stock', $good->num);
                }
            }
            DB::commit();
            \Log::info('成功');
        }catch (\Exception $e) {
            DB::rollBack();
            \Log::info('失败：'.$e);
        }

    }
}
