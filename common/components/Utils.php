<?php
/**
 * User: Ron
 *
 * Date: 2017/09/20 上午11:19
 * 使用频率高的全局方法。
 * 注意：和后端[redis/mongo/service……]有数据交互有公共方法放Utils类，和后端没数据交互的公共方法请放PubFun类。
 */

namespace common\components;

use Yii;
use common\models\dbredis\CacheModel;

class Utils {

    public static $allDbConnList = array();
    public static $masterDb = null;
    public static $slaverDb = null;
    public static $mongoDb = null;

    /**
     * 从缓存获取用户ID，是从数组中取
     *
     * @return int
     */
    public static function getUserId()
    {
        $userId = 0;
        $userInfo = self::getLoginUserInfo();
        if (!empty($userInfo) && $userInfo['user_id']) $userId = $userInfo['user_id'];
        if (empty($userId)) $userId = 0;
        return (int)$userId;
    }

    /**
     * 设置验证码输入错误次数
     */
    public static function setUserInputLoginCodeNum()
    {
        $userIp = PubFun::getIP();
        $userCodeKey = $userIp . "_randCodeNums";
        $codeInputNum = CacheModel::getRedisValue($userCodeKey);
        if (!$codeInputNum) $codeInputNum = 0;
        $codeInputNum += 1;
        CacheModel::setRedisValueWithExpire($userCodeKey, $codeInputNum, Yii::$app->params['cacheExpire.oneDay']);
        return $codeInputNum;
    }

    /**
     * 获取验证码输入错误次数
     */
    public static function getUserInputLoginCodeNum()
    {
        $userIp = PubFun::getIP();
        $userCodeKey = $userIp . "_randCodeNums";
        return CacheModel::getRedisValue($userCodeKey);
    }

    /**
     * 根据IP判断登录错误次数，如果达到配置上限返回true
     *
     * @return bool
     */
    public static function loginHasCheckCode()
    {
        $checkCodeConfig = Yii::$app->params['checkCodeConfig']['login'];
        //开启登录验证码,并且根据IP判断登录错误次数达到设定值开启验证码
        if ($checkCodeConfig['status']) {
            if (self::getUserInputLoginCodeNum() >= $checkCodeConfig['errorNum']) {
                return true;
            }
        }
        return false;
    }

    /**
     * 把验证码存入缓存
     *
     * @param int $randCode 验证码
     */
    public static function setUserLoginCode($randCode)
    {
        CacheModel::setRedisValueWithExpire(CookiesSite1::getUsers() . "_login", $randCode, Yii::$app->params['cacheExpire.halfAnHour']);
    }

    /**
     * 把验证码从缓存取出来
     *
     * @return string
     */
    public static function getUserLoginCode()
    {
        return CacheModel::getRedisValue(CookiesSite1::getUsers() . "_login");
    }

    /**
     * 保存第三方登录的access_token到缓存redis
     *
     * @param string $type 登陆账号类型，google 或 facebook
     * @param string $token 第三方登录的access_token
     * @return bool
     */
    public static function setRedisThirdLoginToken($type, $token)
    {
        $userCookie = CookiesSite1::getUsers();
        CacheModel::setRedisValueWithExpire("{$userCookie}-{$type}-Token", $token);
        return true;
    }

    /**
     * 获取缓存中的第三方登录的access_token
     *
     * @param string $type 登陆账号类型，google 或 facebook
     * @return string
     */
    public static function getRedisThirdLoginToken($type)
    {
        if (empty($type) || is_null($type)) return '';
        $userCookie = CookiesSite1::getUsers();
        return CacheModel::getRedisValue("{$userCookie}-{$type}-Token");
    }

