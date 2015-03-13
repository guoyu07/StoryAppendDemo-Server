<div id="splash" ng-show="local.show_splash"></div>
<div id="error" ng-show="local.show_error"></div>
<nav class="navbar navbar-inverse" role="navigation" id="top-header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
                <span class="sr-only">触发菜单</span>
            </button>
            <a class="navbar-brand" ng-href="{{ local.home_url }}">
                <img src="themes/admin/images/common/logo.png" alt="Histeria - Hitour后台" />
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-01">
            <ul class="nav navbar-nav">
                <li ng-repeat="menu in local.menu" ng-class="{ 'dropdown': menu.items }">
                    <a ng-href="{{ menu.link_url }}" ng-bind="menu.label" ng-show="!menu.items"></a>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" ng-show="menu.items">
                        {{ menu.label }}
                        <span class="caret"></span>
                    </a>
                    <ul id="yw1" class="dropdown-menu" ng-if="menu.items" role="menu">
                        <li ng-repeat="item in menu.items">
                            <a tabindex="-1" ng-href="{{ item.link_url }}" ng-bind="item.label"></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="navbar-right">
                <?php
                if(Yii::app()->user->isGuest) {
                    ?>
                    <a href="<?= $this->createUrl('site/login') ?>">登录</a>
                <?php
                } else {
                    ?>
                    <a href="<?= $this->createUrl('site/logout') ?>">登出</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</nav>
<div class="breadcrumb">
    <div class="container">
        <div class="go-back border part pointer" ng-bind-html="local.breadcrumb.back.content"
             ng-click="local.breadcrumb.back.clickCb()">
        </div>
        <div class="current-position border part" ng-if="local.breadcrumb.back.part_content"
             ng-bind-html="local.breadcrumb.back.part_content" ng-click="local.breadcrumb.back.partClickCb()"
             ng-class="{ 'pointer' : local.breadcrumb.back.partClickCb }">
        </div>
        <div class="main-content part">
            <div class="pull-left" ng-bind-html="local.breadcrumb.body.content" ng-click="local.breadcrumb.body.clickCb()"
                  ng-class="{ 'pointer' : local.breadcrumb.body.clickCb }"></div>
            <div class="pull-right" ng-if="local.breadcrumb.body.right_content"
                 ng-bind-html="local.breadcrumb.body.right_content" ng-click="local.breadcrumb.body.rightClickCb()"
                 ng-class="{ 'pointer' : local.breadcrumb.body.rightClickCb }">
            </div>
        </div>
    </div>
</div>