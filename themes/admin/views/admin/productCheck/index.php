<div class="container page-container" data-ng-controller="ProductCheckCtrl">
    <hi-tab options="local.tab_options"></hi-tab>

    <div class="col-md-18" data-ng-show="local.tab_path=='validate_all'">
        <h3 class="text-center">上线商品总体检查</h3>
        <strong>检查时间：{{ data.validate_all.check_time }}，检查最小间隔：1小时</strong>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-md-1">Product ID</th>
                    <th class="col-md-2">检查结果</th>
                </tr>
            </thead>
            <tbody>
                <tr data-ng-repeat="product in data.validate_all.data">
                    <td><a href="{{ local.product_edit_base_url + product.product_id}}" target="_blank">{{
                                                                                                        product.product_id
                                                                                                        }}</a></td>
                    <td> {{ product.error_msg }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-md-18" data-ng-show="local.tab_path=='date_rule'">
        <h3 class="text-center">上线商品日期规则检查</h3>
        <strong>检查时间：{{ data.date_rule.check_time }}&nbsp;&nbsp;&nbsp;&nbsp;检查最小间隔：1小时</strong><br/>
        <strong>检查条件：需要出行日期，售卖截止时间在14天内</strong>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-md-1">Product ID</th>
                    <th class="col-md-2">售卖截止时间</th>
                    <th class="col-md-6">Product Name</th>
                </tr>
            </thead>
            <tbody>
                <tr data-ng-repeat="product in data.date_rule.data">
                    <td><a href="{{ local.product_edit_base_url + product.product_id}}" target="_blank">{{
                                                                                                        product.product_id
                                                                                                        }}</a></td>
                    <td>{{product.to_date}}</td>
                    <td>{{ product.name }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>