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
            'password' => 'egg',
            'ipFilters' => array('127.0.0.1', '::1', '192.168.1.105', '114.243.211.67', '114.240.87.197', '61.48.43.86', '221.220.250.113', '114.243.29.173', '114.243.219.108'),
        )
    ),

    'components' => array(

        'db' => array(
            //online
            'connectionString' => 'mysql:host=sqld.duapp.com;port=4050;dbname=qJpmwgSdbskBxLaBpUJX', 'emulatePrepare' => true, 'username' => '564c64fdfc5d44dd9bc824cc42676c7b', 'password' => 'a01df665466d4eafbe9e453744081e3e', 'charset' => 'utf8',
            //local
//            'connectionString' => 'mysql:host=127.0.0.1;dbname=hi_egg', 'emulatePrepare' => true, 'username' => 'root', 'password' => 'root', 'charset' => 'utf8',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace,log',
                    'categories' => 'system.db.CDbCommand',
                    'logFile' => 'db.log',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
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
//            'urlSuffix' => '.html',
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
        'time_service' => ['class' => 'application.service.TimeService'],
    ),
    'params' => array(
        'DEBUG' => false,
    )
);
