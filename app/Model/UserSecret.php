<?php

namespace Common\Model;

use CommonUtil\GoogleAuthenticator;
use Moogula\Database\Eloquent\Model;
use Moogula\Contracts\Encryption\Encrypter as EncrypterContract;
use Moogula\Contracts\Encryption\EncryptException;

class UserSecret extends Model
{

    protected $fillable = [
        'user_id', 'white_list', 'deposit_url', 'drawal_url', 'callback_url',
        'api_secret', 'is_google_auth', 'payment_password', 'auth_secret',
    ];

    /**
     * 保存支付密码
     */
    public static function paymentPassword($payload = null, $secret = '', $encode = true)
    {

        try {
            $encrypter = app(EncrypterContract::class);

            if ($encode) {
                return $payload ? $encrypter->encrypt($payload) : '';
            } else {
                return $payload ? $encrypter->decrypt($payload) : '';
            }
        } catch (EncryptException $e) {
            return '';
        }
    }


    /**
     * 验证谷歌验证
     */
    public function checkGoogleAuth($code, $secret = null)
    {
        $secret = is_null($secret) ? $this->auth_secret : $secret;
        $ga = new GoogleAuthenticator();

        return $ga->verifyCode($secret, $code, 1);
    }
}
