<script type="text/ng-template" id="productTourPlan.html">
<div class="view-edit-section last clearfix" data-ng-controller="productTourPlanCtrl">
<section class="one-section-action tour-plan-item">
<form name="image_desc_form" novalidate>
    <div class="row edit-heading">
        <h2>基本信息</h2>
        <button class="col-md-3 col-md-offset-8 btn btn-sharp"
                data-ng-hide="tourPlanEditing == true"
                data-ng-click="tourPlanEditClick()">
            编辑
        </button>
        <button type="submit" class="col-md-3 col-md-offset-8 btn btn-sharp"
                data-ng-click="submitTourPlanChanges()"
                data-ng-hide="tourPlanEditing == false">
            保存
        </button>
    </div>
    <div class="row edit-body" data-ng-hide="tourPlanEditing == true">
        <div class="row">
            <h4>该商品图文<span class="text-emphasis">&nbsp;{{radio_options.is_online.items[info.is_online]}}&nbsp;</span>
            </h4>
            <label><h4>标题<span class="text-emphasis">&nbsp;{{cn_schedule}}&nbsp;</span></label>
            </h4>
            <label><h4>详情页按照<span class="text-emphasis">&nbsp;{{radio_options.display_type.items[info.display_type]}}&nbsp;</span>显示
            </h4></label>
        </div>
    </div>
    <div class="row edit-body" data-ng-hide="tourPlanEditing == false">
        <div class="row">
            <div class="col-md-6 title-text">是否上线</div>
        </div>
        <div class="row">
            <div class="col-md-12 edit-content last-content row">
                <radio-switch options="radio_options.is_online"
                              model="info"></radio-switch>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 title-text">详情页需要按照哪种样式显示？</div>
        </div>
        <div class="row">
            <div class="col-md-12 edit-content last-content row">
                <radio-switch options="radio_options.display_type"
                              model="info"></radio-switch>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 title-text">标题</div>
        </div>
        <div class="row">
            <div class="col-md-12 edit-content last-content row">
                <input class="form-control" type="text" data-ng-model="cn_schedule" placeholder="行程安排"
                       required="required">
            </div>
        </div>


        <!--<div data-ng-hide="info.display_type == 0">
            <div class="row">
                <div class="col-md-6 title-text">请填写游玩天数</div>
            </div>
            <div class="row">
                <div class="edit-content textStyle">
                    <label class="col-md-2">天数</label>
                    <div class="col-md-2 input_margin">
                        <input type="text" class="form-control input-sm"
                               data-ng-model="info.total_days" />
                    </div>
                </div>
            </div>
        </div>-->

        <div data-ng-hide="info.display_type == 0">
            <div class="row">
                <div class="col-md-6 title-text">请选择游玩天数并填写行程标题</div>
            </div>
            <div class="row">
                <div class="edit-content textStyle">
                    <div class="row">
                        <label class="col-md-4">游玩天数：</label>
                        <div class="col-md-3 input_margin">
                            <select class="form-control" data-ng-model="info.total_days"
                                    data-ng-change="changeTotalDays(this.value)">
                                <option value="1" selected="selected">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" data-ng-repeat="plan in plans">
                <div class="edit-content textStyle">
                    <div class="row">
                        <label class="col-md-4">第{{$index+1}}天行程标题：</label>
                        <div class="col-md-10 input_margin">
                            <input class="image-title form-control" data-ng-model="plan.title" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
<div class="row edit-heading">
    <h2>录入内容</h2>
