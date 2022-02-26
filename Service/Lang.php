<?php

namespace App\Service;

class Lang
{
    /**
     * @var array 语言数据
     */
    private static $lang = [];

    /**
     * @var string 语言作用域
     */
    private static $range = 'zh-cn';

    /**
     * 加载语言定义(不区分大小写)
     * @access public
     * @param  array|string $file 语言文件
     * @param  string $range      语言作用域
     * @return mixed
     */
    public static function load($file, $range = '')
    {
        $range = $range ?: self::getRange();
        $file  = is_string($file) ? [$file] : $file;

        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }

        $lang = [];

        foreach ($file as $_file) {
            if (is_file($_file)) {

                $_lang = include $_file;

                if (is_array($_lang)) {
                    $lang = array_change_key_case($_lang) + $lang;
                }
            }
        }

        if (!empty($lang)) {
            self::$lang[$range] = $lang + self::$lang[$range];
        }

        return self::$lang[$range];
    }

    public static function getRange()
    {
        return strtolower(config('app.locale'));
    }
}
