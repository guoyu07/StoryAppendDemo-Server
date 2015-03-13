<script type="text/ng-template" id="productFeedback.html">
    <div class="states-section" id="feedback-container">
        <div class="row">
            <ul class="nav nav-tabs duration-selection">
                <li ng-repeat="one_duration in local.durations_filters" ng-class="{active: local.current_duration == one_duration.key}" ng-click="switchDuration(one_duration.key)">
                    <a show-tab data-toggle="tab" style="cursor: pointer;" ng-bind="one_duration.name"></a>
                </li>
            </ul>
        </div>

        <div class="row duration-selection" ng-show="local.current_duration == 'custom_duration'">
            <div class="col-md-4">
                <quick-datepicker ng-model='local.duration_from_date' on-change='updateDuration()'
                                  disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
            </div>
            <div class="col-md-1 text-right">
                —
            </div>
            <div class="col-md-4">
                <quick-datepicker ng-model='local.duration_to_date' on-change='updateDuration()'
                                  disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label>统计提问数</label>
                <div class="total-static" ng-bind="local.total_question_count"></div>
            </div>
        </div>
        <div ng-show="local.current_duration != 'todo'">

            <div class="row">
                <div class="form-group col-md-3 grid-top">
                    <select chosen
                            style="width: 100%;"
                            ng-model="local.query_filter.country_code"
                            ng-change="filterStatics()"
                            ng-options="country.country_code as country.cn_name group by country.group for country in local.country_list track by country.country_code"
                            data-placeholder="选择国家"
                            no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="form-group col-md-3 grid-top">
                    <select chosen
                            style="width: 100%;"
                            ng-model="local.query_filter.city_code"
                            ng-change="filterStatics()"
                            ng-options="city.city_code as city.city_name group by city.group for city in local.city_list track by city.city_code"
                            data-placeholder="选择城市"
                            no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="form-group col-md-3 grid-top">
                    <select chosen
                            style="width: 100%;"
                            ng-model="local.query_filter.supplier_id"
                            ng-change="filterStatics()"
                            ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in local.supplier_list track by supplier.supplier_id"
                            data-placeholder="选择供应商"
                            no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control grid-top" ng-model="local.query_filter.product"
                           placeholder="搜索商品ID/名称" hi-enter="filterStatics()" />
                </div>
                <div class="col-md-1">
                    <button class="btn btn-inverse pull-right grid-top" ng-click="filterStatics()">搜索</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-18">
                    <h4>
                        提问
                        <span class="i i-refresh refresh-animate" ng-show="local.product_grid_options.in_progress"></span>
                    </h4>
                    <hi-grid options="local.ask_grid_options"></hi-grid>
                </div>
            </div>
        </div>
        <div ng-show="local.current_duration == 'todo'">
            <div class="row">
                <div class="col-md-18">
                    <h4>
                        提问
                    </h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 7%;">商品ID</th>
                                <th style="width: 20%;">商品名称</th>
                                <th style="width: 20%;">提问</th>
                                <th style="width: 10%;">处理人</th>
                                <th style="width: 10%;">约定回答时间</th>
                                <th style="width: 7%;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="fb in local.todo_feedback">
                                <td>
                                    {{ fb.product_id }}
                                </td>
                                <td>
                                    {{ fb.name }}
                                </td>
                                <td>
                                    {{ fb.question }}
                                </td>
                                <td>
                                    {{ fb.screen_name }}
                                </td>
                                <td>
                                    {{ fb.date_expected }}
                                </td>
                                <td>
                                    <button class="btn btn-inverse" ng-click="editAnswer(fb.ask_id)">
                                        回复
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="overlay" ng-show="local.edit_answer">
                <div class="answer-block notify-container">
                    <div class="dialog-title">
                        {{local.todo_feedback[local.dialog_todo_index].product_id}}-{{local.todo_feedback[local.dialog_todo_index].name}}</div>
                    <div class="dialog-question">
                        用户提问:
                        <div class="height-control">{{local.todo_feedback[local.dialog_todo_index].question}}</div>
                    </div>
                    <div class="dialog-contact clearfix">
                        <div class="left-contact">用户姓名:</div>
                        <div class="right-contact grid-bottom">
                            {{local.todo_feedback[local.dialog_todo_index].contact_name}}
                        </div>
                    </div>
                    <div class="dialog-contact clearfix">
                        <div class="left-contact">用户联系方式:</div>
                        <div class="right-contact">
                            <div class="style-contact" ng-show="local.todo_feedback[local.dialog_todo_index].contact_phone">
                                电话:{{local.todo_feedback[local.dialog_todo_index].contact_phone}}
                            </div>
                            <div class="style-contact" ng-show="local.todo_feedback[local.dialog_todo_index].contact_qq">
                                QQ:{{local.todo_feedback[local.dialog_todo_index].contact_qq}}
                            </div>
                            <div class="style-contact" ng-show="local.todo_feedback[local.dialog_todo_index].contact_weixin">
                                微信:{{local.todo_feedback[local.dialog_todo_index].contact_weixin}}
                            </div>
                            <div class="style-contact" ng-show="local.todo_feedback[local.dialog_todo_index].contact_mail">
                                邮箱:{{local.todo_feedback[local.dialog_todo_index].contact_mail}}
                            </div>
                        </div>
                    </div>
                    <div class="dialog-answer">
                        玩途回复:
                        <textarea ng-model="local.todo_feedback[local.dialog_todo_index].answer"
                                  class="form-control disabled-text height-control" required
                                  placeholder="请填写回复"></textarea>
                    </div>
                    <div class="dialog-footer clearfix">
                        <div class="col-md-9 cancel-btn" ng-click="saveAnswer(false)">取消</div>
                        <div class="col-md-9 confirm-btn" ng-click="saveAnswer(true)">确定</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>