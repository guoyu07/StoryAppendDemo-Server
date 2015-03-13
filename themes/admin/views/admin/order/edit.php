<?php $path = __DIR__ . "/modules/"; ?>
<div id="order-edit-container" ng-controller="OrderEditCtrl" class="container page-container">
    <div class="row text-center grid-top">
        <div class="btn-group">
            <button class="btn btn-primary" ng-click="changePage( key )"
                    ng-class="{ 'active': local.current_menu == key }" ng-repeat="(key, item) in local.menus">
                <span ng-bind="key + '. ' + item.label"></span>
                <span class="i i-eye" ng-show="item.alert"></span>
                <span class="i i-refresh refresh-animate" ng-show="item.loading"></span>
            </button>
        </div>
    </div>

    <div ng-show="local.current_menu == '1'">
        <?php include_once($path . "edit_shipping/shipping.php"); ?>
    </div>
    <div ng-show="local.current_menu == '2'">
        <?php include_once($path . "edit_todo/todo.php"); ?>
    </div>
    <div ng-show="local.current_menu == '3'">
        <?php include_once($path . "edit_payment/payment.php"); ?>
    </div>
    <?php include_once($path . "edit_overlay/overlay.php"); ?>
</div>