<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $fillable = [
        'admin_id', 'username', 'url', 'title', 'content', 'ip',
    ];

    /**
     * 记录日志
     * @param string $title
     * @param string $content
     */
    public function record($title = '', $content = '')
    {
        // $auth = Auth::instance();
        // $admin_id = $auth->isLogin() ? $auth->id : 0;
        // $username = $auth->isLogin() ? $auth->username : __('Unknown');

        $admin_id = 1;
        $username = '__';

        $title = $title ? $title : $this->title;

        $content = $content ? $content : $this->$content;
        if (!$content) {
            $content = request()->input();
            $content = $this->getPureContent($content);
        }

        self::create([
            'title'     => $title,
            'content'   => !is_scalar($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content,
            'url'       => substr(request()->fullUrl(), 0, 1500),
            'admin_id'  => $admin_id,
            'username'  => $username,
            'useragent' => substr(request()->server('HTTP_USER_AGENT'), 0, 255),
            'ip'        => request()->ip()
        ]);
    }


    /**
     * 获取已屏蔽关键信息的数据
     * @param $content
     * @return false|string
     */
    protected function getPureContent($content)
    {
        if (!is_array($content)) {
            return $content;
        }
        foreach ($content as $index => &$item) {
            if (preg_match("/(password|salt|token)/i", $index)) {
                $item = "***";
            } else {
                if (is_array($item)) {
                    $item = $this->getPureContent($item);
                }
            }
        }
        return $content;
    }
}
