<h2 class="text-center pad-bottom pad-top">
    订单备注
    <button class="block-action add btn btn-inverse grid-left" ng-click="addTodo()">新增</button>
</h2>
<div class="order-todo-container grid-top col-md-12 col-md-offset-3">
    <div ng-repeat="todo in data.comments" class="grid-bottom comment-block clearfix" ng-init="todo_index = $index">
        <div class="delete-block">
            <span class="i i-close" ng-click="deleteTodo($index)"></span>
        </div>
        <div class="todo-row border clearfix">
            <div class="col-md-12">
                添加时间:
                <span ng-bind="todo.date_added"></span>
                <span ng-bind="todo.user_name" class="pad-left"></span>
            </div>
            <div class="col-md-6 text-right">
                <span class="pad-left" ng-show="local.todo_status[todo.proc_status].label == '已处理'">已处理</span>
                <span class="pad-left todo-pro-status"
                      ng-hide="local.todo_status[todo.proc_status].label == '已处理'">待处理</span>
                <button class="block-action btn btn-inverse" ng-click="changeTodoStatus($index)"
                        ng-show="todo.proc_status == 1">标记已处理
                </button>
                <button class="block-action btn btn-inverse" ng-click="changeTodoStatus($index)"
                        ng-show="todo.proc_status == 2">返回到待处理
                </button>
            </div>
        </div>
        <div class="todo-row clearfix">
            <div class="col-md-18" ng-show="todo.editable == false" ng-bind="todo.comment"></div>
            <div class="col-md-18 clearfix" ng-show="todo.editable == true">
                <input type="text" class="form-control" ng-model="todo.comment" />
            </div>
        </div>
        <div class="todo-row clearfix" ng-show="todo.editable == true">
            <div class="col-md-18 comment-type clearfix" >
                <div class="comment-bottom">
                    处理时间:
                    <quick-datepicker ng-model='todo.date_proc'
                                      disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
                </div>
                <input type="text" class="form-control comment-hour" ng-model="todo.expected_hour" ng-pattern="timeLimit"/>时
                备注类型:
                <select
                    chosen
                    style="width: 100%;"
                    ng-model="todo.type"
                    ng-options="type.value as type.label for type in local.order_comment_type"
                    data-placeholder="点击选择类型"
                    disable-search="true"
                    no-results-text="'没有找到'"
                    >
                </select>
                <button class="block-action btn btn-inverse comment-btn" ng-hide="todo.type == '7'" ng-click="updateTodo($index)">保存</button>
            </div>
        </div>
        <div class="todo-row complaint-type clearfix" ng-show="todo.editable == true && todo.type == '7'">
            <div class="col-md-18 type-bottom">
                <div class="col-md-4 type-top">
                    <input type="checkbox" ng-checked="todo.complaint[0].use" ng-click="toggleCheck($index,0)"/>客人原因
                </div>
                <div class="col-md-4">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="todo.complaint[0].detail_type"
                        ng-options="type.value as type.label for type in local.detail_type_customer"
                        data-placeholder="点击选择类型"
                        disable-search="true"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-9 type-top">
                    <input type="text" class="form-control" ng-model="todo.complaint[0].detail_md" ng-show="todo.complaint[0].detail_type == '5'" />
                </div>
            </div>
            <div class="col-md-18 type-bottom">
                <div class="col-md-4 type-top">
                    <input type="checkbox" ng-checked="todo.complaint[1].use" ng-click="toggleCheck($index,1)"/>玩途原因
                </div>
                <div class="col-md-4">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="todo.complaint[1].detail_type"
                        ng-options="type.value as type.label for type in local.detail_type_hitour"
                        data-placeholder="点击选择类型"
                        disable-search="true"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-9 type-top">
                    <input type="text" class="form-control" ng-model="todo.complain.hitour.detail_md" ng-show="todo.complaint[1].detail_type == '9'" />
                </div>
            </div>
            <div class="col-md-18 type-bottom">
                <div class="col-md-4 type-top">
                    <input type="checkbox" ng-checked="todo.complaint[2].use" ng-click="toggleCheck($index,2)"/>供应商原因
                </div>
                <div class="col-md-4">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="todo.complaint[2].detail_type"
                        ng-options="type.value as type.label for type in local.detail_type_supplier"
                        data-placeholder="点击选择类型"
                        disable-search="true"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-9 type-top">
                    <input type="text" class="form-control" ng-model="todo.complaint[2].detail_md" ng-show="todo.complaint[2].detail_type == '6'" />
                </div>
            </div>
            <div class="col-md-18 type-bottom">
                <div class="col-md-4 type-top">
                    <input type="checkbox" ng-checked="todo.complaint[3].use" ng-click="toggleCheck($index,3)"/>不可抗力
                </div>
                <div class="col-md-4">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="todo.complaint[3].detail_type"
                        ng-options="type.value as type.label for type in local.detail_type_god"
                        data-placeholder="点击选择类型"
                        disable-search="true"
                        no-results-text="'没有找到'"
                        >
                    </select>
                </div>
                <div class="col-md-9 type-top">
                    <input type="text" class="form-control" ng-model="todo.complaint[3].detail_md" ng-show="todo.complaint[3].detail_type == '3'" />
                </div>
            </div>
            <div class="col-md-18 type-bottom">
                <div class="col-md-4 type-top">
                    <input type="checkbox" ng-checked="todo.complaint[4].use" ng-click="toggleCheck($index,4)"/>其它
                </div>
                <div class="col-md-9 type-top">
                    <input type="text" class="form-control" ng-model="todo.complaint[4].complaint_md"/>
                </div>
                <button class="block-action btn btn-inverse comment-btn" ng-click="updateTodo($index)">保存</button>
            </div>
        </div>
        <div class="todo-row clearfix" ng-show="todo.editable == false">
            <button class="block-action btn btn-inverse edit-btn" ng-click="updateTodo($index)">编辑</button>
            <div class="col-md-9">处理时间:{{todo.date_proc}}</div>
            <div class="col-md-4">备注类型:{{local.order_comment_type[todo.type-1].label}}</div>
        </div>
        <div class="todo-row clearfix" ng-show="todo.editable == false && todo.type == '7' && (todo.complaint[0].use || todo.complaint[1].use || todo.complaint[2].use || todo.complaint[3].use)">
            <div class="col-md-3">投诉原因:</div>
            <div class="col-md-10">
                <div ng-show="todo.complaint[0].use">客人原因（{{local.detail_type_customer[todo.complaint[0].detail_type].label}}）</div>
                <div ng-show="todo.complaint[1].use">玩途原因（{{local.detail_type_hitour[todo.complaint[1].detail_type].label}}）</div>
                <div ng-show="todo.complaint[2].use">供应商原因（{{local.detail_type_supplier[todo.complaint[2].detail_type].label}}）</div>
                <div ng-show="todo.complaint[3].use">不可抗力（{{local.detail_type_god[todo.complaint[3].detail_type].label}}）</div>
                <div ng-show="todo.complaint[4].use">其它（{{todo.complaint[4].complaint_md}}）</div>
            </div>
        </div>
    </div>
</div>