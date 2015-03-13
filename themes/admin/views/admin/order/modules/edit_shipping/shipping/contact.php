<!--联系人信息-->
<div class="states-section contact-info clearfix">
    <hi-section-head options="local.section_head.contact_info"></hi-section-head>
    <!--编辑态-->
    <div class="section-body" ng-class="local.section_head.contact_info.getClass()" ng-show="local.section_head.contact_info.is_edit">
        <form name="contact_info">
            <table class="forms-table">
                <tr ng-repeat="record in data.shipping.contact_info">
                    <td class="col-md-4">
                        <label ng-bind="record.label"></label>
                    </td>
                    <td>
                        <span ng-bind="record.data" ng-if="record.allow_edit == false"></span>
                        <input type="text" class="form-control" ng-if="record.allow_edit == true" ng-model="record.data" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <!--显示态-->
    <div class="section-body" ng-class="local.section_head.contact_info.getClass()"
         ng-hide="local.section_head.contact_info.is_edit">
        <table class="forms-table">
            <tr ng-repeat="record in data.shipping.contact_info">
                <td class="view-title col-md-4" ng-bind="record.label"></td>
                <td class="view-body row-align-left">
                    <pre ng-bind="record.data" style="display: inline-block"></pre>
                    <button class="btn btn-inverse ng-scope" style="margin-top: -16px" ng-if="record.id == 'contacts_email' && (shipping.baseInfo.status_id == 4 || shipping.baseInfo.status_id == 5)" ng-click="orderProcessingMail()">
                        重发订单处理中邮件
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>