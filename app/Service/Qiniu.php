<?php 

namespace App\Services;

class Qiniu 
{
    public static function upload($file) 
    {
        $disk = \zgldh\QiniuStorage\QiniuStorage::disk('qiniu');
        $fileName = date('Ymd',time()).'/'.md5($file->getClientOriginalName().time().rand()).'.'.$file->getClientOriginalExtension();
        $bool = $disk->put($fileName, file_get_contents($file->getRealPath()));
        // 判断是否上传成功
        if ($bool) {
            return $disk->downloadUrl($fileName);
        }

        return false;
    }

    public static function delete($url) 
    {
        $url = parse_url($url)['path'];
        $filename = substr($url, 1, strlen($url));
        $disk = \zgldh\QiniuStorage\QiniuStorage::disk('qiniu');
        if($disk->delete($filename)) {
            return true;
        }
        return false;
    }
}
