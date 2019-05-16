<?php
/**
 * User: Ron
 * Date: 2017/10/25 上午10:20
 * 首页
 */

namespace SiteController\controllers\web;

use Yii;
use common\components\{CodeHttp, CookiesSite1, Language, PubFun};
use common\models\dbredis\CacheModel;
use SiteController\components\SiteController;

class IndexController extends SiteController {

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {


        $data = [

        ];

        return $this->renderMine('//web/index/index', $data);
    }
}
