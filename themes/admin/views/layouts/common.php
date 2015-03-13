<?php
$cs = Yii::app()->getClientScript();
$cs->scriptMap = array('jquery.js' => false);
$ctrl_prefix = 'admin/';

$base_url = Yii::app()->getBaseUrl(true) . '/';

if(isset($this->resource_refs)) {
    $resPath = strtolower($this->resource_refs);
} else {
    $resPath = strtolower(str_replace($ctrl_prefix, '', Yii::app()->controller->id) . '_' . Yii::app()->controller->action->id);
}
//var_dump( $resPath );
?>

<!DOCTYPE html>
<html lang="zh" ng-app="HitourAdminApp" ng-controller="rootCtrl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="renderer" content="webkit">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no,minimal-ui" />

    <base href="<?= $base_url; ?>" />

    <title><?= CHtml::encode($this->pageTitle); ?></title>

    <link rel="dns-prefetch" href="//hitour.qiniudn.com/">

    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/image/common/apple_touch_icon_144x144_precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/image/common/apple_touch_icon_114x114_precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/image/common/apple_touch_icon_72x72_precomposed.png">
    <?php
    $this->beginContent('//resources/head_' . $resPath);
    $this->endContent();
    ?>
    <?php
    $this->beginContent('//resources/head');
    $this->endContent();
    ?>
</head>
<body ng-class="{ 'has-overlay': local.has_overlay, 'is-test': local.is_test }">
    <?php
    $this->beginContent('//layouts/topmenu');
    $this->endContent();
    ?>
    <?= $content; ?>
    <?php
    $this->beginContent('//resources/foot');
    $this->endContent();
    ?>
    <?php
    $this->beginContent('//resources/foot_' . $resPath);
    $this->endContent();
    ?>
</body>
</html>