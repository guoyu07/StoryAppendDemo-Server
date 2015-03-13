<div class="states-section" ng-controller="priceTicketTypeCtrl" ng-if="!local.has_default_tickets">
    <hi-section-head options="local.section_head.ticket_type"></hi-section-head>
    <div class="section-body" ng-show="local.section_head.ticket_type.is_edit">
        <form name="ticket_type_form">
            <div class="row">
                <div class="col-md-6 section-subtitle">商品票种选择</div>
            </div>
            <div class="row">
                <div class="col-md-12 section-subbody">
                    <hi-radio-switch options="local.radio_options.ticket_type" model="data.ticket_type"></hi-radio-switch>
                </div>
            </div>

            <div ng-if="data.ticket_type.ticket_type == '1'">
                <div class="row">
                    <div class="col-md-6 section-subtitle">填写票种描述</div>
                </div>
                <div class="row">
                    <div class="col-md-10 section-subbody">
                        <input class="form-control" type="text" ng-model="data.ticket_type.ticket_rules[0].description" />
                    </div>
                </div>
            </div>

            <div ng-if="data.ticket_type.ticket_type == '2'">
                <div class="one-ticket-type row" ng-repeat="type in data.ticket_type.ticket_rules">
                    <div class="col-md-4">
                        {{local.ticket_type.map[ type.ticket_id ]}}
                    </div>
                    <div class="col-md-7">
                        <label class="col-md-6">年龄范围</label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" ng-model="type.age.begin" min="0" max="100" name="begin{{$index}}" required ng-value="type.age.begin" />
                        </div>
                        <div class="col-md-2 text-center">-</div>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" ng-model="type.age.end" min="0" max="100" name="end{{$index}}" required ng-value="type.age.end" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-4 text-right">描述</label>
                        <div class="col-md-14">
                            <input class="form-control" type="text" ng-model="type.description" />
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="data.ticket_type.ticket_type == '3'">
                <div class="row">
                    <div class="col-md-6 section-subtitle">
                        新增一个票种
                        <button class="btn toggle-btn tags-input-add" ng-click="addTicket()"></button>
                    </div>
                </div>

                <div class="one-ticket-type row section-subbody" ng-repeat="type in data.ticket_type.ticket_rules">
                    <div class="col-md-4">
                        <div class="dropdown">
                            <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                                {{local.ticket_type.map[ type.ticket_id ]}}
                                <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow"></span>
                            <ul class="dropdown-menu">
                                <li ng-repeat="(key, value) in local.ticket_type.map">
                                    <a class="status text-center" ng-bind="value" ng-click="changeTicketId(key, type)"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="col-md-6">年龄范围</label>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" min="0" max="100" ng-model="type.age.begin" ng-value="type.age.begin" required />
                        </div>
                        <div class="col-md-2 text-center">-</div>
                        <div class="col-md-5">
                            <input class="form-control text-center" type="number" min="0" max="100" ng-model="type.age.end" ng-value="type.age.end" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-5 text-right">描述</label>
                        <div class="col-md-13">
                            <input class="form-control" type="text" ng-model="type.description" />
                        </div>
                    </div>
                    <button class="col-md-1 btn btn-inverse del-btn btn-square" ng-click="deleteTicket( $index )">
                        删除
                    </button>
                </div>
            </div>
        </form>

    </div>
    <div class="section-body" ng-show="!local.section_head.ticket_type.is_edit">
        <div class="section-subtitle">
            商品包含：{{ local.radio_options.ticket_type.items[data.ticket_type.ticket_type] }}
            <span ng-show="data.ticket_type.ticket_type == 1">{{data.ticket_type.ticket_rules[0].description ? ' ( ' + data.ticket_type.ticket_rules[0].description + ' ) ' : '' }}</span>
        </div>
        <div class="section-subbody">
            <div class="row" ng-repeat="type in data.ticket_type.ticket_rules" ng-show="data.ticket_type.ticket_type > 1">
                <div class="col-md-2 text-right content-body">{{local.ticket_type.map[ type.ticket_id ]}}</div>
                <div class="col-md-15 content-body">
                    年龄范围：{{ type.age_range }} {{ type.description ? ' ( ' + type.description + ' ) ' : '' }}
                </div>
            </div>
        </div>
    </div>
</div>