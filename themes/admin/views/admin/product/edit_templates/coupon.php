<script type="text/ng-template" id="editProductCoupon.html">
    <div class="edit-section clearfix">
        <section class="col-xs-14 col-xs-offset-2 section-action">
            <div class="nav-row text-center grid-top">
                <div class="btn-group">
                    <button class="btn btn-primary" ng-click="changeCouponTab( $index )" ng-class="{ 'selected': local.current_menu == $index }" ng-repeat="item in local.menus">
                        {{$index + 1}}. {{item.label}}
                    </button>
                </div>
            </div>
            <div class="row">
                <button class="col-xs-offset-7 col-xs-4 btn btn-inverse" ng-show="local.current_menu == 0" ng-click="addTemplate()" >
                    新增
                </button>
            </div>
            <div class="one-coupon-template" ng-repeat="template in data.templates" ng-show="local.menus[local.current_menu].status == template.status">
                <a ng-href="{{ local.edit_template }}{{ template.id }}" class="i i-edit edit-link">编辑</a>
                <table>
                    <tr class="dark-bg">
                        <th>模版名称</th>
                        <td>
                            <a ng-href="{{ local.edit_template }}{{ template.coupon_id }}">{{ template.coupon_id }} / {{ template.template_coupon.name }}</a>
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
        </section>
    </div>
</script>