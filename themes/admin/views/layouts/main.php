<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/bower_components/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/stylesheets/admin_styles.css" />
    <?php
    $cs = Yii::app()->getClientScript();
    $base_url = Yii::app()->theme->baseUrl;
    $cs->scriptMap = array('jquery.js' => false);

    $cs->registerScriptFile($base_url . '/bower_components/jquery/jquery.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/bootstrap/dist/js/bootstrap.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/angular/angular.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/angular-resource/angular-resource.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/angular-animate/angular-animate.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/angular-strap/dist/angular-strap.min.js');
    $cs->registerScriptFile($base_url . '/bower_components/angular-strap/dist/angular-strap.tpl.min.js');

    ?>
</head>
<body>
    <?php

    $items = array(
        array(
            'url' => $this->createUrl('product/index'),
            'label' => '搜索产品'
        )
    );

    if(Yii::app()->user->isGuest) {
        $items[] = array(
            'url' => $this->createUrl('site/login'),
            'label' => 'Login'
        );
    } else {
        $items[] = array(
            'url' => $this->createUrl('site/logout'),
            'label' => Yii::app()->user->getName() . ' -- Logout'
        );
    }

    $this->widget('bootstrap.widgets.TbNavbar', array(
        'type' => 'default',
        'brand' => 'Hitour',
        'brandUrl' => $this->createUrl('/'),
        'collapse' => true,
        'items' => array(
            array(
                'class' => 'bootstrap.widgets.TbMenu',
                'items' => $items,
            ),
        )
    ));

    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'links' => $this->breadcrumbs,
    ));
    ?>
    <div class="container content-container">
        <?= $content; ?>
    </div>
</body>
</html>
