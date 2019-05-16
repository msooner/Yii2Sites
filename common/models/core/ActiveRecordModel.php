<?php
/**
 * User: Ron
 * Date: 2017/09/26 下午12:41
 * mysql 基础类
 *
 * model类相关的使用说明：
 * 1、common里面的model类的命名：去掉表名前缀，去掉下划线后，第一个字母大写，后面加上"Model"为类名，文件名命名与类名相同。如：who_user_address表类名为 UserAddressModel
 *   各应用里面的model类的命名：去掉表名前缀，去掉下划线后，第一个字母大写，后面加上"AppModel"为类名，文件名命名与类名相同。如：who_user_address表类名为 UserAddressAppModel
 *   应用里面的model类可以继承common里面的model类，也可以直接继承common公共model父类。
 * 2、对于查询生成器，表名通过类名自动配置。ActiveRecordModel类对yii2的 tableName() 重写了。
 * 3、对于每个查询，一定要指定所使用的链接(更新、修改、删除不需要，因为默认使用主库链接)，getMyDb() 或 getMyDbTwo() 来指定。
 * 4、在某些情况下要使用sql来处理查询，最好使用 ActiveRecord类中的 findBySql() 来处理。
 * 5、在model的类中调用方法时 $this 相当于是model类。如 UserAddressModel 类中，$this::find() 相当于是 UserAddressModel::find()，注意不是使用 ->
 * 6、对于 mysqlBrand 实例下的库的表的类，直接继承 ActiveRecordModel 类；对于 mysqlTwo 实例下的库的表的类，直接继承 SplitMysqlBaseModel 类，此类做了切换实例处理；
 * 7、service层，除getByKeyId()、getListByKey()、getCountByKey()之外，model层父类和yii2框架内的方法不允许调用，必须通过表的model类的方法进行处理数据。此约束为统一处理数据入口。
 * 8、getByKeyId()、getListByKey()、getCountByKey() 方法可在service层直接调用，在必要时可以通过表的model类复写来扩展。
 *
 * 组织sql、验证规则的相关文档：
 * 1、查询生成器： http://www.yiichina.com/doc/guide/2.0/db-query-builder
 * 2、操作详解： http://www.yiichina.com/tutorial/834
 * 3、rules验证规则详解： http://blog.csdn.net/navioo/article/details/51096648
 *
 * 有关增、删、改、查数据的使用说明（注意：使用时尽量优先考虑使用本类的公共方法）：
 * 1、更新数据统一使用 $this::updateAll()
 * 2、删除数据统一使用 $this::deleteAll()
 * 3、根据表中是否有主键和判断原来是否有记录，插入数据时根据情况使用 $this::insert() 或 $this::save()
 * 4、是否要验证规则，通过 $safeOnly 确定，true为需要验证规则，false为不需要验证规则。当需要验证规则时，需要在对应的model中添加好 rules()
 */

namespace common\models\core;

use Yii;
use yii\db\ActiveRecord;
use yii\di\ServiceLocator;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use common\components\LogManage;

class ActiveRecordModel extends ActiveRecord {
    //需要强制查询主库的表名，表名不要带前缀（如需要增加，请将表名添加到 $_exceptTableBrand 数组中）
    private static $_exceptTableBrand = [
        'users'
    ];

    //需要强制查询主库的表名，表名不要带前缀（如需要增加，请将表名添加到 $_exceptTableSplit 数组中）
    private static $_exceptTableSplit = [
        'user_login_op_log'
    ];

    public static $_dbTwo = null;//dbLog组件

    public $_dbType = 0;//标识要使用的mysql实例，0：标识使用mysql的db实例，1：标识使用mysql的dbTwo实例


    public function __construct($dbType = 0, array $config = [])
    {
        parent::__construct($config);
        $this->_dbType = $dbType;
    }

    /**
     * 从model类名获取表名
     */
    public static function tableNameFromModel()
    {
        $tabName = Inflector::camel2id(StringHelper::basename(get_called_class()), '_');
        $tabName = str_replace('_model', '', $tabName);//去掉model后缀
        $tabName = str_replace('_app', '', $tabName);//去掉应用里面扩展的后缀标识
        return $tabName;
    }

    /**
     * 给表名加上前缀替换符
     */
    public static function tableName()
    {
        return '{{%' . self::tableNameFromModel() . '}}';
    }

