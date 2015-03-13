<?php /* @var $this Controller */ ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144"
          href="/image/common/apple-touch-icon-144x144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114"
          href="/image/common/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72"
          href="/image/common/apple-touch-icon-72x72-precomposed.png">

    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/bower_components/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/stylesheets/icons.css" />
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/stylesheets/admin_styles.css" />
    <script src='https://api.tiles.mapbox.com/mapbox.js/v2.0.0/mapbox.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox.js/v2.0.0/mapbox.css' rel='stylesheet' />
<?php
$cs = Yii::app()->getClientScript();
$base_url = Yii::app()->theme->baseUrl;
$cs->scriptMap = array('jquery.js' => false);

$cs->registerScriptFile($base_url . '/bower_components/es5-shim/es5-shim.min.js');

$cs->registerScriptFile($base_url . '/bower_components/jquery/jquery.min.js');

$cs->registerScriptFile($base_url . '/bower_components/angular/angular.js');
$cs->registerScriptFile($base_url . '/bower_components/angular-animate/angular-animate.min.js');
$cs->registerScriptFile($base_url . '/bower_components/angular-sanitize/angular-sanitize.min.js');
$cs->registerScriptFile($base_url . '/bower_components/angular-resource/angular-resource.min.js');

$cs->registerScriptFile($base_url . '/bower_components/bootstrap/dist/js/bootstrap.min.js');
$cs->registerScriptFile($base_url . '/bower_components/angular-ui-bootstrap/dist/ui-bootstrap-tpls-0.10.0.min.js');

$cs->registerScriptFile($base_url . '/javascripts/helpers/histeriaFactory.js');
$cs->registerScriptFile($base_url . '/javascripts/helpers/histeriaDirective.js');
?>