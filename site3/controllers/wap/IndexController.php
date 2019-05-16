<?php
/**
 * M站首页
 */

namespace site3\controllers\wap;

use Yii;
use common\components\PubFun;
use common\components\CookiesSite3;
class IndexController extends SiteController {

    /**
     * Ron
     * 2018/08/07
     */
    public function actionIndex()
    {

        $data = [

        ];

        return $this->renderMine('//wap/index/index', $data);
    }

}
