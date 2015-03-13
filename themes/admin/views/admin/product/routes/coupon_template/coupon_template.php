<script type="text/ng-template" id="CouponTemplate.html">
    <div class="state-section grid-top">
        <hi-tab options="local.tab_options"></hi-tab>
        <div class="row">
            <button class="col-xs-offset-7 col-xs-4 btn btn-inverse" ng-show="local.tab_options.current_tab.status == 1" ng-click="addTemplate()" >
                新增
            </button>
        </div>
        <div class="one-coupon-template" ng-repeat="template in data.templates" ng-show="local.tab_options.current_tab.status == template.status">
            <a target="_blank" class="i i-edit edit-link" ng-href="{{ local.edit_template }}{{ template.id }}">编辑</a>
            <table>
                <tr class="dark-bg">
                    <th>模版名称</th>
                    <td>
                        <a target="_blank" ng-href="{{ local.edit_template }}{{ template.coupon_id }}">
                            {{ template.coupon_id }} / {{ template.template_coupon.name }}
                        </a>
                    </td>
                </tr>
                <tr class="dark-bg">
                    <th>模版描述</th>
                    <td ng-bind="template.template_coupon.description"></td>
                </tr>
                <tr>
                    <th>折扣金额</th>
                    <td ng-bind="template.discount_str"></td>
                </tr>
                <tr>
                    <th>使用日期</th>
                    <td ng-bind="template.date_str"></td>
                </tr>
                <tr>
                    <th>使用条件</th>
                    <td ng-bind="template.usage_str"></td>
                </tr>
            </table>
        </div>
    </div>
</script>