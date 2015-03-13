<!--发货信息-->
<div class="states-section shipping-info hide-btns">
    <div class="section-head">
        <h2 class="section-title" ng-bind="local.section_head.shipping_info.title"></h2>
    </div>
    <div hi-uploader options="local.uploader_options.voucher"></div>
    <div class="section-body row">
        <?php include_once('shipping/action.php'); ?>
        <?php include_once('shipping/product_shipping.php'); ?>
    </div>
</div>

<?php include_once('shipping/info.php'); ?>

<?php include_once('shipping/passenger.php'); ?>

<?php include_once('shipping/contact.php'); ?>

<?php include_once('shipping/history.php'); ?>

<?php include_once('shipping/meta_dialog.php'); ?>

<?php include_once('shipping/meta.php'); ?>