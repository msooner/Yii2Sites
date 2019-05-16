<?php
/**
 * User: Ron
 * Date: 2017/10/17 下午6:01
 * To change this template use File | Settings | File Templates.
 */

namespace site2controllers;

use Yii;
use site2components\SiteController;
use common\components\PubFun;
use common\components\LogManage;
use common\service\user\UserEmailService;

class BaseController extends SiteController {

    /**
     * 检测下载来源，记录数据
     *
     * @return mixed
     */
    public function actionDownload()
    {
        $type = $this->getParam('type');
        if ($type == '') {
            return false;
        }
        if($type == 'qrcode') {
            $brType = PubFun::clientType();
            if ($brType['systemType'] == 'ios' || ($brType['systemType'] == 'windows' && $brType['browserType'] == 'safari')) {
                $type = 'ios';
            }
        }
        if (strtolower($type) == 'ios') {
            $hrefUrl = Yii::$app->params['iosDownUrl'];
        } else {
            $hrefUrl = Yii::$app->params['googlePayDownUrl'];
        }

        $logData = array(
            'addTime' => time(),
            'userIp' => $this->_userIp,
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'page' => 'app_down_load_page',
            'type' => $type
        );
        //记录请求日志
        (new LogManage())->writeLog(array('method' => __METHOD__, 'retMsg' => $logData), 'down_info');

        return $this->redirect($hrefUrl, 301);
    }

    /**
     * 第三方需要的Privacy Notice
     *
     * @return mixed
     */
    public function actionPrivacyPolicy()
    {
        $this->checkAccessUser();
        $this->_data['siteLanguage'] = $this->_siteLanguage;

        return $this->render('policy', $this->_data);
    }

    /**
     * 如果邮箱地址不存在则保存  /vip/send-email?email=tes1235@123.com&sourceType=2
     */
    public function actionSendEmail()
    {
        //if(!$this->isAjax()) $this->_ajaxReturn(400, array(), 'is not ajax request');
        $email = $this->getParam('email');
        $sourceType = $this->getParam('sourceType', 1);//来源（1 pc 、2 wap）
        if (!PubFun::isEmail($email)) $this->_ajaxReturn(400, array(), 'email is error');
        (new UserEmailService())->saveEmailNotExit($email, $sourceType);

        $this->_ajaxReturn(200, array(), 'success');
    }

}