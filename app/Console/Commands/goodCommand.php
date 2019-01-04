<?php

namespace App\Console\Commands;

use App\Models\MallGood;
use App\models\MallGoodGroup;
use App\Models\MallGoodUp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class goodCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'good:date';

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
        $goods = MallGood::all();
        $today = Carbon::today();
        $new = [];
        foreach ($goods as $good){
            $isUp = $good->is_up;
            if($good->sratr_date!='' && $good->end_date!='' ){
                $startDate =Carbon::parse($good->sratr_date);
                $endDate = Carbon::parse($good->end_date);
                if($isUp == 0 ){
                    if($today->gte($startDate) && $today->lt($endDate)){
                        $good->is_up = 1 ;
                        $new[] = $good;
                    }
                }else if($isUp == 1){
                    if($today->gte($endDate)){
                        $good->is_up = 0 ;
                        $new[] = $good;
                    }
                }
            }
        }
        \Log::info('商城商品'.time());
        DB::beginTransaction();
        try {
            foreach ($new as $item){
                MallGoodUp::where('id', $item->up_id)->update(['is_up' => 0]);
                $is_up = $item->is_up;
                if($is_up == 0 ){
                    MallGood::where('id',$item->id)->update(['is_up'=>0]);
                }
                if ($is_up == 1) {
                    $up = MallGoodUp::create(['is_up' => 1,'good_id'=>$item->id]);
                    MallGood::where('id',$item->id)->update(['up_id'=>$up->id,'is_up'=>1]);
                }
            }
            \Log::info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info('执行失败：'.$e);
        }



    }
}
