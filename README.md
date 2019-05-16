 yii2的多web站点使用说明
===============================

项目包括：

1、site1的pc站项目在site1->web，m站项目在site1->wap

2、site2的pc站项目在site2->web，m站的在site2->wap

3、site3的pc站项目在site3->web，m站的在site3->wap

4、项目的框架、外部扩展、组件基本上通过composer来管理

 项目结构
-------------------

```
common
    components/          包含公共组件、公共扩展类
    config/              包含所有项目的公共配置
    extend/              需要自定义处理的第三方扩展（如果完全是第三方的扩展包，通过composer安装到vendor下面）
    javaapi/             对接java接口的类，每类接口都有父类
    models/              公共的model，基本上所有的model层都放这里，必要时在应用里的model扩展子类。mysql所有类直接放在model根目录下，规范命名。
        core/            mongo/redis/yac等核心的父类
        dbmongo/         处理mongo数据库的所有model类
        dbredis/         处理redis的所有model类
    service/             应用的公共逻辑层，按模块分目录管理
        core/            service类的父类
    tests/               测试相关
    widgets/             公共小部件

console
    config/              控制台的配置
    controllers/         控制台的controller (commands)
    migrations/          数据库迁移内容
    models/              控制台的model
    runtime/             包含在运行时生成的文件

site1（站点1）
    assets/              包含该应用管理JavaScript和CSS等应用程序资源的内容
    components/          包含该应用项目的组件、公共扩展类
    config/              该应用项目的配置
    controllers/         该应用项目的控制器，继承自SiteController，SiteController继承自BaseController
    external             配置的数据，如xml等
    models/              该应用项目的model，必要时扩展，继承公共的model来处理
    runtime/             包含在运行时生成的文件
    tests/               测试相关
    views/               视图内容
    wap/                 该项目m站的入口（域名解析到此目录），以及js/css资源的管理
    web/                 该项目pc站的入口（域名解析到此目录），以及js/css资源的管理
    widgets/             该应用项目的小部件

site2（站点2）
    assets/              包含该应用管理JavaScript和CSS等应用程序资源的内容
    components/          包含该应用项目的组件、公共扩展类
    config/              该应用项目的配置
    controllers/         该应用项目的控制器，继承自SiteController，SiteController继承自BaseController
    models/              该应用项目的model，必要时扩展，继承公共的model来处理
    runtime/             包含在运行时生成的文件
    tests/               测试相关
    views/               视图内容
    wap/                 该项目m站的入口（域名解析到此目录），以及js/css资源的管理
    web/                 该项目pc站的入口（域名解析到此目录），以及js/css资源的管理
    widgets/             该应用项目的小部件

site3（站点3)
    assets/              包含该应用管理JavaScript和CSS等应用程序资源的内容
    components/          包含该应用项目的组件、公共扩展类
    config/              该应用项目的配置
    controllers/         该应用项目的控制器，继承自SiteController，SiteController继承自BaseController
    models/              该应用项目的model，必要时扩展，继承公共的model来处理
    runtime/             包含在运行时生成的文件
    tests/               测试相关
    views/               视图内容
    wap/                 该项目m站的入口（域名解析到此目录），以及js/css资源的管理
    web/                 该项目pc站的入口（域名解析到此目录），以及js/css资源的管理
    widgets/             该应用项目的小部件

vendor/                  包含yii2框架，扩展，组件
```




 一些说明
===============================
一、有关类的使用
> 1. 定义的类名要注意全局唯一（针对model、service、公共方法类），类名与文件名一一对应。
> 1. model下面的类名必须唯一。
> 2. service下面分模块管理，类名必须唯一，虽然分模块管理了，不同目录不要建同名的类，以方便使用与查找。

二、有关函数的使用
> 1. 定义方法名尽量全局唯一。使用贴近相关意义的单词全称或缩写，不用担心名称长。如果拼字有误，IDE会有提醒，有拼写错误提醒的词要调整。
> 2. 函数都必须加注释，包括：函数说明、参数的数据类型与说明、返回类型说明。注意：参数注明类型、返回数据格式要前后统一。
> 3. 函数要尽量小型化，考虑通用性。

三、有关cookie类
> 1. cookie基础类为CookiesBase，各项目（站点类型）处理cookie的在此基础上扩展，如：CookiesSite1、CookiesSite2、CookiesSite3。
> 2. 对cookie的处理，都必须抽取出来放到这些处理cookie的类中来处理，针对一个cookie处理的方法成对出现，一个set，一个get。
> 3. 此类函数的命名，前面是get或set，后面是对应的cookie键名有意义部分。

