<?php

Yii::setPathOfAlias('bootstrap', dirname(__FILE__) . '/../extensions/bootstrap');

return array(
    'name' => 'Histeria',

    'theme' => 'public',

    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',

    'preload' => array('log'),

    'controllerMap' => array(),
    'defaultController' => 'home',

    'import' => array(
        'bootstrap.widgets.*',
        'application.models.*',
        'application.filters.*',
        'application.library.*',
        'application.service.*',
        'application.components.*',
        'application.modules.srbac.controllers.SBaseController'
    ),

    'modules' => array(

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'HelloHitour',
            'ipFilters' => array('127.0.0.1', '::1', '192.168.1.105', '114.243.211.67', '114.240.87.197', '61.48.43.86', '221.220.250.113', '114.243.29.173', '114.243.219.108'),
        ),
        'srbac' => array(
            'userclass' => 'User',
            'userid' => 'uid',
            'username' => 'account',
            'delimeter' => '@',
            'debug' => false,
            'pageSize' => 10,
            'superUser' => 'Hitour',
            'css' => 'srbac.css',
            'layout' => 'application.views.layouts.main',
            'notAuthorizedView' => 'srbac.views.authitem.unauthorized',
            'alwaysAllowed' => array(
                'SiteLogin', 'SiteLogout', 'SiteIndex', 'SiteAdmin',
                'SiteError', 'SiteContact'
            ),
            'userActions' => array('Show', 'View', 'List'),
            'listBoxNumberOfLines' => 15,
            'imagesPath' => 'srbac.images',
            'imagesPack' => 'tango',
            'iconText' => true,
            'header' => 'srbac.views.authitem.header',
            'footer' => 'srbac.views.authitem.footer',
            'showHeader' => true,
            'showFooter' => true,
            'alwaysAllowedPath' => 'srbac.components',
        )
    ),

    'components' => array(

        'db' => array(
            //dev
            'connectionString' => 'mysql:host=113.31.82.136;dbname=hitour', 'emulatePrepare' => true, 'username' => 'hitour', 'password' => 'cqzs01@hitour', 'charset' => 'utf8',
            //test
            //'connectionString' => 'mysql:host=test.hitour.cc:3310;dbname=hitour', 'emulatePrepare' => true, 'username' => 'hitour', 'password' => 'cqzs01@hitour', 'charset' => 'utf8',
            //'connectionString' => 'mysql:host=localhost;dbname=hicart', 'emulatePrepare' => true, 'username' => 'root', 'password' => 'root', 'charset' => 'utf8',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, debug, warn, error, fatal, profile',
                    'categories' => 'mail.*',
                    'maxFileSize' => 1024, //单文件最大1M
                    'logFile' => 'mail.log',
                ),
            ),
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
        'user' => array(
            'allowAutoLogin' => true,
        ),
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap'
        ),
