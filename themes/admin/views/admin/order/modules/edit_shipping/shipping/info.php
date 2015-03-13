<!--基本信息-->
<div class="states-section basic-info" ng-class="{ 'hide-btns' : !local.shipping.allow_edit_basicinfo }" hi-after-load="setChosen()">
    <hi-section-head options="local.section_head.basic_info"></hi-section-head>
    <!--编辑态-->
    <div class="section-body row" ng-class="local.section_head.basic_info.getClass()" ng-show="local.section_head.basic_info.is_edit">
        <form name="basic_info">
            <table class="forms-table">
                <tr ng-repeat="record in data.shipping.basic_info">
                    <td class="col-md-5">
                        <label ng-bind="record.label"></label>
                    </td>
                    <td>
                        <div ng-if="record.allow_edit == false" ng-bind-html="record.html"></div>
                        <div class="departure" ng-if="record.allow_edit == true && record.id == 'departure_point'">
                            <select chosen
                                    ng-model="record.data"
                                    ng-options="dep for dep in shipping_product_info[shipping.baseInfo.product_id].departure.list"
                                    no-results-text="'没有找到'"
                                    data-placeholder="选择一个departure">
                            </select>
                            <span class="pad-left"
                                  ng-show="shipping_product_info[shipping.baseInfo.product_id].departure.list.indexOf( record.data ) == -1">没有可用的{{record.label}}</span>
                        </div>
                        <div ng-if="record.allow_edit == true && record.id == 'tour_date'">
                            <quick-datepicker ng-model='record.date' disable-timepicker='true' date-format='yyyy-M-d'
                                              on-change='shipping_product_info[shipping.baseInfo.product_id].tour_date.fetchDepartureFromTourDate(record.date)'
                                              date-filter='shipping_product_info[shipping.baseInfo.product_id].tour_date.dateFilter'></quick-datepicker>
                            <span class="pad-left" ng-bind="record.comment"></span>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!--显示态-->
    <div class="section-body row" ng-class="local.section_head.basic_info.getClass()"
         ng-hide="local.section_head.basic_info.is_edit">
        <table class="forms-table">
            <tr ng-repeat="record in data.shipping.basic_info">
                <td class="view-title col-md-4" ng-bind="record.label"></td>
                <td class="view-body row-align-left" ng-bind-html="record.html"></td>
            </tr>
        </table>
    </div>
</div>