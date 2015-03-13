<script type="text/ng-template" id="editProductTripPlan.html">
    <div id="product-trip-plan-container" class="view-edit-section">
        <section class="one-section-action">
            <div class="row edit-heading">
                <h2>行程总览</h2>
                <button class="btn col-md-3 btn-sharp" ng-hide="local.base_info.is_edit" ng-click="editOperation()">编辑
                </button>
                <button class="btn col-md-3 btn-sharp" ng-show="local.base_info.is_edit" ng-click="saveOperation()">保存
                </button>
            </div>
            <div class="row edit-body">
                <div ng-show="local.base_info.is_edit">
                    <div class="row grid-bottom">
                        <div class="col-md-6 title-text">行程安排是否上线</div>
                    </div>
                    <radio-switch options="local.radio_options.is_online" model="data"></radio-switch>
                </div>
                <div class="row" ng-hide="local.base_info.is_edit">
                    <div class="col-md-10 title-text">
                        行程安排
                        <span class="pad-left" ng-bind="data.is_online == '1' ? '上线': '不上线'"></span>
                    </div>
                </div>
            </div>
        </section>
        <section class="one-section-action">
            <div class="row edit-heading">
                <h2>行程编辑</h2>
            </div>
            <div class="row edit-body">
                <div class="col-md-2">
                    <div class="day-group text-center">
                        <div class="one-day"
                             ng-class="{'active' : local.current_day == $index}"
                             ng-repeat="day in data.plan_days"
                             hi-dnd
                             item="day"
                             data-index="{{ $index }}"
                             options="local.dnd_options.plan_days.options"
                             callback="local.dnd_options.plan_days.callback(info, dst_index)"
                             ng-click="switchDays($index)">D{{ $index + 1 }}
                            <div class="del-btn" ng-click="delDay($index)">X</div>
                        </div>
                        <button class="btn btn-sharp grid-top tagsinput-add" ng-click="addDay()"></button>
                    </div>
                </div>
                <div class="col-md-offset-1 col-md-15" ng-show="local.current_day > -1">
                    <div class="title-breadcrumb">
                        <div class="title"
                             ng-repeat="title in local.title_set.titles"
                             ng-bind="title.title"
                             ng-class="{ 'disabled': !title.has_content, 'active': local.current_step == $index }"
                             ng-click="local.title_set.switch_title($index)"></div>
                    </div>
                    <div class="detail-ctn">
                        <div class="one-section-detail" ng-show="local.current_step == 0">
                            <div class="trip-title">行程标题：</div>
                            <div class="">
                                <input type="text" class="form-control" required ng-maxlength="30"
                                       ng-model="data.plan_days[local.current_day].title"  />
                            </div>
                            <div class="trip-title">描述：</div>
                            <div class="">
                                <!--TODO: markdown-->
                                <textarea class="form-control" rows="5" required ng-maxlength="100"
                                          ng-model="data.plan_days[local.current_day].description">
                                          </textarea>
                            </div>
                            <div class="row">
                                <button class="col-md-offset-7 col-md-4 grid-top btn btn-inverse btn-sharp"
                                        ng-disabled="!data.plan_days[local.current_day].title || !data.plan_days[local.current_day].description" ng-click="stepToNext()">
                                    保存并下一步
                                </button>
                            </div>
                        </div>
                        <div class="one-section-detail point-section" ng-show="local.current_step == 1 || local.current_step == 2" ng-class="{'traffic': local.current_step == 2}">
                            <div class="row items-container">
                                <div class="one-item" ng-repeat="item in data.plan_days[local.current_day].points" ng-class="'type' + item.type" hi-dnd item="item" data-index="{{ $index }}" options="local.dnd_options.plan_points.options" callback="local.dnd_options.plan_points.callback(info, dst_index)">
                                    <div class="item-container">
                                        <div class="item-order">{{$index + 1}}</div>
                                        <div class="item-content" ng-if="item.type == 1 || item.type == 2" ng-bind="item.the_alias"></div>
                                        <div class="item-content" ng-if="item.type == 3">
                                            <span ng-bind-html="item.the_alias"></span>
                                        </div>
                                        <div class="item-content alias-set" ng-if="item.type == 4">
                                            <p ng-repeat="alias in item.alias_set track by $index"
                                               ng-bind="alias" class="one-alias"></p>
                                        </div>
                                        <div class="item-action">
                                            <span class="i i-edit" ng-click="editItem(item.type, $index)"></span>
                                            <span class="i i-trash" ng-click="deleteItem($index)"></span>
                                        </div>
                                        <div ng-click="editItem(5, $index)" class="item-traffic" ng-if="local.current_step == 2 && item.traffic">
                                            <span class="traffic-indicator i"
                                                  ng-class="'traffic' + item.traffic.trans_type"></span>
                                            <span class="traffic-description" ng-bind="item.traffic.description"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row add-row">
                                <span class="i i-plus-circle-bg"></span>
                                <div class="add-one-item" ng-click="editItem('1')">
                                    <i class="i i-hotel"></i>
                                    酒店
                                </div>
                                <div class="add-one-item" ng-click="editItem('4')">
                                    <i class="i i-star-filled2"></i>
                                    商品
                                </div>
                                <div class="add-one-item" ng-click="editItem('2')">
                                    <i class="i i-location"></i>
                                    景点
                                </div>
                                <div class="add-one-item" ng-click="editItem('3')">
                                    <i class="i i-text"></i>
                                    文本
                                </div>
                            </div>

                            <div class="row" ng-show="local.current_step == 1">
                                <button class="col-md-offset-5 col-md-3 grid-top btn btn-inverse btn-sharp"
                                        ng-click="savePlanPoint()">保存
                                </button>
                                <button class="col-md-offset-2 col-md-3 grid-top btn btn-inverse btn-sharp"
                                        ng-click="stepToNext()">下一步
                                </button>
                            </div>
                            <div class="row" ng-show="local.current_step == 2">
                                <button class="col-md-offset-7 col-md-4 grid-top btn btn-inverse btn-sharp"
                                        ng-click="stepToNext()">
                                    保存
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="overlays" ng-show="local.overlays.has_overlay">
            <div class="overlay-md hotel_product" ng-show="local.overlays.overlay_type == 1">
                <div class="overlay-head">酒店编辑</div>
                <div class="overlay-body">
                    <div class="hotel-product-ctn row" ng-repeat="item in data.hotel_list">
                        <div class="radio-box col-md-1">
                            <input type="radio" name="hotel" ng-value="{{item.product_id}}"
                                   ng-model="local.current_point.the_id"
                                   ng-change="local.current_point.the_alias = item.description.name" />
                        </div>
                        <div class="col-md-3" ng-bind="'ID:' + item.product_id"></div>
                        <div class="col-md-14">
                            <input type="text" class="form-control input-flat"
                                   ng-model="local.current_point.the_alias"
                                   ng-show="item.product_id == local.current_point.the_id" />
                            <div ng-bind="item.description.name"
                                 ng-hide="item.product_id == local.current_point.the_id"></div>
                        </div>
                    </div>
                </div>
                <div class="overlay-foot">
                    <div class="row">
                        <button class="col-md-offset-9 btn btn-sharp col-md-3 btn-cancel" ng-click="overlayCancel()">取消</button>
                        <button class="col-md-offset-1 btn btn-sharp col-md-3 btn-confirm" ng-click="overlayConfirm()" ng-disabled="!local.current_point.the_id">确定</button>
                    </div>
                </div>
            </div>
            <div class="overlay-md hotel_product" ng-show="local.overlays.overlay_type == 4">
                <div class="overlay-head">商品编辑</div>
                <div class="overlay-body">
                    <div class="hotel-product-ctn row" ng-repeat="item in data.product_list">
                        <div class="radio-box col-md-1">
                            <input type="checkbox" name="product" ng-value="{{item.product_id}}"
                                   ng-click="updateSet(item.product_id)"
                                   ng-checked="local.current_point.id_set.indexOf(item.product_id) > -1" />
                        </div>
                        <div class="col-md-3" ng-bind="'ID:' + item.product_id"></div>
                        <div class="col-md-14">
                            <input type="text" class="form-control input-flat"
                                   ng-model="local.current_point.alias_set[local.current_point.id_set.indexOf(item.product_id)]"
                                   ng-show="local.current_point.id_set.indexOf(item.product_id) > -1" />
                            <div ng-bind="item.description.name"
                                 ng-hide="local.current_point.id_set.indexOf(item.product_id) > -1"></div>
                        </div>
                    </div>
                </div>
                <div class="overlay-foot">
                    <div class="row">
                        <button class="col-md-offset-9 btn btn-sharp col-md-3 btn-cancel" ng-click="overlayCancel()">取消</button>
                        <button class="col-md-offset-1 btn btn-sharp col-md-3 btn-confirm" ng-click="overlayConfirm()" ng-disabled="local.current_point.id_set.length == 0">确定</button>
                    </div>
                </div>
            </div>
            <div class="overlay-md text" ng-show="local.overlays.overlay_type == 3">
                <div class="overlay-head">文本输入</div>
                <div class="overlay-body">
                    <textarea class="form-control" required
                              ng-model="local.current_point.the_alias" rows="3"></textarea>
                </div>
                <div class="overlay-foot">
                    <div class="row">
                        <button class="col-md-offset-9 btn btn-sharp col-md-3 btn-cancel" ng-click="overlayCancel()">取消</button>
                        <button class="col-md-offset-1 btn btn-sharp col-md-3 btn-confirm" ng-click="overlayConfirm()" ng-disabled="!local.current_point.the_alias">确定</button>
                    </div>
                </div>
            </div>
            <div class="overlay-md sightseeing" ng-show="local.overlays.overlay_type == 2">
                <div class="overlay-head">景点编辑</div>
                <div class="overlay-body">
                    <table class="forms-table">
                        <tbody>
                            <tr>
                                <td class="col-md-4">行程描述：</td>
                                <td class="col-md-16">
                                    <div class="table-margin">
                                        <input type="text" class="form-control input-flat" ng-model="local.current_point.the_alias"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-4">景点坐标：</td>
                                <td class="col-md-16">
                                    <div class="table-margin">
                                        <input type="text" class="form-control input-flat width-control" ng-model="local.current_point.latlng" placeholder="经度，纬度"/>
                                        <a href="" id="map-url" target="_blank" ng-click="viewMap()" class="pull-right">地图链接 ></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="scenic-ctn">
                        <input id="slide-upload" class="hidden" type="file" data-nv-file-select
                               accept="image/png, image/jpeg" uploader="uploader" />
                        <div class="one-image-block" ng-repeat="pointImage in local.current_point.point_images">
                            <div class="image-holder">
                                <img data-ng-src="{{pointImage.image_url}}?imageView/5/w/488/h/160/" />
                                <span class="image-order" ng-bind="$index + 1"></span>
                                <span class="triangle"></span>
                                <div class="edit-cover">
                                    <div class="overlay-button glyphicon glyphicon-share-alt"
                                         ng-click="changePointImage( $index )"></div>
                                </div>
                                <span class="glyphicon glyphicon-remove-sign del-icon" ng-click="delPointImage( $index )"></span>
                            </div>
                            <div class="image-desc">
                                <div class="desc-title">标题:</div>
                                <div class="desc-detail"><input class="form-control input-flat" type="text" ng-model="pointImage.title"/></div>
                                <div class="desc-title">描述:</div>
                                <div class="desc-detail"><textarea class="form-control input-flat" rows="3" ng-model="pointImage.description"></textarea></div>
                            </div>
                        </div>
                    </div>
                    <div class="row grid-top">
                        <p class="text-center" ng-show="local.new_scenic">添加图文前需保存当前行程<br />请编辑行程描述后点击确定并保存当前行程后重新编辑</p>
                        <button class="col-md-offset-6 col-md-6 btn btn-sharp" ng-click="addPointImage()" ng-hide="local.new_scenic">新建一个图文</button>
                    </div>
                </div>
                <div class="overlay-foot">
                    <div class="row">
                        <button class="col-md-offset-9 btn btn-sharp col-md-3 btn-cancel" ng-click="overlayCancel()">取消</button>
                        <button class="col-md-offset-1 btn btn-sharp col-md-3 btn-confirm" ng-click="overlayConfirm()">确定</button>
                    </div>
                </div>
            </div>
            <div class="overlay-md traffic" ng-show="local.overlays.overlay_type == 5">
                <div class="overlay-head">交通工具</div>
                <div class="overlay-body">
                    <div class="row">
                        <h5>是否添加交通工具？</h5>
                        <radio-switch
                            model="local.current_point"
                            options="local.radio_options.has_traffic"></radio-switch>
                    </div>
                    <div ng-show="local.current_point.has_traffic == 1">
                        <h5>选择交通方式</h5>
                        <div class="row">
                            <div class="col-md-4" ng-repeat="(key, label) in local.traffic_type">
                                <input type="radio" name="traffic" ng-value="{{key}}" ng-model="local.current_point.trans_type" />{{label}}
                            </div>
                            <textarea class="form-control col-md-18 grid-top" ng-model="local.current_point.description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="overlay-foot">
                    <div class="row">
                        <button class="col-md-offset-9 btn btn-sharp col-md-3 btn-cancel" ng-click="overlayCancel()">取消</button>
                        <button class="col-md-offset-1 btn btn-sharp col-md-3 btn-confirm" ng-click="overlayConfirm()">确定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>