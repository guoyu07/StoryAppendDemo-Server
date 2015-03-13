<section class="grid-top" ng-if="local.tab_options.current_tab.path == '_tourplan'">
    <div ng-controller="ServiceTourplanCtrl">
        <div class="states-section">
            <hi-section-head options="local.section_head.tour"></hi-section-head>
            <div class="grid-top" ng-hide="local.section_head.tour.is_edit">
                <div class="row">
                    <div class="col-md-4">商品图文</div>
                    <div class="col-md-14" ng-bind="local.radio_options.is_online.items[data.is_online]"></div>
                </div>
                <div class="row">
                    <div class="col-md-4">商品标题</div>
                    <div class="col-md-14" ng-bind="data.cn_schedule"></div>
                </div>
                <div class="row">
                    <div class="col-md-4">详情页展示方式</div>
                    <div class="col-md-14" ng-bind="local.radio_options.display_type.items[data.display_type]"></div>
                </div>
            </div>
            <div class="grid-top" ng-show="local.section_head.tour.is_edit">
                <form name="service_tour_form" hi-watch-dirty="local.path_name">
                    <div class="row">
                        <div class="col-md-4">是否上线</div>
                        <div class="col-md-14">
                            <hi-radio-switch options="local.radio_options.is_online" model="data"></hi-radio-switch>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">详情页展示方式</div>
                        <div class="col-md-14">
                            <hi-radio-switch options="local.radio_options.display_type" model="data"></hi-radio-switch>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">行程标题</div>
                        <div class="col-md-14">
                            <input class="form-control" type="text" ng-model="data.cn_schedule" placeholder="行程安排" required="required">
                        </div>
                    </div>
                    <div class="grid-top" ng-hide="data.display_type == 0">
                        <div class="row">
                            <label class="col-md-4">游玩天数：</label>
                            <div class="col-md-3">
                                <select class="form-control" ng-model="data.total_days" ng-change="changeTotalDays(this.value)" ng-options="day for day in local.days_list">
                                </select>
                            </div>
                        </div>
                        <div class="row" ng-repeat="plan in data.plans">
                            <label class="col-md-4">第{{$index + 1}}天行程标题：</label>
                            <div class="col-md-10">
                                <input class="form-control" ng-model="plan.title" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <hi-uploader options="local.uploader_options"></hi-uploader>
        <div class="overlay progress-screen" ng-show="local.uploader_options.uploader.isUploading">
            <div class="progress">
                <div class="progress-bar" data-ng-style="{ 'width': local.uploader_options.uploader.progress + '%' }">
                </div>
            </div>
        </div>

        <section class="states-section">
            <div class="section-head grid-bottom">
                <h2 class="section-title">录入内容</h2>
            </div>
            <!--时间轴图文-->
            <div ng-show="data.display_type == 1">
                <ul class="nav nav-tabs">
                    <li ng-class="{ active: $index == local.current_plan_index }" ng-repeat="plan in data.plans" ng-click="switchDayPlan($index)">
                        <a href="javascript:void(0);" ng-bind="'D'+ ($index + 1)"></a>
                    </li>
                </ul>
                <div class="grid-top">
                    <div class="one-plan-ctn" ng-show="$index == local.current_plan_index" ng-repeat="plan in data.plans">
                        <div class="one-plan-group" ng-repeat="group in plan.groups" ng-init="group_index = $index">
                            <div class="group-del-btn text-center" ng-click="deleteGroup($index)">x</div>
                            <!--group header-->
                            <div class="group-head">
                                <div class="row" ng-show="group.editing == 1">
                                    <div class="col-md-2 col-md-offset-1 text-right">时间</div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" ng-model="group.time" />
                                    </div>
                                    <div class="col-md-2 text-right">标题</div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" ng-model="group.title" />
                                    </div>
                                    <div class="i toggle-edit i-save col-md-1" ng-click="toggleGroup($index)"></div>
                                </div>
                                <div class="row" ng-show="group.editing == 0">
                                    <div class="col-md-3 col-md-offset-2 small" ng-bind="group.time"></div>
                                    <div class="col-md-11" ng-bind="group.title"></div>
                                    <div class="i toggle-edit i-edit col-md-1" ng-click="toggleGroup($index)"></div>
                                </div>
                            </div>
                            <!--group body-->
                            <div class="group-body">
                                <div class="group-content" ng-repeat="item in group.items" data-index="{{ group_index + '-' + $index }}" hi-dnd item="item" callback="local.dnd.callback(info, dst_index)" options="local.dnd.options">
                                    <div class="item-img-ctn">
                                        <img class="img-cover" ng-src="{{ item.image_url }}?imageView/5/w/822/h/145" />
                                        <div class="img-order" ng-bind="$index + 1"></div>
                                        <div class="triangle"></div>
                                        <div class="img-option-ctn" ng-show="item.editing">
                                            <div class="overlay-btn i-share" ng-click="changeImage(group_index, $index )"></div>
                                            <div class="overlay-btn i-trash" ng-click="deleteImage(group_index, $index )"></div>
                                        </div>
                                    </div>
                                    <div class="item-info-ctn" ng-hide="item.editing">
                                        <h4 class="item-title" ng-bind="item.title"></h4>
                                        <p class="item-desc" ng-bind="item.description"></p>
                                    </div>
                                    <div class="item-info-ctn" ng-show="item.editing">
                                        <div class="row padding-fix">
                                            <div class="col-md-2">标题：</div>
                                            <div class="col-md-6">
                                                <input class="form-control" ng-model="item.title" />
                                            </div>
                                        </div>
                                        <div class="row padding-fix">
                                            <div class="col-md-2">描述：</div>
                                            <div class="col-md-16">
                                                <textarea class="image-desc form-control" ng-model="item.description" style="height: 100px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-toggle-edit" ng-click="toggleItem( group_index,$index )" ng-class="{ 'i-edit' : item.editing == false, 'i-save' : item.editing == true }"></div>
                                    <div class="item-del-btn i-close" ng-click="deleteItem(group_index, $index )"></div>
                                </div>
                                <div class="insert-item text-center">
                                    添加一个新图文
                                    <span class="plus-inverse i-plus" ng-click="addItem($index)"></span>
                                </div>
                            </div>
                            <div class="grid-top grid-bottom insert-group text-center">
                                插入一个新分组
                                <span class="plus-inverse i-plus" ng-click="insertGroup(plan, group)"></span>
                            </div>
                        </div>
                        <div class="grid-top col-md-18 text-center row" ng-show="plan.groups.length == 0">
                            添加一个分组
                            <span class="plus-inverse i-plus" data-ng-click="addGroup($index)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-show="data.display_type == 0">
                <div class="grid-top group-body">
                    <div class="group-content" ng-repeat="item in items" data-index="{{ $index }}" hi-dnd item="item" callback="local.dnd.callback(info, dst_index)" options="local.dnd.options">
                        <div class="item-img-ctn">
                            <img class="img-cover" ng-src="{{ item.image_url }}?imageView/5/w/822/h/145" />
                            <div class="img-order" ng-bind="$index + 1"></div>
                            <div class="triangle"></div>
                            <div class="img-option-ctn" ng-show="item.editing">
                                <div class="overlay-btn i-share" ng-click="changeImage(0, $index)"></div>
                                <div class="overlay-btn i-trash" ng-click="deleteImage(0, $index)"></div>
                            </div>
                        </div>
                        <div class="item-info-ctn" ng-hide="item.editing">
                            <h4 class="item-title" ng-bind="item.title"></h4>
                            <p class="item-desc" ng-bind="item.description"></p>
                        </div>
                        <div class="item-info-ctn" ng-show="item.editing">
                            <div class="row padding-fix">
                                <div class="col-md-2">标题：</div>
                                <div class="col-md-6">
                                    <input class="form-control" ng-model="item.title" />
                                </div>
                            </div>
                            <div class="row padding-fix">
                                <div class="col-md-2">描述：</div>
                                <div class="col-md-16">
                                    <textarea class="image-desc form-control" ng-model="item.description" style="height: 100px;"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="item-toggle-edit" ng-click="toggleItem(0, $index)" ng-class="{ 'i-edit' : item.editing == false, 'i-save' : item.editing == true }"></div>
                        <div class="item-del-btn i-close" ng-click="deleteItem(0, $index)"></div>
                    </div>
                </div>
                <div class="insert-item text-center">
                    添加一个新图文<span class="plus-inverse i-plus" ng-click="addItem(0)"></span>
                </div>
            </div>
        </section>
    </div>
</section>