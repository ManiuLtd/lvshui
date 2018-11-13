<?php

namespace App\Http\Controllers;

use App\Services\Qiniu;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upload() 
    {
        $file = request()->file('file');
        $url = Qiniu::upload($file);
        if($url) {
            return response()->json(['status' => 'success', 'msg' => '上传成功', 'url' => $url]);   
        }   

        return response()->json(['status' => 'error', 'msg' => '上传失败']);
    }

    public function delete() 
    {
        if(Qiniu::delete(request('url'))) {
            return response()->json(['status' => 'success', 'msg' => '删除成功']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败']);
    }
}
