<?php
/**
 * User: Ron
 * Date: 2017/09/30 下午5:14
 * To change this template use File | Settings | File Templates.
 */

namespace site2\controllers\web;

use Yii;
use site2controllers\BaseController;
use common\components\LogManage;

class IndexController extends BaseController {

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $data = [];
        return $this->render('//web/vip/index', $data);
    }

}