四、有关语言、翻译类
> Language类，处理所有和语言、翻译有关的内容，PC、M的都整合在一起了，基本不需要怎么改了。如果原来的业务里有不在适用的，调整下业务逻辑以适用这个类的用法。

五、有关公共方法（添加时注意归类）
> 1. PubFun类中全是静态方法，和后端数据(缓存、数据库)没有交互。
> 2. Utils类中全是静态方法，和后端缓存数据，或配置数据有交互。另外，针对常用使用redis的，也抽取出来放到此类了，基本成对出现。

六、有关日志类
> LogManage类，是把日志写到文件的处理类。

七、有关全局静态变量数据缓存的VarCache
> 1. VarCache类，处理数据对象缓存，减少在一个请求中相同数据的重复调用。如 getUserRegionFromIp() 中的使用。
> 2. 使用些类时，要注意数据键名的唯一性。

八、有关service的约定（此说明在BaseService类中也有）
> 1. service层主要处理从controller中抽取出来的逻辑。
> 2. service层处理数据时，通过model类的方法进行处理数据。不允许调用model层父类和yii2框架内的方法处理数据，此约束为统一处理数据入口。
> 3. service层，在必要时，可对java_api接口进行进一步封装。java_api里的api，不能在controller里直接调用。

九、有关java_api的约定
> 1. java_api的接口对接所有的java接口，不做逻辑处理，只能数据通道。
> 2. java_api里的api，不能在controller里直接调用。可在service中再封装一下，方便处理返回的数据及异常。

十、有关service/api的约定
> 1. api中的类与javaapi中的类一一对应，api中的类名后面带service
> 2. api中类方法的名字，在javaapi中类的名字基础上加上api前缀作为名字，这样就容易区分或查找了。

十一、有关model的说明
> model的父类中models/core中，父类的注释中有相关的使用说明。其中ActiveRecordModel为mysql业务的父类。



 开发中需要注意一些事项
===============================

1. 在配置中，涉及数据的，统一使用中括号（[]）表示。可以的话，在代码中数组也统一使用中括号表示。
2. 要注意IDE的错误、语法提示，在自己开发的代码中若出现要马上调整。（在IDE右上角出现绿色的勾，说明此文件已达到最规范的格式）
3. 统一注释格式。包括：函数说明、参数的数据类型与说明、返回类型说明。注意：参数注明类型、返回数据格式要前后统一。
4. 在循环中不能出现循环调用数据库的情况，除非在非循环下不能实现相关业务（此时要提出来讨论）。注意：这些情况在新项目已改写，在对接业务时要注意。
5. 对于php的代码，写完代码定型后，用IDE对代码和注释进行格式化，以保持格式统一。
6. 对于配置的管理，对于同类配置放在一个数组中，不要平行配置，以方便集中管理。
7. BaseService、BaseController类中，$_baseUrl、$_suffix、$_siteId、$_terminalType 为全局使用的变量。在代码中直接使用，不要再使用Yii::$app->params取配置。这些变量不能再定义作其他作用。
8. 前端传值，对于多类型判断尽量传字符串，后端再映射对应值。


 常用参数说明
===============================

1、Yii::$app->request->hostInfo 返回 http://www.example.com, url中host info部分

2、Yii::$app->request->serverName 返回 www.example.com, URL中的host name




 日志格式说明
===============================

自定义日志：

1、自定义日志类为 LogManage，统一格式，格式用json，方便以后使用ELK分析

 日志格式（json）
-------------------

```
ver                日志版本号
serTime            写日志时间，也相当于日志上报时间（上报ELK时间）
LogApp             应用的类型，如nimini的pc的日志：app-nimini-pc  Yii::$app->id . '-' . YII_SITE_TYPE
logType            日志类型（0：能用类型  1：错误日志  2：性能日志）
logLevel           日志等级（0：不写日志 1：写所有日志内容 2：写日志但不写retData的内容）
logModule          日志模块，目前都为0
userId             用户ID，如果用户没登录则值为0
content            日志详细内容
    processTime    请求响应时长，以毫秒为单位
    serverAddr     服务器IP地址，$_SERVER['SERVER_ADDR']
    method         请求方法，$_SERVER['REQUEST_METHOD']，或 Yii::$app->request->getMethod()
    userAgent      客户端浏览器类型，Yii::$app->request->getUserAgent()
    userHost       客户机的host，Yii::$app->request->getUserHost()
    userIp         客户机的IP地址，Yii::$app->request->getUserIp()
    requestData    日志类型，默认为0，若有请求和响应则：1是请求，2是响应
    requestData    请求参数，$_REQUEST
    requestUrl     请求URL
    retCode        返回码
    retMsg         返回错误具体信息
    retData        返回具体数据
```
