<?php
/**
 * User: Ron
 * Date: 2017/09/21 下午2:26
 * 错误类型定义
 */

namespace common\components;

class CodeError {
    /**
     * 错误码格式说明（示例：100001、200001)  第一位"1"错误种类 第2、3位为业务错误(主要针对控制器同一类业务请用相同的代码) 第4、5、6对应具体的错误
     * 1 为系统级错误  2 服务器错误 从这里开始请按照规范填写，let's go!
     * 01 登录、注册相关
     * 02 支付相关
     * 03 购物车相关
     */
    const SUCCESS                            = 0; //成功
    const LOGIN_PASSWORD_ERROR               = -201001; //登录验证密码失败

    /**
     * 自定义说明，可以根据需要定义，也可以另外扩展一个数组来独立定义
     *
     * @var array
     */
    public static $ERR_MSG_MAP = array(
        self::SUCCESS                        => '成功',
        self::LOGIN_PASSWORD_ERROR           => '登录验证密码失败',
    );
}