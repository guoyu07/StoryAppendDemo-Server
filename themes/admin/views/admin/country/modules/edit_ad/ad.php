<div class="states-section country-ad clearfix" ng-show="local.current_menu == '3'">
    <button class="btn btn-inverse add-btn" ng-click="addAd()">添加</button>
    <div class="section-head">
        <h2 class="section-title">广告条</h2>
    </div>
    <div class="section-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 8%;">广告条ID</th>
                    <th style="width: 30%;">广告条图片(1400*400)</th>
                    <th style="width: 10%;">广告条名称</th>
                    <th style="width: 20%;">广告条文字</th>
                    <th style="width: 20%;">广告条链接</th>
                    <th style="width: 12%;">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="ad in data.ad_group">
                    <td>
                        <span ng-bind="ad.group_id"></span>
                    </td>
                    <td>
                        <hi-uploader options="ad.uploader"></hi-uploader>
                    </td>
                    <td>
                        <textarea ng-model="ad.name" class="form-control grid-top disabled-text" rows="3" ng-blur="updateAdInfo(ad.group_id)"></textarea>
                    </td>
                    <td>
                        <textarea ng-model="ad.summary" class="form-control grid-top disabled-text" rows="3" ng-blur="updateAdInfo(ad.group_id)"></textarea>
                    </td>
                    <td>
                        <textarea ng-model="ad.link_url" class="form-control grid-top disabled-text" rows="3" ng-blur="updateAdInfo(ad.group_id)"></textarea>
                    </td>
                    <td>
                        <button class="block-action add btn btn-inverse delete-btn" ng-click="deleteAd(ad.group_id)">
                            删除
                        </button>
                        <div class="all-status">
                            <button class="btn dropdown-toggle tab-status" data-toggle="dropdown"
                                    ng-class="{ 'btn-default' : ad.status == '1', 'btn-inverse' : ad.status == '2' }"
                                    ng-bind="local.group_status[ ad.status ].label">
                                <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse drop-menu">
                                <li ng-repeat="(status_id, status) in local.group_status">
                                    <a ng-click="updateAdStatus(ad.group_id, status_id)" ng-bind="status.label"></a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>