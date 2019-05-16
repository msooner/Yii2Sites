<?php
/**
 * User: Ron
 * Date: 2018/07/26
 * 3DES加密类
 */

namespace common\service\common;

use Yii;
use common\service\core\BaseService;

class DesService extends BaseService {

    public $key = "#7022323";
    private $iv = '';

    public function __construct($para = [])
    {
        parent::__construct();
        //没有外部key则合适配置key，否则使用默认key
        $key = '';
        if (isset($para['key']) && !empty($para['key'])) $key = $para['key'];
        if (empty($key)) $key = Yii::$app->params['goodsIdLockConfig']['key'];
        if (!empty($key)) $this->key = $key;
    }

    /**
     * 确定字符是大写还是小写。
     *
     * @param $str
     * @return bool
     */
    function checkcase1($str)
    {
        $str = ord($str);
        if ($str > 64 && $str < 91) {
            //大写
            return true;
        }
        if ($str > 96 && $str < 123) {
            //小写
            return false;
        }
        //非字母
        return false;
    }

    public function encrypt($input)
    {
        $this->key = str_pad($this->key, 24, '0');
        $data = @openssl_encrypt($input, 'des-ede3-cbc', $this->key, 0, $this->iv);
        return $data;
    }

    public function decrypt($encrypted)
    {
        $this->key = str_pad($this->key, 24, '0');
        $info = @openssl_decrypt($encrypted, 'des-ede3-cbc', $this->key, 0, $this->iv);
        return $info;
    }

    /*
      For PKCS7 padding
     */
    private function addPadding($string, $blockSize = 16)
    {
        $len = strlen($string);
        $pad = $blockSize - ($len % $blockSize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    private function stripPadding($string)
    {

        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

}