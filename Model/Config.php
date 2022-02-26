<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class Config extends Model
{


    /**
     * 读取配置类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = [
            'string'        => __('String'),
            'password'      => __('Password'),
            'text'          => __('Text'),
            'editor'        => __('Editor'),
            'number'        => __('Number'),
            'date'          => __('Date'),
            'time'          => __('Time'),
            'datetime'      => __('Datetime'),
            'datetimerange' => __('Datetimerange'),
            'select'        => __('Select'),
            'selects'       => __('Selects'),
            'image'         => __('Image'),
            'images'        => __('Images'),
            'file'          => __('File'),
            'files'         => __('Files'),
            'switch'        => __('Switch'),
            'checkbox'      => __('Checkbox'),
            'radio'         => __('Radio'),
            'city'          => __('City'),
            'selectpage'    => __('Selectpage'),
            'selectpages'   => __('Selectpages'),
            'array'         => __('Array'),
            'custom'        => __('Custom'),
        ];
        return $typeList;
    }

    public static function getRegexList()
    {
        $regexList = [
            'required' => '必选',
            'digits'   => '数字',
            'letters'  => '字母',
            'date'     => '日期',
            'time'     => '时间',
            'email'    => '邮箱',
            'url'      => '网址',
            'qq'       => 'QQ号',
            'IDcard'   => '身份证',
            'tel'      => '座机电话',
            'mobile'   => '手机号',
            'zipcode'  => '邮编',
            'chinese'  => '中文',
            'username' => '用户名',
            'password' => '密码'
        ];
        return $regexList;
    }

    
    public function getGroupList()
    {
        $groupList = config('site.configgroup');
        foreach ($groupList as $k => &$v) {
            $v = __($v);
        }
        return $groupList;
    }


}
