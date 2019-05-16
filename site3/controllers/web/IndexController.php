<?php
/**
 * User: Ron
 * Date: 2018/07/18 上午10:09
 * To change this template use File | Settings | File Templates.
 */

namespace site3\controllers\web;

use common\components\Language;
use Yii;
use common\components\PubFun;
use common\components\CookiesSite1corp;

class IndexController extends SiteController {

    /**
     * 首页
     *
     * @author Ron 2018-07-18
     * @return mixed
     */
    public function actionIndex()
    {

        $data = [

        ];

        return $this->renderMine('//web/index/index', $data);
    }

}
