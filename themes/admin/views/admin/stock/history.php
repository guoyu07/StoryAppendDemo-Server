<div id="stock-history-container" ng-controller="StockHistoryCtrl" class="container page-container">
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">商品库存信息</h2>
        </div>
        <div class="section-body view">
            <table class="forms-table">
                <tbody>
                    <tr>
                        <td class="view-title">
                            <label>城市</label>
                        </td>
                        <td class="view-body" ng-bind="data.history.product_info.city_name"></td>
                    </tr>
                    <tr>
                        <td class="view-title">
                            <label>供应商</label>
                        </td>
                        <td class="view-body" ng-bind="data.history.product_info.supplier"></td>
                    </tr>
                    <tr>
                        <td class="view-title">
                            <label>商品</label>
                        </td>
                        <td class="view-body">
                            <a href="{{ local.product_url }}{{ data.history.product_info.product_id }}">
                                {{ data.history.product_info.product_id }} &nbsp; {{
                                data.history.product_info.product_name }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">商品库存历史</h2>
        </div>
        <div class="section-body">
            <table class="table table-striped table-hover">
                <thead>
                    <th>票种</th>
                    <th>上传文件名称</th>
                    <th>上传时间</th>
                    <th>成功上传票数</th>
                    <th>状态</th>
                    <th>操作</th>
                </thead>
                <tbody>
                    <tr ng-repeat="item in data.history.history_list">
                        <td ng-bind="item.ticket_name"></td>
                        <td ng-bind="item.source_filename"></td>
                        <td ng-bind="item.upload_time"></td>
                        <td ng-bind="item.confirmed_count"></td>
                        <td>
                            <span class="grid-status" ng-bind="local.stock_status[item.status].label"
                                  ng-class="local.stock_status[item.status].class_name"></span>
                        </td>
                        <td>
                            <button class="btn btn-inverse block-action" ng-show="item.status == 1"
                                    ng-click="showDuplicate( item )">查看重复
                            </button>
                            <button class="btn btn-inverse block-action" ng-show="item.status == 2"
                                    ng-click="showInspect( item )">抽检
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    include_once(__DIR__ . '/modules/stock_list/partial.php');
    ?>
</div>