    /**
     * 检查并注册dbLog组件
     */
    public static function initDbTwo()
    {
        if (is_null(self::$_dbTwo)) {
            $locator = new ServiceLocator;
            $configMysql = require(Yii::getAlias('@YiiSiteApp') . '/config/' . YII_ENV . '/mysql.config.php');
            if ($configMysql['mysqlTwo']) $locator->set('dbTwo', $configMysql['mysqlTwo']);
            self::$_dbTwo = $locator->get('dbTwo');
        }
        return self::$_dbTwo;
    }

    /**
     * db 实例选择主从
     *
     * @return null|\yii\db\Connection
     */
    public static function getMyDb()
    {
        if (in_array(self::tableNameFromModel(), self::$_exceptTableBrand)) {
            return Yii::$app->db->getMaster();//对指定的表名，强制使用主库链接
        }
        return Yii::$app->db;//默认，有读写分离的链接
    }

    /**
     * dbTwo 实例选择主从
     *
     * @return null|\yii\db\Connection
     */
    public static function getMyDbTwo()
    {
        self::initDbTwo();//检查并注册dbLog组件
        if (in_array(self::tableNameFromModel(), self::$_exceptTableSplit)) {
            //对指定的表名，强制使用主库链接
            if (!is_null(self::$_dbTwo)) {
                return self::$_dbTwo->getMaster();
            }
        }
        if (!is_null(self::$_dbTwo)) {
            return self::$_dbTwo;
        }
        return null;//默认，有读写分离的链接
    }

    /**
     * 处理one的查询
     *
     * @param object $query query对象
     * @return array
     */
    public function oneMine($query)
    {
        $this->writeSql($query);

        if ($this->_dbType == 0) {
            $data = $query->one(static::getMyDb());
        } elseif ($this->_dbType == 1) {
            $data = $query->one(static::getMyDbTwo());
        } else {
            $data = $query->one(static::getMyDb());
        }
        if (is_null($data)) $data = array();

        return $data;
    }

    /**
     * 处理all的查询
     *
     * @param object $query query对象
     * @return array
     */
    public function allMine($query)
    {
        $this->writeSql($query);

        if ($this->_dbType == 0) {
            $data = $query->all(static::getMyDb());
        } elseif ($this->_dbType == 1) {
            $data = $query->all(static::getMyDbTwo());
        } else {
            $data = $query->all(static::getMyDb());
        }
        if (is_null($data)) $data = array();

        return $data;
    }

    /**
     * 处理count的查询
     *
     * @param object $query query对象
     * @param string $q 查询时count()中的内容，如：count(*)
     * @return int
     */
    public function countMine($query, $q)
    {
        $this->writeSql($query);

        if ($this->_dbType == 0) {
            $data = $query->count($q, static::getMyDb());
        } elseif ($this->_dbType == 1) {
            $data = $query->count($q, static::getMyDbTwo());
        } else {
            $data = $query->count($q, static::getMyDb());
        }

        return $data;
    }

    /**
     * 写查询时sql的日志
     *
     * @param object $query query对象
     * @return bool
     */
    public function writeSql($query)
    {
        if (!Yii::$app->params['logMangeConfig']['mysqlLogLevel']) return false;
        $commandQuery = clone $query;
        $sqlStr = $commandQuery->createCommand()->getRawSql();
        (new LogManage())->writeLog(['retMsg' => $sqlStr, 'method' => 'mysql', 'userAgent' => '', 'requestUrl' => ''], 'sql_mysql');
        return true;
    }

    /**
     * 查询单条数据的能用方法，支持多个数据库的实例。
     * 用法： model类名::findOneInfo($whereArr, $columnArr)
     *
     * @param string|array $whereArr 查询条件
     * @param string|array $columnArr 查询字段
     * @return array|null|ActiveRecord
     */
    public function findOneMine($whereArr, $columnArr = array())
    {
        if (empty($whereArr) || $whereArr == '') return array();
        if (empty($columnArr) || $columnArr == '') $columnArr = '*';//如果不选择查询字段，默认查询所有字段

        $query = $this->find()->select($columnArr)->where($whereArr)->asArray();
        $data = $this->oneMine($query);

        return $data;
    }

