<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Services\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\FanTicket;

class TicketController extends Controller
{
    public function index()
    {
        $tickes=Ticket::paginate(20);
        ;
        return response()->json(['status' => 'success', 'data' => $tickes]);
    }

    public function show()
    {
        $ticke=Ticket::with('id')->find(request()->id);
        return response()->json(['status' => 'success', 'data' => $ticke]);
    }

    public function store()
    {
        $data= request()->all();
        $ticke=Ticket::create($data);
        if ($ticke) {
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if (Ticket::where('id', request()->id)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if (Ticket::find(request()->id)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    //  门票验证
    public function ticketVerify()
    {
        // 传入 fan_tickets 对象
        $fan_ticket= request()->all();
        $ticket=Ticket::with('id')->find($fan_ticket->ticket_id);
        $fan_id = request()->fan_id;
        $price = $ticke->price;
        $ticket->error = 0; //无错误

        if ($ticket->limit != 0) {
            if ($fan_ticket->purchase_quantity > $ticket->limit) {
                $ticket->error = 6; //票数已达到用户单次购买上限
            }
            if ($fan_ticket->purchase_quantity > $ticke->daily_inventory) {
                $ticket->errot = 3; //票数超出每日上限
            }
        }
        if ($ticket->stock == 0) {
            $ticket->error = 4; //商品已售罄
        }
        if ($ticket->is_up == 0) {
            $ticket->error = 5; //商品已下架
        }
        
        return response()->json(['data' => $ticket]);
    }


}
