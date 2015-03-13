<script type="text/ng-template" id="ProductFeedback.html">
    <div class="states-section grid-top product-feedback">
        <div class="section-head">
            <h2 class="section-title">Q&A</h2>
        </div>
        <div class="col-md-offset-1 col-md-16">
            <div class="row">
                <button class="col-md-offset-7 col-md-4 btn feedback-btn i i-plus" ng-click="addFeedback()">
                    添加提问
                </button>
            </div>
            <form name="feedback_list_form" novalidate>
                <div class="feedback-block" ng-repeat="ask in data.asks track by $index" ng-init="ask_index = $index">
                    <div class="delete-block">
                        <span class="i i-close" ng-click="deleteFeedback(ask_index)"></span>
                    </div>
                    <div class="block-head clearfix">
                        提问类型:
                        <div class="feedback-type" ng-hide="ask.is_edit">{{local.radio_switch.type_switch.items[ask.ask_type]}}</div>
                        <div class="feedback-type" ng-show="ask.is_edit">
                            <div hi-radio-switch options="local.radio_switch.type_switch" model="ask"></div>
                        </div>

                        <div class="feedback-date">{{ask.date_added}}</div>
                        <div class="feedback-recorder">{{ask.user.screen_name}}</div>
                    </div>
                    <div class="block-question">
                         用户提问:
                        <textarea ng-model="ask.question"
                                  class="form-control disabled-text height-control" required
                                  placeholder="请填写问题"
                                  ng-disabled="ask.is_edit == false"></textarea>
                    </div>
                    <div class="block-backdate">
                        约定回复日期:
                        <div class="feedback-type" ng-hide="ask.is_edit">{{ask.date_expected}}</div>
                        <div class="feedback-type" ng-show="ask.is_edit">
                            <quick-datepicker ng-model='ask.date_expected'
                                              disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                    </div>
                    <div class="block-contact clearfix">
                        <div class="left-contact">用户姓名:</div>
                        <div class="right-contact grid-bottom">
                            <input class="form-control disabled-text contact-text" ng-model="ask.contact_name" ng-disabled="!ask.is_edit"/>
                        </div>
                        <div class="left-contact">用户联系方式:</div>
                        <div class="right-contact" ng-hide="ask.is_edit">
                            <div class="style-contact" ng-show="ask.contact_phone">
                                电话:{{ask.contact_phone}}
                            </div>
                            <div class="style-contact" ng-show="ask.contact_qq">
                                QQ:{{ask.contact_qq}}
                            </div>
                            <div class="style-contact" ng-show="ask.contact_weixin">
                                微信:{{ask.contact_weixin}}
                            </div>
                            <div class="style-contact" ng-show="ask.contact_mail">
                                邮箱:{{ask.contact_mail}}
                            </div>
                        </div>
                        <div class="right-contact" ng-show="ask.is_edit">
                            <div class="grid-bottom">
                                <div class="type-label" ng-class="{'type-on':type.value == ask.contact_way}"
                                     ng-click="switchContactType($index, ask_index)" ng-repeat="type in local.contact_types">
                                    {{type.label}}
                                    <span class="i-check" ng-show="ask[type.value]"></span>
                                </div>
                            </div>
                            <input class="form-control contact-text" ng-model="ask.contact_phone" ng-show="ask.contact_way == 'contact_phone'"/>
                            <input class="form-control contact-text" ng-model="ask.contact_qq" ng-show="ask.contact_way == 'contact_qq'"/>
                            <input class="form-control contact-text" ng-model="ask.contact_weixin" ng-show="ask.contact_way == 'contact_weixin'"/>
                            <input class="form-control contact-text" ng-model="ask.contact_mail" ng-show="ask.contact_way == 'contact_mail'"/>
                        </div>
                    </div>
                    <div class="block-answer">
                        玩途回复:
                        <textarea ng-model="ask.answer"
                                  class="form-control disabled-text height-control grid-bottom" required
                                  placeholder="请填写回复"
                                  ng-disabled="ask.is_edit == false"></textarea>
                        是否上线:
                        <div class="feedback-type" ng-hide="ask.is_edit">{{local.radio_switch.line_switch.items[ask.is_online]}}</div>
                        <div class="feedback-type" ng-show="ask.is_edit">
                            <div hi-radio-switch options="local.radio_switch.line_switch" model="ask"></div>
                        </div>
                    </div>
                    <div class="i block-btn"
                          ng-class="{ 'i-edit': ask.is_edit == false, 'i-save': ask.is_edit == true }"
                          ng-click="toggleEdit(ask_index)"></div>
                </div>
            </form>
        </div>
    </div>
</script>