    /**
     * 查询多条数据的能用方法，支持多个数据库的实例。
     * 用法： model类名::findAllInfo($whereArr, $columnArr, $orderArr, $limit, $offset)
     *
     * @param string|array $whereArr 查询条件
     * @param string|array $columnArr 查询字段
     * @param string|array $orderArr 排序条件  正序: SORT_ASC  倒序: SORT_DESC
     * @param int $limit 限制条数。如果没有查询条件，最大给查询的条数为2000.
     * @param int $offset 查询页数
     * @return array|ActiveRecord[]
     */
    public function findAllMine($whereArr = array(), $columnArr = array(), $orderArr = array(), $limit = 0, $offset = 1)
    {
        if (empty($whereArr) || $whereArr == '') $limit = 2000;//如果没有查询条件，最大给查询的条数
        if (empty($columnArr) || $columnArr == '') $columnArr = '*';//如果不选择查询字段，默认查询所有字段

        $query = $this->find()->select($columnArr);
        if (!empty($whereArr) and !is_null($whereArr)) $query->where($whereArr);
        if (!empty($orderArr) and !is_null($orderArr)) $query->orderBy($orderArr);
        if ($limit) $query->limit($limit);
        if ($limit and $offset) {
            $offset = ($offset - 1) * $limit;
            $query->offset($offset);
        }
        $query->asArray();
        $data = $this->allMine($query);

        return $data;
    }

    /**
     * 查询条数
     *
     * @param string|array $whereArr 查询条件
     * @param string|array $columnArr 查询字段
     * @param string $q count的内容
     * @return int|string
     */
    public function findCountMine($whereArr = array(), $columnArr = array(), $q = '*')
    {
        if (empty($columnArr) || $columnArr == '') $columnArr = '*';//如果不选择查询字段，默认查询所有字段

        $query = $this->find()->select($columnArr);
        if (!empty($whereArr) and !is_null($whereArr)) $query->where($whereArr);
        $query->asArray();
        $data = $this->countMine($query, $q);

        return $data;
    }

    /**
     * 插入数据
     *
     * @param array $data 需要插入的数据
     * @param bool $lastId 是否返回主键id
     * @param bool $safeOnly 字段是否校验rules
     * @return int|array|mixed
     * @throws
     */
    public function insertMine($data, $lastId = false, $safeOnly = false)
    {
        $this->setAttributes($data, $safeOnly);
        $ret = $this::insert($safeOnly);
        if ($lastId) {
            return $this->primaryKey;//返回插入行的主键值
            //return $this->getAttributes();//返回插入的一行数据
        } else {
            return $ret;//返回插入数据后的状态
        }
    }


    /**
     * 根据条件更新数据
     *
     * @param array $attributes 更新的内容
     * @param string|array $condition 条件
     * @param array $params 参数
     * @return int
     */
    public function updateAllMine($attributes, $condition = '', $params = [])
    {
        return $this::updateAll($attributes, $condition, $params);
    }

    /**
     * 根据条件删除数据
     *
     * @param string|array|null $condition 条件
     * @param array $params 参数
     * @return int
     */
    public function deleteAllMine($condition = null, $params = [])
    {
        return $this::deleteAll($condition, $params);
    }

    /**
     * 此方法禁止使用。特别的以这样的方式提示不用使用。
     *
     * @param mixed $condition
     * @return array
     */
    public static function findOne($condition)
    {
        return [];
    }

    /**
     * 此方法禁止使用。特别的以这样的方式提示不用使用。
     *
     * @param mixed $condition
     * @return array
     */
    public static function findAll($condition)
    {
        return [];
    }

    /**
     * 根据条件查单条信息（注意：此方法可在service层直接调用，在必要时可以通过表的model类复写来扩展）
     *
     * @param array $whereArr 查询条件数据
     * @param string|array $columnArr 查询字段
     * @return array
     */
    public function getByKeyId($whereArr, $columnArr = [])
    {
        return $this->findOneMine($whereArr, $columnArr);
    }

    /**
     * 根据条件列表查多条信息（注意：此方法可在service层直接调用，在必要时可以通过表的model类复写来扩展）
     *
     * @param array $whereArr 查询条件数据
     * @param string|array $columnArr 查询字段
     * @param string|array $orderArr 排序条件  正序: SORT_ASC  倒序: SORT_DESC
     * @param int $limit 限制条数。
     * @param int $offset 查询页数
     * @return array
     */
    public function getListByKey($whereArr, $columnArr = [], $orderArr = array(), $limit = 0, $offset = 1)
    {
        return $this->findAllMine($whereArr, $columnArr, $orderArr, $limit, $offset);
    }

    /**
     * 查询记录条数（注意：此方法可在service层直接调用，在必要时可以通过表的model类复写来扩展）
     *
     * @param string|array $whereArr 查询条件
     * @param string|array $columnArr 查询字段
     * @param string $q count的内容
     * @return int|string
     */
    public function getCountByKey($whereArr = [], $columnArr = [], $q = '*')
    {
        return $this->findCountMine($whereArr, $columnArr, $q);
    }

}