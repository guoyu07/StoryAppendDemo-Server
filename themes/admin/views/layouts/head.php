<?php
$request_urls = !empty($this->request_urls) ? json_encode($this->request_urls) : json_encode(array());
$fe_options = !empty($this->fe_options) ? json_encode($this->fe_options) : json_encode(array());
$root_product = isset($this->product_detail) && !empty($this->product_detail) ? json_encode($this->product_detail) : json_encode([]);
?>
<link rel="stylesheet" href="themes/admin/stylesheets/_custom_bootstrap.css" />
<link rel="stylesheet" href="themes/admin/stylesheets/_custom_flatly.css" />
<link rel="stylesheet" href="themes/admin/stylesheets/icons.css" />

<link rel="stylesheet" href="themes/admin/bower_components/nvd3/nv.d3.min.css" />
<link rel="stylesheet" href="themes/admin/bower_components/ngQuickDate/dist/ng-quick-date.css">
<link rel="stylesheet" href="themes/admin/bower_components/chosen/public/chosen.min.css">
<link rel="stylesheet" href="themes/admin/bower_components/angular-chosen-localytics/chosen-spinner.css">

<link rel="stylesheet" href="themes/admin/stylesheets/styles.css" />

<script type="text/javascript">
    //Backend Data
    var $fe_options = JSON.parse('<?= $fe_options; ?>');
    var $request_urls = JSON.parse('<?= $request_urls; ?>');
    var $root_product = JSON.parse('<?= $root_product ?>');

    var pathinfo = {
        base_dir  : 'admin/',
        theme_dir : 'themes/admin/'
    };
    pathinfo.module_dir = pathinfo.theme_dir + 'views/modules/';
    pathinfo.template_dir = pathinfo.theme_dir + 'views/templates/';
</script>