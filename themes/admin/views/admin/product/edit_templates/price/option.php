<script type="text/ng-template" id="editPriceOption.html">
    <div class="view-edit-section last clearfix" data-ng-controller="editPriceOptionCtrl">
        <section class="one-section-action">
            <form name="price_option_form" novalidate>
                <div class="row edit-heading">
                    <h2>售卖方式</h2>
                    <button class="col-md-3 col-md-offset-8 btn btn-sharp"
                            data-ng-hide="priceOptionEditing == true"
                            data-ng-click="priceOptionEditClick()">
                        编辑
                    </button>
                    <button type="submit" class="col-md-3 col-md-offset-8 btn btn-sharp"
                            data-ng-click="submitPriceOptionChanges()"
                            data-ng-hide="priceOptionEditing == false">
                        保存
                    </button>
                </div>
                <div class="row edit-body price-option-Info" data-ng-hide="priceOptionEditing == true">
                    <div class="row">
                        <label data-ng-show="option_rule.sale_rule.sale_in_package == 1"><h4 class="option-detail detail-background">此商品按套售卖</h4></label>
                        <label data-ng-show="option_rule.sale_rule.sale_in_package == 0"><h4 class="option-detail detail-background">此商品不按套售卖</h4></label>
                    </div>

                    <div class="row" data-ng-show="option_rule.sale_rule.sale_in_package == 1">
                        <label class="col-md-1"><h4>包含</h4></label>
                        <label><h4 class="option-detail detail-background">&nbsp;{{rulesDescription.resultStr}}</h4></label>
                    </div>
                    <div class="row" data-ng-show="option_rule.sale_rule.sale_in_package == 0 && rulesDescription.ticketTypeAmount == 2">
                        <div class="row" data-ng-repeat="ticket_rule in option_rule.ticket_rule">
                            <label class="col-md-3"><h4>{{ ticket_rule.is_independent == "1" ? "可单独购买" : "不可单独购买" }}</h4></label>
                            <label><h4 class="option-detail detail-background">&nbsp;{{ ticket_rule.ticket_type.cn_name }}票</h4></label>
                        </div>
                    </div>
                    <div data-ng-show="option_rule.sale_rule.sale_in_package == 0 && rulesDescription.ticketTypeAmount == 3">
                        <div class="row" data-ng-show="rulesDescription.hasSingle">
                            <label class="col-md-3"><h4>可单独购买</h4></label>
                            <label><h4 class="option-detail detail-background">{{rulesDescription.single}}</h4></label>
                        </div>
                        <div class="row" data-ng-show="rulesDescription.hasCantSingle">
                            <label class="col-md-3"><h4>不可单独购买</h4></label>
                            <label><h4 class="option-detail detail-background">{{rulesDescription.cantSingle}}</h4></label>
                        </div>
                    </div>
                    <div class="row">
                        <h4 class="option-detail">
                            此票{{option_rule.sale_rule.min_num}}{{rulesDescription.unit}}起定,
                            <span data-ng-show="option_rule.sale_rule.sale_in_package == 0 && option_rule.ticket_rule[child_index].is_independent == 0">
                                最少包含{{option_rule.ticket_rule[adult_index].min_num}}个成人,
                            </span>
                            最多一次性购买{{option_rule.sale_rule.max_num}}{{rulesDescription.unit}}
                        </h4>
                    </div>
                </div>


                <div class="row edit-body" data-ng-hide="priceOptionEditing == false">
                    <div class="row">
                        <div class="col-md-6 title-text">此商品是否按套出售？</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 edit-content last-content">
                            <radio-switch options="radio_options.is_packaged"
                                          model="option_rule.sale_rule"></radio-switch>
                        </div>
                    </div>
                    <div data-ng-show="option_rule.sale_rule.sale_in_package == 1">
                        <div class="row">
                            <div class="col-md-8 title-text">您需要定义每套商品包含下列票种的数量</div>
                        </div>

                        <div data-ng-repeat="ticket in option_rule.package_rule" class="row">
                            <div class="edit-content option-ticket-item textStyle">
                                <label class="col-md-1">包含</label>
                                <label class="col-md-2 text-emphasis">{{ticket.ticket_type.cn_name}}票</label>
                                <div class="col-md-2 input_margin">
                                    <input type="text" class="form-control input-sm" data-ng-model="ticket.quantity" />
                                </div>
                                <label class="col-md-10">张</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="edit-content package-min-max-container textStyle">
                                <label class="col-md-3">起定套数为</label>
                                <div class="col-md-2 input_margin">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.sale_rule.min_num" />
                                </div>
                                <label class="col-md-3">最大购买套数</label>
                                <div class="col-md-2 input_margin">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.sale_rule.max_num" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div
                         data-ng-show="option_rule.sale_rule.sale_in_package == 0">

                        <div data-ng-show="rulesDescription.ticketTypeAmount == 2">
                            <div class="row">
                                <div class="col-md-6 title-text">是否可以单独售卖成人票？</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 edit-content last-content">
                                    <radio-switch options="radio_options.adult_only"
                                                  model="option_rule.ticket_rule[adult_index]"></radio-switch>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 title-text">是否可以单独售卖儿童票？</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 edit-content last-content">
                                    <radio-switch options="radio_options.child_only"
                                                  model="option_rule.ticket_rule[child_index]"></radio-switch>
                                </div>
                            </div>
                        </div>

                        <div data-ng-show="rulesDescription.ticketTypeAmount == 3">
                            <div class="row">
                                <div class="col-md-6 title-text">请勾选下列可单独购买的票种</div>
                            </div>
                            <div data-ng-repeat="ticket in option_rule.ticket_rule" class="row">
                                <div class="edit-content textStyle">
                                    <input type="checkbox" data-ng-model="ticket.is_independent" />
                                    <span>可单独购买   {{ticket.ticket_type.cn_name}}票</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="edit-content package-min-max-container textStyle">
                                <label class="col-md-2">起定人数</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.sale_rule.min_num" />
                                </div>
                                <label class="col-md-3">最大购买人数</label>
                                <div class="col-md-2 input_margin">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.sale_rule.max_num" />
                                </div>
                            </div>
                        </div>
                        <div class="row" data-ng-show="option_rule.ticket_rule[child_index].is_independent == 0">
                            <div class="edit-content option-ticket-item textStyle">
                                <label class="col-md-2">最少包含</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.ticket_rule[adult_index].min_num" />
                                </div>
                                <label class="col-md-2">个成人</label>
                            </div>
                        </div>
                        <div class="row" data-ng-show="option_rule.ticket_rule[adult_index].is_independent == 0">
                            <div class="edit-content option-ticket-item textStyle">
                                <label class="col-md-2">最少包含</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control input-sm"
                                           data-ng-model="option_rule.ticket_rule[child_index].min_num" />
                                </div>
                                <label class="col-md-2">个儿童</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</script>