</div>
<div data-ng-show="info.display_type == 1">
    <ul id="mytab" class="nav nav-tabs">
        <li ng-class="{active: $index == 0}" data-ng-repeat="plan in plans" data-ng-click="switchDayPlan($index)"><a
            show-tab href="#D{{$index+1}}" data-toggle="tab">D{{$index+1}}</a></li>
    </ul>
    {{plan_editing_counts}}
    <div class="tab-content one-section-action">
        <div class="tab-pane edit-body" ng-class="{active: $index == 0}" data-ng-repeat="plan in plans"
             id="D{{$index+1}}">
            <br>
            <div data-ng-repeat="group in plan.groups">
                <div class="square-input-section" style="float: left; width: 100%">
                    <a class="del-btn text-center"
                       data-ng-click="deleteGroup($index)">x</a>
                    <div class="section-head" style="margin-bottom: 0px;">
                        <div class="row" data-ng-show="group.editing == 1">
                            <label class="col-md-2">时间</label>
                            <div class="col-md-3 input_margin">
                                <input type="text" cjass="form-control"
                                       data-ng-model="group.time" />
                            </div>
                            <label class="col-md-2">标题</label>
                            <div class="col-md-8 input_margin">
                                <input type="text" class="form-control"
                                       data-ng-model="group.title" />
                            </div>
                            <label><span class="glyphicon toggle-edit glyphicon-ok"
                                         data-ng-click="toggleGroup( $index )"></span></label>
                        </div>
                        <div class="row" data-ng-show="group.editing == 0">
                            <label><h4>时间：<span class="text-emphasis">&nbsp;{{group.time}}&nbsp;</span></h4></label>
                            <label><h4>标题：<span class="text-emphasis">&nbsp;{{group.title}}&nbsp;</span></h4></label>
                            <label for=""><span class="glyphicon toggle-edit glyphicon-edit"
                                                data-ng-click="toggleGroup( $index )"></span></label>

                        </div>

                    </div>
                    <div class="section-body row" style="overflow: visible;">
                        <div class="row product-image" id="scrolltop-placeholder">
                            <input id="home-slide-upload" class="hidden" type="file" data-ng-file-select
                                   accept="image/png, image/jpeg" />
                            <div class="image-container edit-body grid-bottom">
                                <div class="one-image-container carousel-image col-md-9 grid-bottom"
                                     data-ng-repeat="item in group.items"
                                     data-index="{{ $parent.$index + '-' + $index }}" dnd-sortable item="item"
                                     callback="slideDndCallback(info, dstIndex)"
                                     options="slideDndOptions">
                                    <div class="image-holder">
                                        <img data-ng-src="{{item.image_url}}?imageView/5/w/836/h/160/" />
                                        <span class="image-order" data-ng-bind="$index + 1"></span>
                                        <span class="triangle"></span>
                                        <div class="overlay" data-ng-show="item.editing == true">
                                            <div class="overlay-button glyphicon glyphicon-share-alt"
                                                 data-ng-click="changeImage($parent.$index, $index )"></div>
                                            <div class="overlay-button glyphicon glyphicon-trash"
                                                 data-ng-click="deleteImage($parent.$index, $index )"></div>
                                        </div>
                                    </div>
                                    <div class="image-info" data-ng-show="item.editing == false">
                                        <h3 data-ng-bind="item.title"></h3>
                                        <p data-ng-bind="item.description"></p>
                                    </div>
                                    <div class="image-info" data-ng-show="item.editing == true">
                                        <div class="row">
                                            <span class="col-md-2">标题：</span><input class="image-title form-control"
                                                                                    data-ng-model="item.title" />
                                        </div>
                                        <div class="row">
                                            <span class="col-md-2">描述：</span>
                                            <textarea class="image-desc form-control"
                                                      data-ng-model="item.description" style="height: 100px;"></textarea>
                                        </div>
                                    </div>
                                      <span class="glyphicon toggle-edit"
                                            data-ng-click="toggleItem( $parent.$index,$index )"
                                            data-ng-class="{ 'glyphicon-edit' : item.editing == false, 'glyphicon-ok' : item.editing == true }"
                                          ></span>
                                <span class="glyphicon glyphicon-remove-sign del-icon"
                                      data-ng-click="deleteItem($parent.$index, $index )"></span>
                                </div>
                                <div>
                                    <br>
                                    新增一个图文项
                                    <button class="btn btn-sharp tagsinput-add "
                                            data-ng-click="addTourPlanItem($index)"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="insert-group">
                        <span class="col-md-3 col-md-offset-7">插入一个新分组</span>
                        <button class="btn btn-sharp tagsinput-add" data-ng-click="insertGroup(plan, group)"></button>
                    </div>
                </div>
            </div>
            <div class="col-md-18 title-text last-content row" data-ng-show="plan.groups.length == 0">
                添加一个分组
                <button class="btn btn-sharp tagsinput-add" data-ng-click="addGroup($index)"></button>
            </div>
        </div>
    </div>
</div>

<div data-ng-show="info.display_type == 0">
    <div class="row product-image" id="scrolltop-placeholder">

        <input id="home-slide-upload" class="hidden" type="file" data-ng-file-select />

        <div class="image-container edit-body grid-bottom">
            <div class="one-image-container carousel-image col-md-9 grid-bottom"
                 data-ng-repeat="item in items"
                 data-index="{{ $index }}" dnd-sortable item="item"
                 callback="slideDndCallback(info, dstIndex)"
                 options="slideDndOptions">
                <div class="image-holder">
                    <img data-ng-src="{{item.image_url}}?imageView/5/w/836/h/160/" />
                    <span class="image-order" data-ng-bind="$index + 1"></span>
                    <span class="triangle"></span>
                    <div class="overlay" data-ng-show="item.editing == true">
                        <div class="overlay-button glyphicon glyphicon-share-alt"
                             data-ng-click="changeImage(0, $index )"></div>
                        <div class="overlay-button glyphicon glyphicon-trash"
                             data-ng-click="deleteImage(0, $index )"></div>
                    </div>
                </div>
                <div class="image-info" data-ng-show="item.editing == false">
                    <h3 data-ng-bind="item.title"></h3>
                    <p data-ng-bind="item.description"></p>
                </div>
                <div class="image-info" data-ng-show="item.editing == true">
                    <div class="row">
                        <span class="col-md-2">标题：</span><input class="image-title form-control"
                                                                data-ng-model="item.title" />
                    </div>
                    <div class="row">
                        <span class="col-md-2">描述：</span>
                        <textarea class="image-desc form-control"
                                  data-ng-model="item.description" style="height: 100px;"></textarea>
                    </div>

                </div>
                                <span class="glyphicon toggle-edit" data-ng-click="toggleItem(0, $index )"
                                      data-ng-class="{ 'glyphicon-edit' : item.editing == false, 'glyphicon-ok' : item.editing == true }"
                                    ></span>
                <span class="glyphicon glyphicon-remove-sign del-icon" data-ng-click="deleteItem(0, $index )"></span>
            </div>
            <div>
                新增一个图文项
                <button class="btn btn-sharp tagsinput-add "
                        data-ng-click="addTourPlanItem(0)"></button>
            </div>
        </div>
    </div>
</div>
</div>

</section>
</div>
</script>