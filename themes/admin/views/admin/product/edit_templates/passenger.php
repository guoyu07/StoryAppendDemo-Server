<script type="text/ng-template" id="selectPassengerInfoType.html">
    <sidebar name="selectPassengerInfoType"></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action row">
        <a class="col-xs-5 passenger-info-type-box" data-ng-repeat="(key, value) in passenger_info_types"
           data-ng-class="{ active: currentType == key }" data-ng-href="#/editPassengerInfo/{{key}}">
            <h3>{{value.title}}</h3>

            <p>{{value.description}}</p>
        </a>
    </section>
</script>
<script type="text/ng-template" id="editPassengerInfo.html">
    <div ng-if="data.type != '8'" ng-controller="editNormalPassengerInfoCtrl">
        <sidebar name="selectPassengerInfoType"></sidebar>
        <section class="col-xs-13 col-xs-offset-1 section-action">
            <div class="row edit-heading">
                <h2>{{current_types.title}}</h2>
                <a href="#/selectPassengerInfoType"><span class="glyphicon glyphicon-arrow-left"></span>重新选择类型</a>
                <p>{{current_types.description}}</p>
            </div>
            <div data-ng-show="current_types.need_info.need_lead == '1'">
                <div passenger-info-select-box names="lead_info" allcriteria="data.all_criteria"
                     fieldcriteria="data.other_rules.lead_field_items">
                </div>
            </div>
            <div data-ng-repeat="one_rule in data.other_rules.rule_item"
                 data-ng-show="current_types.need_info.need_passenger_num == '0'">
                <div passenger-info-select-box names="one_rule.ticket_type" allcriteria="data.all_criteria"
                     fieldcriteria="one_rule.field_items"></div>
            </div>
        </section>
        <button class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form" data-ng-click="submitChanges()"
                data-ng-disabled="is_GTA">
            保存
        </button>
    </div>
    <div ng-if="data.type == '8'" ng-controller="editPackagePassengerInfoCtrl">
        <div class="package-passenger-rule-container">
            <button class="col-xs-offset-14 col-xs-2 btn update-btn" data-ng-click="refreshData()">
                更新
            </button>
            <section class="col-xs-13 col-xs-offset-3 section-action">
                <div data-ng-show="data.need_lead == '1'">
                    <div class="one-location-group-selection one-passenger-edit-box input-section">
                        <h4>领队信息</h4>
                        <button class="btn one-criteria one-allcriteria"
                                data-ng-repeat="l_rule in local.lead_field_array"
                                data-ng-click="toggleItem(l_rule.item_id, 'lead_field')"
                                data-ng-class="{ checked: local.lead_hidden_array.indexOf(l_rule.item_id) != -1 }">
                            {{l_rule.label}}
                        </button>
                    </div>
                </div>
                <div data-ng-show="data.need_passenger_num == '0'">
                    <div class="one-location-group-selection one-passenger-edit-box input-section">
                        <h4>出行人信息</h4>
                        <button class="btn one-criteria one-allcriteria"
                                data-ng-repeat="l_rule in local.other_field_array"
                                data-ng-click="toggleItem(l_rule.item_id, 'other_field')"
                                data-ng-class="{ checked: local.other_hidden_array.indexOf(l_rule.item_id) != -1 }">
                            {{l_rule.label}}
                        </button>
                    </div>
                </div>
            </section>
            <button class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form" data-ng-click="submitChanges()"
                    data-ng-disabled="is_GTA">
                保存
            </button>
        </div>
    </div>
</script>