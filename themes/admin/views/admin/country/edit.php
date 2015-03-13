<div id="country-edit-container" ng-controller="CountryEditCtrl" class="container page-container">
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

    <?php include_once('modules/edit_ad/ad.php'); ?>

    <?php include_once('modules/edit_tab/tab.php'); ?>

</div>