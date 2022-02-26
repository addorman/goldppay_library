<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class Attachment extends Model
{


    /**
     * 获取Mimetype列表
     * @return array
     */
    public function getMimetypeList()
    {
        $data = [
            "image/*"        => __("Image"),
            "audio/*"        => __("Audio"),
            "video/*"        => __("Video"),
            "text/*"         => __("Text"),
            "application/*"  => __("Application"),
            "zip,rar,7z,tar" => __("Zip"),
        ];
        return $data;
    }
}
