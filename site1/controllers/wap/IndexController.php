<?php
/**
 * User: Ron
 * Date: 2017/10/25 上午10:20
 * To change this template use File | Settings | File Templates.
 */

namespace SiteController\controllers\wap;

use SiteController\components\SiteController;
use common\components\CodeHttp;
use common\components\Language;
use common\components\PubFun;
use common\models\core\YacBaseModel;
use common\models\dbredis\CacheModel;
use common\components\CookiesSite1;
use common\service\common\CommonService;
use Yii;

class IndexController extends SiteController {

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $queryParam = $request->getQueryParams();
        $url = 'index';
        if($queryParam) {
            $queryStr = http_build_query($queryParam);
            $url .= '?'.$queryStr;
        }
        $this->redirect($url);

        $this->_data['msg'] = 'wap index ' . Yii::getVersion();
        $this->_data['info'] = '';

        return $this->render('//wap/index/index', $this->_data);
    }

}
