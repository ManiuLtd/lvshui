<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Services\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\FanTicket;

class FanTicketController extends Controller
{
    public function index()
    {
        $fanTicket=FanTicket::paginate(20);
        ;
        return response()->json(['status' => 'success', 'data' => $fanTicket]);
    }

    public function show()
    {
        $fanTicket=FanTicket::with('id')->find(request()->id);
        return response()->json(['status' => 'success', 'data' => $fanTicket]);
    }

    public function store()
    {
        $data= request()->all();
        $fanTicket=FanTicket::create($data);
        if ($fanTicket) {
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if (FanTicket::find(request()->id)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if (FanTicket::find(request()->id)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

}
