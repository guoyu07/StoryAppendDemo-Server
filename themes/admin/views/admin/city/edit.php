<div id="city-edit-container" class="container page-container" ng-controller="CityEditCtrl">
    <div class="row text-center grid-top">
        <div class="btn-group">
            <button class="btn btn-primary" ng-click="setCurrentMenu( key )"
                    ng-class="{ 'active': local.current_menu == key }" ng-repeat="(key, item) in local.menus">
                <span ng-bind="key + '. ' + item.label"></span>
                <span class="i i-refresh refresh-animate" ng-show="item.loading"></span>
            </button>
        </div>
    </div>

    <?php include_once('modules/edit_info/info.php'); ?>

    <?php include_once('modules/edit_group/group.php'); ?>

    <?php include_once('modules/edit_top/top.php'); ?>

    <?php include_once('modules/edit_hotel/hotel.php'); ?>

    <?php include_once('modules/edit_line/line.php'); ?>

    <?php include_once('modules/edit_article/article.php'); ?>

    <?php include_once('modules/edit_app/app.php'); ?>

</div>