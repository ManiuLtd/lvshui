<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\FanTicket;

class ticketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:date';

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
     */
    public function handle()
    {
        // 重置每日库存
        $tickets = Ticket::where('is_up',1)->get();
        DB::beginTransaction();
        try {
            foreach ($tickets as $ticket) {
                $goods = $new->orderGoods;
                // Ticket::where('id', $ticket->id)->update(['daily_inventory' => $ticket->total]);
                $purchase_quantity = FanTicket::whereDate('booking_date', '=', date('Ymd'))->count();
                Ticket::where('id',$ticket_id)->update(['status'=> $ticket->total - $purchase_quantity]);
            }
            DB::commit();
            \Log::info('重置每日库存成功');
        }catch (\Exception $e) {
            DB::rollBack();
            \Log::info('重置每日库存失败：'.$e);
        }
    }
}
