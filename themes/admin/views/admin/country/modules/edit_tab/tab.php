<div class="states-section country-tab" ng-show="local.current_menu == '4'">
    <button class="btn btn-inverse add-btn" ng-click="addIndexTab()">添加</button>
    <div class="section-head">
        <h2 class="section-title">国家主页</h2>
    </div>
    <div class="section-body" id="citygroup_watershed" ng-show="data.tabs">
        <div class="section-subbody row clearfix">
            <ul class="nav nav-tabs">
                <li ng-class="{ active: $index == local.current_tab_i }"
                    ng-repeat="tab in data.all_tabs"
                    ng-click="switchTab($index)">
                    <a href="javascript:void(0);"><span ng-show="tab.status == 1">★</span>{{tab.name}}</a>
                </li>
            </ul>
        </div>
        <div class="section-subbody row clearfix">
            <div class="row tab-div">
                <span class="col-md-offset-3 col-md-2">Tab名称:</span>
                <input type="text" class="form-control tab-name" placeholder="请输入Tab名称"
                       ng-blur="updateTabInfo()" ng-model="data.tabs[local.current_tab_i].name" />
                <button class="btn btn-inverse delete-tab" ng-click="deleteIndexTab()">删除此Tab</button>
                <div class="all-status">
                    <button class="btn dropdown-toggle tab-status" data-toggle="dropdown"
                            ng-class="{ 'btn-default' : data.tabs[local.current_tab_i].status == '1', 'btn-inverse' : data.tabs[local.current_tab_i].status == '2' }"
                            ng-bind="local.group_status[ data.tabs[local.current_tab_i].status ].label">
                        <span class="caret"></span>
                    </button>
                    <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                    <ul class="dropdown-menu dropdown-inverse drop-menu">
                        <li ng-repeat="(status_id, status) in local.group_status">
                            <a ng-click="updateTabStatus( status_id )" ng-bind="status.label"></a>
                        </li>
                    </ul>
                </div>
                <div class="tab-display">
                    排序:
                    <input type="text" class="form-control tab-display-text" ng-blur="updateTabOrder()"
                           ng-model="data.all_tabs[local.current_tab_i].display_order" />
                </div>
            </div>
            <div class="row tab-div">
                <span class="col-md-offset-3 col-md-2">标题:</span>
                <textarea class="form-control tab-text" placeholder="请输入标题" ng-blur="updateTabInfo()"
                          hi-no-break hi-elastic  ng-model="data.tabs[local.current_tab_i].title"></textarea>
            </div>
            <div class="row tab-div">
                <span class="col-md-offset-3 col-md-2">简言:</span>
                <textarea class="form-control tab-text" placeholder="请输入简言" ng-blur="updateTabInfo()"
                          hi-no-break hi-elastic  ng-model="data.tabs[local.current_tab_i].brief"></textarea>
            </div>
            <div class="rowtab-div">
                <span class="col-md-offset-3 col-md-2">描述:</span>
                <textarea class="form-control tab-text" placeholder="请输入描述" ng-blur="updateTabInfo()"
                          hi-no-break hi-elastic  ng-model="data.tabs[local.current_tab_i].description"></textarea>
            </div>
        </div>
        <div class="col-md-offset-2 section-subtitle">
            主页内容
        </div>
        <div class="col-md-9">
            <div class="col-md-9 one-block side-block margin tab-group-block">
                <div class="item-list-container ungrouped">
                    <h4 class="list-title">未分配分组</h4>
                    <ul class="item-list">
                        <li ng-repeat="group in data.all_group" ng-show="!isGroupInAllTabs( group.group_id ) && group.status == 2">
                            <a href="" ng-click="addGroupToTab( group )">
                                <span ng-bind="group.group_id + '-' + group.name"></span>
                                <span class="i i-enter"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="item-list-container grouped">
                    <h4 class="list-title">已分配分组</h4>
                    <ul class="item-list">
                        <li ng-repeat="group in data.all_group"
                            ng-show="isGroupInAllTabs( group.group_id ) && !isGroupInTab( group.group_id ) && group.status == 2">
                            <a class="item-name" href="" ng-bind="group.group_id + '-' + group.name"></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9 one-block side-block margin tab-group-block">
                <div class="item-list-container ungrouped">
                    <h4 class="list-title">未分配广告条</h4>
                    <ul class="item-list">
                        <li ng-repeat="ad in data.ad_group" ng-show="!isAdInAllTabs( ad.group_id ) && ad.status == 2">
                            <a href="" ng-click="addGroupToTab( ad )">
                               <span ng-bind="ad.group_id + '-' + ad.name"></span>
                               <span class="i i-enter"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="item-list-container grouped">
                    <h4 class="list-title">已分配广告条</h4>
                    <ul class="item-list">
                        <li ng-repeat="ad in data.ad_group"
                            ng-show="isAdInAllTabs( ad.group_id ) && !isAdInTab( ad.group_id ) && ad.status == 2">
                            <a class="item-name" href="" ng-bind="ad.group_id + '-' + ad.name"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-9 one-block side-block tab-content-block">
                <div class="item-list-container ingroup">
                    <h4 class="list-title">包含分组</h4>
                    <ul class="item-list">
                        <li ng-repeat="group in data.tabs[local.current_tab_i].groups_order">
                            <input class="item-order form-control" ng-model="group.display_order"
                                   ng-value="{{ $index }} + 1" ng-blur="updateTabGroupOrder( group )" />
                            <span class="item-name" ng-bind="'[分组]'+group.group_id + '-' + group.name" ng-show="group.type != 6"></span>
                            <span class="item-name" ng-bind="'[广告]'+group.group_id + '-' + group.name" ng-hide="group.type != 6"></span>
                            <a href="" ><span class="i i-close" ng-click="deleteGroupFromTab( group.group_id )"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>