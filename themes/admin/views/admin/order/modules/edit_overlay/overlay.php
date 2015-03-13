<div class="overlay confirm" id="refund_overlay" ng-show="local.shipping.has_overlay" hi-after-load="initOverlay()">
    <div class="notify-container confirm" id="refund_container">
        <form name="refund_form">
            <div class="notify-head">退货／退款</div>
            <div class="notify-body">
                <div class="notify-subhead">
                    选择退货／退款类型
                </div>
                <div class="row pad-top">
                    <div class="col-md-6 col-md-offset-1">
                        <label class="hi-radio" ng-repeat="type in local.shipping.return_types"
                               ng-show="type.is_visible" ng-click="data.shipping.return_info.return_type = type.name">
                            <input type="radio" value="{{type.name}}" name="return_type"
                                   ng-model="data.shipping.return_info.return_type" required />
                            <span class="inner-text" ng-bind="type.label"></span>
                        </label>
                    </div>
                    <div class="col-md-10 refund-amount"
                         ng-if="['return_and_refund', 'partial_refund', 'record_refund'].indexOf(data.shipping.return_info.return_type) > -1">
                        <p class="small-desc">
                            订单支付金额：¥{{shipping.baseInfo.order.total | number : 2}}<br />
                            系统可退金额：¥{{data.payment.remain_refund_amount | number : 2}}
                        </p>
                        <label class="col-md-6" for="refund_amount">
                            退款金额
                        </label>
                        <div class="col-md-12">
                            <input type="number" name="refund_amount" class="form-control" required
                                   min="0" max="{{data.payment.remain_refund_amount}}"
                                   ng-model="data.shipping.return_info.refund_amount" refund-amount
                                   return="data.shipping.return_info" refundable="shipping.baseInfo.order.total" />
                        </div>
                    </div>
                </div>
                <div ng-if="['partial_refund', 'record_refund'].indexOf(data.shipping.return_info.return_type) > -1">
                    <div class="notify-subhead">
                        退款理由
                    </div>
                    <div class="row pad-top">
                        <div class="col-md-6 col-md-offset-1">
                            <label class="hi-radio" ng-repeat="type in local.shipping.refund_reason"
                                   ng-click="data.shipping.return_info.reason = type.reason">
                                <input type="radio" value="{{type.reason}}" name="refund_reason"
                                       ng-model="data.shipping.return_info.reason" required />
                                <span class="inner-text" ng-bind="type.label"></span>
                            </label>
                        </div>
                        <div class="col-md-10">
                            <label for="refund_comment">理由注释：</label>
                            <textarea class="form-control" rows="3" name="refund_comment"
                                      ng-model="data.shipping.return_info.comment"
                                      ng-required="data.shipping.return_info.reason == 2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="notify-foot">
            <button class="block-action btn btn-default" ng-click="toggleOverlay( false )">
                取消
            </button>
            <button class="block-action btn btn-inverse" ng-click="returnOrder()" ng-disabled="refund_form.$invalid">
                保存
                <span class="i i-refresh refresh-animate" ng-show="local.shipping.progress.return"></span>
            </button>
        </div>
    </div>
</div>