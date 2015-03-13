<script type="text/ng-template" id="editVoucherRule.html">
    <div class="edit-section clearfix">
        <section class="col-xs-18 section-action gutter-padding">
            <div class="row edit-heading">
                <h2>Voucher预览</h2>
            </div>
        </section>
    </div>
    <form name="voucher_config_form" novalidate>
        <div data-ng-include="'editVoucherRuleBasic.html'"></div>
        <div data-ng-include="'editVoucherRuleOthers.html'"></div>
    </form>
    <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
            data-ng-click="submitChanges()" data-ng-disabled="is_GTA || is_combo">
        保存
    </button>
</script>


<script type="text/ng-template" id="editVoucherRuleBasic.html">
    <div class="edit-section clearfix">
        <sidebar name='editVoucherRule'></sidebar>
        <section class="col-xs-13 col-xs-offset-1 section-action">
            <div class="row edit-heading with-dot">
                <h2>VOUCHER显示语言</h2>
            </div>
            <div class="row edit-body">
                <radio-switch options="radio_options.language" model="voucherConfig"></radio-switch>
            </div>

            <div class="row edit-heading with-dot">
                <h2>VOUCHER需要出现的出行人信息</h2>
            </div>

            <div data-ng-show="rule_group == 'leader' || rule_group == 'leader_and_everyone'">
                <div class="one-location-group-selection one-passenger-edit-box input-section">
                    <h4>领队信息</h4>
                    <button class="btn one-criteria one-allcriteria criteria-with-x"
                            data-ng-repeat="l_rule in leader_rules"
                            data-ng-click="toggleItem(l_rule.ticket_id, curr_leader_rules)"
                            data-ng-class="{ checked: curr_leader_rules.indexOf(l_rule.ticket_id) == -1 }">
                        {{l_rule.label}}
                    </button>
                </div>
            </div>

            <div data-ng-show="rule_group == 'everyone' || rule_group == 'leader_and_everyone'">
                <div data-ng-repeat="o_rule in other_rules">
                    <div class="one-location-group-selection one-passenger-edit-box input-section">
                        <h4>{{o_rule.infos.cn_name}}信息</h4>
                        <button class="btn one-criteria one-allcriteria criteria-with-x"
                                data-ng-repeat="item in o_rule.origin_rule"
                                data-ng-click="toggleItem(item.ticket_id, o_rule.current_rule)"
                                data-ng-class="{ checked: o_rule.current_rule.indexOf(item.ticket_id) == -1 }">
                            {{item.label}}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</script>
<script type="text/ng-template" id="editVoucherRuleOthers.html">
    <div class="edit-section clearfix last">
        <sidebar name='editVoucherOthers'></sidebar>
        <section class="col-xs-13 col-xs-offset-1 section-action">
            <div class="row edit-heading with-dot">
                <h2>是否显示支付声明?</h2>
            </div>
            <div class="row edit-body">
                <radio-switch options="radio_options.payable_by" model="voucherConfig"></radio-switch>
                <div class="payable_cert" data-ng-show="voucherConfig.need_pay_cert == 1">
                    <div class="form-group col-xs-8">
                        <input type="text" data-ng-model="voucherConfig.pay_cert" placeholder="供应商支付声明"
                               class="form-control">
                    </div>
                </div>
            </div>
            <div class="row edit-heading with-dot">
                <h2>是否显示供应商原始商品名称？</h2>
            </div>
            <div class="row edit-body">
                <radio-switch options="radio_options.origin_name" model="voucherConfig"></radio-switch>
            </div>
            <div class="row edit-heading with-dot">
                <h2>是否需要客户签名？</h2>
            </div>
            <div class="row edit-body">
                <radio-switch options="radio_options.signature" model="voucherConfig"></radio-switch>
            </div>
            <div class="row edit-heading with-dot">
                <h2>辅助信息</h2>
            </div>
            <div class="row edit-body">
                <button class="btn btn-primary btn-square btn-inverse" id="btn_upload" data-ng-click="triggerUpload()">
                    <span class="glyphicon glyphicon-plus"></span> 上传辅助信息PDF
                    <span class="glyphicon glyphicon-refresh refresh-animate" data-ng-show="local.check_upload_progress == true"></span>
                </button>
                <div class="row grid-top">
                    <div
                        data-ng-repeat="pdf in voucherConfig.attached_pdf"
                        data-ng-init="vindex = $index">
                        <a
                            style="color: #505050; text-decoration: underline;" target="_blank"
                            ng-href="{{ pdf.pdf_path }}">{{ pdf.pdf_name }}</a>
                        <span class="fui-cross color-red" style="color: red;cursor: hand"
                              data-ng-click="delPdf(voucherConfig.product_id, pdf.pdf_name, vindex)"></span>
                    </div>

                </div>
                <input id="pdf-upload" type="file" class="hidden" nv-file-select uploader="uploader" accept="application/pdf" />
            </div>
        </section>
    </div>
</script>