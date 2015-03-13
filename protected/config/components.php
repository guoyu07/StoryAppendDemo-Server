<?php
return array(
    'db' => array(
//        'connectionString' => 'mysql:host=dev.hitour.cc;dbname=hitour',
        'connectionString' => 'mysql:host=test.hitour.cc:3310;dbname=hitour',
        'emulatePrepare' => true,
        'username' => 'hitour',
        'password' => 'cqzs01@hitour',
        'charset' => 'utf8',
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
                'levels' => 'info, debug, warn, error, fatal, profile',
                'categories' => 'mail.*',
                'maxFileSize' => 1024,
                'logFile' => 'mail.log',
            ),
        ),
    ),

    'user' => array(
        'allowAutoLogin' => true,
    ),

    'cache' => array(
        'class' => 'CFileCache',
    ),

    'urlManager' => array(
        'rules' => array(
            '' => 'home/index',
            'admin' => 'admin/product/index',
            'admin/<controller:\w+>/<action:\w+>' => 'admin/<controller>/<action>',
            'home/<action:\w+>' => 'home/<action>',
            'export/<action:\w+>' => 'export/<action>',
            'channel/<action:\w+>' => 'channel/<action>',
            'account/<action:\w+>' => 'account/<action>',
            'activity/<action:\w+>' => 'activity/<action>',
            'activity360/<action:\w+>' => 'activity360/<action>',
            'ad/<action:\w+>' => 'ad/<action>',
            'checkout/<action:\w+>' => 'checkout/<action>',
            'common/<action:\w+>' => 'common/<action>',
            'mobile' => 'mobile/index',
            'mobile/<action:\w+>' => 'mobile/<action>',
            'order/<action:\w+>' => 'order/<action>',
            'paygate/<action:\w+>' => 'payGate/<action>',
            'about' => 'site/about',
            'contact' => 'site/contact',
            'error' => 'site/error',
            'wechat/<action:\w+>' => 'weChat/<action>',
            'test/<action:\w+>' => 'test/<action>',
            'promotion/<promotion_id:\d+>' => 'promotion/index',
            'promotion/<action:\w+>' => 'promotion/<action>',
            'sightseeing/<product_id:\d+>' => 'product/index',
            'product/<action:\w+>' => 'product/<action>',
            'product/<action:\w+>/<product_id:\d+>' => 'product/<action>',
            '<en_name:\w+>' => 'country/index',
            'country/<action:\w+>' => 'country/<action>',
            '<country_name:\w+>/<city_name:\w+(-)?\w*>' => 'city/index',
            '<country_name:\w+>/<city_name:\w+>/group/<tag_or_group:\w+>' => 'city/index',
            '<country_name:\w+>/<city_name:\w+>/tag' => 'city/index',
        ),
        'routeVar' => 'route',
        'urlFormat' => 'path',
        'showScriptName' => false,
    ),

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

    'bootstrap' => array(
        'class' => 'bootstrap.components.Bootstrap',
    ),

    //Gateway
    'gta' => array(
        'class' => 'application.components.gta.GTAService',
        'gate_url' => 'https://interface.demo.gta-travel.com/rbscnapi/RequestListenerServlet',
        'partner' => '38',
        'account' => 'XML@BEIJINGCENTURYTRAVEL.COM',
        'password' => 'PASS',
    ),
    'alipay_pc' => array(
        'class' => 'application.components.payment.AlipayPc',
        'partner' => '2088011716854319',
        'seller' => 'xt@hitour.cc',
        'security' => 'uhiusvfit517p5fx16lsx15is9pz633h',
        'title' => '支付宝（即时到帐）',
        'logo' => '',
        'mobile' => 0,
    ),
    'alipay_wap' => array(
        'class' => 'application.components.payment.AlipayWap',
        'partner' => '2088011716854319',
        'seller' => 'xt@hitour.cc',
        'security' => 'uhiusvfit517p5fx16lsx15is9pz633h',
        'title' => '支付宝手机版（即时到帐）',
        'logo' => '',
        'mobile' => 1,
    ),
    'chinapay_pc' => array(
        'class' => 'application.components.payment.ChinapayPc',
        'partner' => '808080201303661',
        'title' => '银联在线支付',
        'logo' => '',
        'mobile' => 0,
    ),
    'chinapay_wap' => array(
        'class' => 'application.components.payment.ChinapayWap',
        'partner' => '808080201303661',
        'title' => '银联在线支付手机版',
        'logo' => '',
        'mobile' => 1,
    ),
    'weixinpay_pc' => array(
        'class' => 'application.components.payment.WeixinpayWidget',
        'partner' => '808080201303661',
        'title' => '微信安全支付',
        'logo' => '',
        'mobile' => 0,
    ),
    'weixinpay_widget' => array(
        'class' => 'application.components.payment.WeixinpayWidget',
        'partner' => '808080201303661',
        'title' => '微信安全支付',
        'logo' => '',
        'mobile' => 1,
    ),

    //    'cps' => require_once(dirname(__FILE__) . '/cps.php'),
    'cart' => ['class' => 'application.service.Cart'],
    'order' => ['class' => 'application.service.Order'],
    'notify' => ['class' => 'application.service.Notify'],
    'product' => ['class' => 'application.service.Product'],
    'booking' => ['class' => 'application.service.Booking'],
    'activity' => ['class' => 'application.service.Activity'],
    'shipping' => ['class' => 'application.service.Shipping'],
    'customer' => array('class' => 'application.library.Customer'),
    'returning' => ['class' => 'application.service.Returning'],
    'sms_notify' => ['class' => 'application.service.SmsNotify'],
    'email_notify' => ['class' => 'application.service.EmailNotify'],
    'weixin_notify' => ['class' => 'application.service.WeixinNotify'],
    'stateMachine' => array('class' => 'application.service.StateMachine'),
);