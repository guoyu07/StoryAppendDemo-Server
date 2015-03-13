<script type="text/ng-template" id="editShippingConfig.html">
    <form name="voucher_config_form" novalidate>
        <div data-ng-include="'editShippingBasic.html'"></div>
        <div data-ng-include="'editShippingDetails.html'"></div>
    </form>
    <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
            data-ng-click="submitChanges()" data-ng-disabled="is_GTA || is_combo || is_CPIC">
        保存
    </button>
</script>

<script type="text/ng-template" id="editShippingBasic.html">
    <div class="edit-section clearfix">
        <sidebar name='editShippingConfig'></sidebar>
        <section class="col-xs-13 col-xs-offset-1 section-action">
            <div class="row edit-heading with-dot">
                <h2>请选择一种发货方式</h2>
            </div>
            <div class="row edit-body">
                <radio-switch options="radio_options.shipping_type" model="shippingRule"></radio-switch>
            </div>
        </section>
    </div>
</script>
<script type="text/ng-template" id="editShippingDetails.html">
    <div class="edit-section clearfix" data-ng-show="shippingRule.booking_type != 'STOCK'">
        <sidebar name='editShippingConfigDetails'></sidebar>
        <section class="col-xs-13 col-xs-offset-1 section-action">
            <div id="need_notify_supplier" data-ng-show="shippingRule.booking_type != 'STOCK'">
                <div class="row edit-heading with-dot">
                    <h2>发货时，是否通知供应商</h2>
                </div>
                <div class="row edit-body">
                    <radio-switch options="radio_options.need_notify_supplier" model="shippingRule"></radio-switch>
                </div>
            </div>
            <div data-ng-show="shippingRule.booking_type == 'EMAIL'
                || ((shippingRule.booking_type == 'HITOUR'||shippingRule.booking_type == 'B2B' || shippingRule.booking_type == 'EXCEL') && shippingRule.need_notify_supplier == '1')">
                <div class="row edit-heading with-dot">
                    <h2>请填写供应商Email邮件组</h2>
                </div>
                <div class="row edit-body">
                    <input type="text" class="col-xs-4 form-control input-sm"
                           data-ng-model="shippingRule.supplier_email" />
                </div>
                <div class="row edit-heading with-dot">
                    <h2>请选择Email语言</h2>
                </div>
                <div class="row edit-body">
                    <radio-switch options="radio_options.email_language" model="shippingRule"></radio-switch>
                </div>
            </div>

            <div data-ng-show="shippingRule.booking_type == 'EMAIL' || shippingRule.booking_type == 'B2B' || shippingRule.booking_type == 'EXCEL'">
                <div class="row edit-heading with-dot">
                    <h2>请选择供应商返回信息类型</h2>
                </div>
                <div class="row edit-body">
                    <radio-switch options="radio_options.feedback_type" model="shippingRule"></radio-switch>
                </div>
            </div>


            <div
                data-ng-show="shippingRule.supplier_feedback_type == '1' && (shippingRule.booking_type == 'EMAIL' || shippingRule.booking_type == 'B2B' || shippingRule.booking_type == 'EXCEL')">
                <div class="row edit-heading with-dot">
                    <h2>请选择Code类型</h2>
                </div>
                <div class="row edit-body">
                    <div class="shippingCheckBox">
                        <input type="checkbox" data-ng-model="check_boxs.need_supplier_booking_ref" />
                        <span>包含Supplier_BookingID</span>
                    </div>
                    <div class="shippingCheckBox">
                        <input type="checkbox" data-ng-model="check_boxs.confirmation_type" data-ng-change="confirmationTypeChanged()"/>
                        <span>包含Supplier_ConfirmCode</span>
                    </div>
                </div>
            </div>

            <div
                data-ng-show="((shippingRule.supplier_feedback_type == '1'&& check_boxs.confirmation_type == true) || shippingRule.supplier_feedback_type == '2' ) && (shippingRule.booking_type == 'EMAIL' || shippingRule.booking_type == 'B2B' || shippingRule.booking_type == 'EXCEL')">
                <div class="row edit-heading with-dot"
                     data-ng-show="shippingRule.supplier_feedback_type == '1'">
                    <h2>返回的Confirmation Code是几个？</h2>
                </div>

                <div class="row edit-heading with-dot"
                     data-ng-show="shippingRule.supplier_feedback_type == '2'">
                    <h2>返回的PDF Voucher是几个？</h2>
                </div>

                <div class="row edit-body">
                    <radio-switch options="radio_options.confirmation_type" model="shippingRule"></radio-switch>
                </div>
            </div>

            <div
                data-ng-show="shippingRule.supplier_feedback_type == '1' && (shippingRule.booking_type == 'EMAIL' || shippingRule.booking_type == 'B2B' || shippingRule.booking_type == 'EXCEL')">
                <div class="row edit-heading with-dot">
                    <h2>Voucher上的Code要如何显示</h2>
                </div>
                <div class="row edit-body">
                    <radio-switch options="radio_options.code_style" model="shippingRule"></radio-switch>
                </div>
            </div>


            <div data-ng-show="shippingRule.booking_type != 'HITOUR'">
                <div class="row edit-heading with-dot">
                    <h2>是否需要供应商附加信息</h2>
                </div>
                <div class="row edit-body">
                    <radio-switch options="radio_options.need_additional_info" model="shippingRule"></radio-switch>
                </div>
            </div>

        </section>
    </div>
</script>
