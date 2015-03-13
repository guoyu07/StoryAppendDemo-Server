<div id="statistics-container" class="fixed-container clearfix" ng-controller="StatisticsCtrl">
    <div id="menu" class="col-md-3 side-menu">
        <div class="list-group-item" ng-repeat="item in local.menu_items"
             ng-class="{ active: item.id == local.current_route }">
            <h4 ng-if="item.group == true" class="list-group-item-heading" ng-bind="item.label"></h4>
            <a ng-if="item.group == false" class="list-group-item-text" style="cursor: pointer;" id="{{item.id}}" ng-bind="item.label"
               ng-click="goToItem( item.id )"></a>
        </div>
    </div>
    <div class="col-md-16 col-md-offset-3 clearfix">
        <ng-view></ng-view>
    </div>
</div>

<?php
$path = __DIR__ . '/routes/';
include_once($path . 'basic_order/basic_order.php');
include_once($path . 'order_chart/order_chart.php');
include_once($path . 'order_complaint/order_complaint.php');
include_once($path . 'activities_order/activities_order.php');
include_once($path . 'user_analysis/user_analysis.php');
include_once($path . 'product_feedback/product_feedback.php');
?>