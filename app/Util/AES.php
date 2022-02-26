<?php
namespace Common\Util;
/* eg.
$encrypt = (new AES(str_random(),AES::AES_ECB_PKCS5Padding));
$encryptData = $encrypt->encrypt('gz1DR+BsCzQe55HFdq1IiQ==');
$encryptData1 = $encrypt->decrypt($encryptData);
*/

class AES
{
    /*
    *  算法/模式/填充                16字节加密后数据长度        不满16字节加密后长度
    *   AES/CBC/NoPadding             16                          不支持       iv 固定
    *   AES/CBC/PKCS5Padding          32                          16          iv 固定
    *   AES/ECB/NoPadding             16                          不支持       iv 不固定
    *   AES/ECB/PKCS5Padding          32                          16          iv 不固定
    */
    const AES_CBC_NoPadding = 1;
    const AES_CBC_PKCS5Padding = 2;
    const AES_ECB_NoPadding = 3;
    const AES_ECB_PKCS5Padding = 4;

    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher = MCRYPT_RIJNDAEL_128;

    /**
     * The mode used for encryption.
     *
     * @var string
     */
    protected $mode = \MCRYPT_MODE_ECB;

    protected $padding = 'PKCS5Padding';

    protected $iv;

    protected $isPHP7 = false;

    /**
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  int  $modePadding
     */
    public function __construct($key, $modePadding = self::AES_ECB_PKCS5Padding)
    {
        $this->key = (string) $key;
        if (version_compare(PHP_VERSION,'7.0')>=0){
            $this->isPHP7 = true;
        }
        switch ($modePadding){
            case self::AES_CBC_NoPadding : {
                $this->mode = MCRYPT_MODE_CBC;
                $this->padding = 'noPadding';
            }break;
            case self::AES_CBC_PKCS5Padding : {
                $this->mode = MCRYPT_MODE_CBC;
                $this->padding = 'PKCS5Padding';
            }break;
            case self::AES_ECB_NoPadding : {
                $this->mode = MCRYPT_MODE_ECB;
                $this->padding = 'noPadding';
            }break;
            default:break;
        }
    }

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     */
    public function encrypt($value)
    {
        if ($this->isPHP7){
            return base64_encode(openssl_encrypt($value, 'AES-128-ECB', $this->key, true));
        }
        $iv = $this->iv?:mcrypt_create_iv($this->getIvSize(), $this->getRandomizer());

        $value = $this->addPadding($value);

        return base64_encode(mcrypt_encrypt($this->cipher, $this->key, $value, $this->mode, $iv));
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     */
    public function decrypt($payload)
    {
        // We'll go ahead and remove the PKCS7 padding from the encrypted value before
        // we decrypt it. Once we have the de-padded value, we will grab the vector
        // and decrypt the data, passing back the unserialized from of the value.
        $value = base64_decode($payload);
        if ($this->isPHP7){
            return openssl_decrypt($value, 'AES-128-ECB', $this->key, true);
        }
        $iv = $this->iv?:mcrypt_create_iv($this->getIvSize(), $this->getRandomizer());

        return $this->stripPadding(mcrypt_decrypt($this->cipher, $this->key, $value, $this->mode, $iv));
    }

    /**
     * Add padding to a given value.
     * @param  string  $value
     * @return string
     */
    protected function addPadding($value)
    {
        switch ($this->padding){
            case 'PKCS5Padding' : {
                $block = mcrypt_get_iv_size($this->cipher, $this->mode);

                $pad =  $block- (strlen($value) % $block);

                return $value.str_repeat(chr($pad), $pad);
            }break;
            default : {  // NoPadding
                return $value;
            }
        }
    }

    /**
     * Remove the padding from the given value.
     *
     * @param  string  $value
     * @return string
     */
    protected function stripPadding($value)
    {
        $pad = ord($value[($len = strlen($value)) - 1]);

        return $this->paddingIsValid($pad, $value) ? substr($value, 0, $len - $pad) : $value;
    }

    /**
     * Determine if the given padding for a value is valid.
     *
     * @param  string  $pad
     * @param  string  $value
     * @return bool
     */
    protected function paddingIsValid($pad, $value)
    {
        $beforePad = strlen($value) - $pad;

        return substr($value, $beforePad) == str_repeat(substr($value, -1), $pad);
    }


    /**
     * Get the IV size for the cipher.
     *
     * @return int
     */
    protected function getIvSize()
    {
        return mcrypt_get_iv_size($this->cipher, $this->mode);
    }

    /**
     * Get the random data source available for the OS.
     *
     * @return int
     */
    protected function getRandomizer()
    {
        if (defined('MCRYPT_DEV_URANDOM')) return MCRYPT_DEV_URANDOM;

        if (defined('MCRYPT_DEV_RANDOM')) return MCRYPT_DEV_RANDOM;

        mt_srand();

        return MCRYPT_RAND;
    }

    /**
     * Set the encryption key.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = (string) $key;

        return $this;
    }

    /**
     * Set the encryption cipher.
     *
     * @param  string  $cipher
     * @return $this
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;

        return $this;
    }

    /**
     * Set the encryption iv.
     *
     * @param  string  $iv
     * @return $this
     */
    public function setIv($iv)
    {
        $this->iv = $iv;

        return $this;
    }
}