    /**
     * 生成图片验证码：用于登录、注册、找回密码和修改密码时的验证
     *
     * @author Ron 2018-02-06
     * @param array $conf 生成验证码的配置信息
     */
    public static function createCaptcha($conf = array())
    {
        Header("Content-type: image/gif");
        $num = 4;
        $width = '100';
        $height = '30';
        //$name = 'randcode';

        if ($conf != "") {
            foreach ($conf as $key => $value) {
                $$key = $value;
            }
        }

        //初始化
        $border = 0; //是否要边框 1要:0不要
        $how = $num; //验证码位数
        $w = $width; //图片宽度
        $h = $height; //图片高度
        $fontsize = 5; //字体大小
        $alpha = "023456789"; //验证码内容1:字母
        $number = "023456789"; //验证码内容2:数字
        $randcode = ""; //验证码字符串初始化
        srand((double)microtime() * 1000000); //初始化随机数种子
        $im = imagecreate($w, $h); //创建验证图片
        //绘制基本框架
        $bgcolor = imagecolorallocate($im, 255, 255, 255); //设置背景颜色
        imagefill($im, 0, 0, $bgcolor); //填充背景色
        if ($border) {
            $black = imagecolorallocate($im, 0, 0, 0); //设置边框颜色
            imagerectangle($im, 0, 0, $w - 1, $h - 1, $black);//绘制边框
        }
        //逐位产生随机字符
        $j = 0;
        for ($i = 0; $i < $how; $i++) {
            $alpha_or_number = mt_rand(0, 1); //字母还是数字
            $str = $alpha_or_number ? $alpha : $number;
            $which = mt_rand(0, strlen($str) - 1); //取哪个字符
            $code = substr($str, $which, 1); //取字符
            $j = !$i ? 20 : $j + 15; //绘字符位置
            $color3 = imagecolorallocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100)); //字符随即颜色
            imagechar($im, $fontsize, $j, 3, $code, $color3); //绘字符
            $randcode .= $code; //逐位加入验证码字符串
        }
        Utils::setUserLoginCode($randcode);
        //添加干扰
        for ($i = 0; $i < 5; $i++)//绘背景干扰线
        {
            $color1 = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //干扰线颜色
            imagearc($im, mt_rand(-5, $w), mt_rand(-5, $h), mt_rand(20, 300), mt_rand(20, 200), 55, 44, $color1); //干扰线
        }
        for ($i = 0; $i < $how * 15; $i++)//绘背景干扰点
        {
            $color2 = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //干扰点颜色
            imagesetpixel($im, mt_rand(0, $w), mt_rand(0, $h), $color2); //干扰点
        }
        //绘图结束
        imagegif($im);
        imagedestroy($im);
    }

    /**
     * 验证验证码是否正确：传入randCode缓存中不存在，则视为无验证码验证情况
     *
     * @author Ron 2018-02-06
     * @param string $randCode 用户输入的验证码
     * @return array
     */
    public static function checkRandCode($randCode)
    {
        //验证缓存中是否保存用户验证码
        if (Utils::loginHasCheckCode()) {
            $redisLoginCode = strtolower(Utils::getUserLoginCode());
            if ($redisLoginCode) {
                $randCode = strtolower($randCode);
                if ($randCode == $redisLoginCode || strrev($randCode) == $redisLoginCode) {
                    return array('isValidate' => true, 'code' => CodeHttp::HTTP_OK, 'message' => '');
                } else {
                    return array('isValidate' => false, 'code' => CodeHttp::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Verification Code Error. Please Try Again!');
                }
            } else {
                return array('isValidate' => false, 'code' => CodeHttp::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Verification Code Error. Please Try Again!');
            }
        } else {
            return array('isValidate' => true, 'code' => CodeHttp::HTTP_OK, 'message' => 'No verification is required!');
        }
    }

    /**
     * 检测一个文件是否已经被包含过
     *
     * @author Ron 2019-05-10
     * @param string $fileStr 文件路径
     * @return boolean
     */
    public static function checkFileIsInclude($fileStr)
    {
        $incFileArr = get_required_files();
        foreach($incFileArr as $fileKey => $fileVal){
            if ($fileVal == $fileStr) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取文件的根路径
     *
     * @author Ron 2019-05-10
    */
    public static function getRootDirectory()
    {
        $path =  __FILE__;
        return str_replace(strstr($path,'system'),'',$path);
    }

}