//        'urlManager' => array(
//            'showScriptName' => false,
//            'urlFormat' => 'path',
//            'urlSuffix' => '.html'
//            'routeVar' => 'route',
//            'rules' => array(
//                '' => 'home/index',
//                'webView/<action:\w+>' => 'appView/<action>',
//                'admin' => 'admin/product/index',
//                'admin/<controller:\w+>/<action:\w+>' => 'admin/<controller>/<action>',
//                'home/<action:\w+>' => 'home/<action>',
//                'export/<action:\w+>' => 'export/<action>',
//                'channel/<action:\w+>' => 'channel/<action>',
//                'account/<action:\w+>' => 'account/<action>',
//                'activity/<action:\w+>' => 'activity/<action>',
//                'activity360/<action:\w+>' => 'activity360/<action>',
//                'ad/<action:\w+>' => 'ad/<action>',
//                'checkout/<action:\w+>' => 'checkout/<action>',
//                'about' => 'site/about',
//                'contact' => 'site/contact',
//                'test/<action:\w+>' => 'test/<action>',
//                'site/<action:\w+>' => 'site/<action>',
//                'album/<action:\w+>' => 'album/<action>',
//                'order/<action:\w+>' => 'order/<action>',
//                'mobile' => 'mobile/index',
//                'wechat/<action:\w+>' => 'weChat/<action>',
//                'mobile/<action:\w+>' => 'mobile/<action>',
//                'common/<action:\w+>' => 'common/<action>',
//                'promotion/<promotion_id:\d+>' => 'promotion/index',
//                'promotion/<action:\w+>' => 'promotion/<action>',
//                'product/<action:\w+>' => 'product/<action>',
//                'product/<action:\w+>/<product_id:\d+>' => 'product/<action>',
//                'paygate/<action:\w+>' => 'payGate/<action>',
//                'country/<action:\w+>' => 'country/<action>',
//                'sightseeing/<product_id:\d+>' => 'product/index',
//                '<en_name:\w+>' => 'country/index',
//                '<country_name:\w+>/<city_name:\w+(-)?\w*>' => 'city/index',
//                'group/<group_id:\d+>' => 'group/index',
//                'error' => 'site/error',
//                '<country_name:\w+>/<city_name:\w+>/group/<tag_or_group:\w+>' => 'city/index',
//                '<country_name:\w+>/<city_name:\w+>/<tag_or_group:\w+>' => 'city/index',
//                '<country_name:\w+>/<city_name:\w+>/tag' => 'city/index',
//                '<country_name:\w+>/<city_name:\w+>' => 'city/index',
//                '<country_name:\w+>/<city_name:\w+>/hotel_plus' => 'city/hotelplus',
//                'exthome/<action:\w+>' => 'extHome/<action>',
//            ),
//        ),
        'authManager' => array(
            'class' => 'application.modules.srbac.components.SDbAuthManager',
            'connectionID' => 'db',
            'itemTable' => 'items',
            'assignmentTable' => 'assignments',
            'itemChildTable' => 'itemchildren',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),

        /* Custom Components */
        'cart' => ['class' => 'application.service.Cart'],
        'order' => ['class' => 'application.service.Order'],
        'notify' => ['class' => 'application.service.Notify'],
        'booking' => ['class' => 'application.service.Booking'],
        'product' => ['class' => 'application.service.Product'],
        'shipping' => ['class' => 'application.service.Shipping'],
        'customer' => array('class' => 'application.library.Customer'),
        'returning' => ['class' => 'application.service.Returning'],
        'stateMachine' => array('class' => 'application.service.StateMachine'),
        'activity' =>['class'=>'application.service.Activity'],
        'weixin_notify' =>['class'=>'application.service.WeixinNotify'],
        'email_notify' =>['class'=>'application.service.EmailNotify'],
        'sms_notify' =>['class'=>'application.service.SmsNotify'],
        'weixin_notify' => ['class' => 'application.service.WeixinNotify'],
    ),
    'params' => array(
        'DEBUG' => false,

        'testMail' => 'test@hitour.cc',
        'adminEmail' => 'webmaster@example.com',

        'urlPreview' => 'http://dev.hitour.cc/hitour/sightseeing/',
        'urlPreviewOnTest' => 'http://dev.hitour.cc/product/index/product_id/',
        'urlViewAlbum' => 'http://sandbox.hitour.cc/alpaca/index.php?r=Album/Edit&album_id=',
        'urlHicartBase' => 'http://hicart.host/',

        'MOBILE_DEV' => 1,

        'PAYMENT_REALLY' => 0,
        'INVOICE_PATH' => 'data/invoice/',
        'WEB_PREFIX' => '/',
        'THEME_BASE_URL' => '/themes/public',

        'DIR_UPLOAD_ROOT' => '/var/www/admin/hitour/',
        'HOME_IMAGE_ROOT' => '/image/upload/home_image/',
        'TOUR_IMAGE_ROOT' => '/image/upload/tour_image/',
        'EXPERT_IMAGE' => '/image/expert/',
        'QR_IMAGE' => '/image/qr/',

        'VOUCHER_PATH' => '/data/voucher/',
        'PRODUCT_VOUCHER_PATH' => '/data/product/',
        'DIR_PDF_SCRIPT' => '/home/app/bin/',
        'STOCK_PDF_ROOT' => '/data/stock_pdf/',
        'INSURANCE_FILE_ROOT' => '/data/insurance_excel/',
        'IS_TEST' => 0,
        'ATTACHED_VOUCHER_PATH' => '/data/attached_voucher/',
        'DEPARTURES_FILE_ROOT' => '/data/departures/',
        'urlPreviewOnTest' => 'http://test.hitour.cc/product/index/product_id/',
        'urlViewCity' => 'http://test.hitour.cc/city/index/city_code/',

        'urlHome' => 'http://admin.host/',
        'WEIXIN_GATE' => 'http://test.hitour.cc:60001/push',
        'BOOTSTRAP_BASE_URL' => '/themes/bootstrap',
        'EDM_FILE_ROOT' => '/data/edms/',
        'EDM_IMAGE_DIR' => '/themes/bootstrap/images/edm'
    )
);
