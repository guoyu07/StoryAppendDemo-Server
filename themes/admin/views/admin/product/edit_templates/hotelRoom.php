<script type="text/ng-template" id="editHotelRoom.html">
    <div class="view-edit-section last clearfix" data-ng-controller="editHotelRoomCtrl">
        <section class="one-section-action" ng-if="local.current_edit == -1">
            <div class="rooms-container">
                <div class="room-type-container" ng-repeat="room in data.room_list">
                    <div>
                        <label class="room-title" ng-bind="room.name"></label>
                        <label class="room-subtitle">房间面积</label>
                        <label class="room-subtitle" ng-bind="room.area"></label>
                        <label class="room-subtitle">㎡</label>
                    </div>
                    <div class="clearfix">
                        <div class="service-item">
                            <label ng-bind="room.capacity"></label>
                            <label>人</label>
                        </div>
                        <div class="service-item" ng-repeat="item in room.services">
                            <label ng-bind="item.name"></label>
                        </div>
                    </div>
                    <div>
                        <label class="summary" ng-bind="room.highlight"></label>
                    </div>
                    <div>
                        <button class="btn btn-inverse edit-btn" ng-click="delRoom($index, room.room_type_id)">删除</button>
                        <button class="btn btn-inverse delete-btn" ng-click="editRoom($index)">编辑</button>
                    </div>
                </div>
            </div>
            <div>
                <button class="btn btn-inverse add-room-btn" ng-click="addRoom()">添加房型</button>
            </div>
        </section>
        <section class="one-section-action" ng-if="local.current_edit > -1">
            <form name="hotel_room_form" novalidate>
                <div class="row edit-heading">
                    <h2>基本信息</h2>
                </div>
                <div class="row edit-body grid-bottom">
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">中文名称</label>
                            <div class="col-md-8">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].name" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">英文名称</label>
                            <div class="col-md-8">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].special_info.en_name" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">供应商原始名称</label>
                            <div class="col-md-8">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].special_info.product_origin_name" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">面积</label>
                            <div class="col-md-4">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].area" required ng-pattern="onlyNumbers" />
                            </div>
                            <label class="col-md-2">㎡</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">亮点</label>
                            <div class="col-md-8">
                <textarea class="input-area editor"
                          data-ng-model="data.room_list[local.current_edit].highlight"
                          required placeholder="此客房装饰温馨，享有海滩部分景致，客房均位于较高楼层，享有花园。"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">房价包含</label>
                            <div class="col-md-8">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].price_include"
                                       placeholder="价格是按每间客房计算的，不包含10%服务费" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-3">添加图片</label>
                            <div class="room-images col-md-16">
                                <div class="image-container edit-body grid-bottom">
                                    <div style="display: inline-block;">
                                        <div class="one-image-container carousel-image grid-bottom">
                                            <span class="glyphicon glyphicon-plus"
                                                  data-ng-click="triggerUpload()"></span>
                                            <input id="img-upload" type="file" data-nv-file-select
                                                   accept="image/png, image/jpeg" />
                                        </div>
                                        <div class="one-image-container carousel-image grid-bottom"
                                             data-ng-if="local.uploader.isUploading == true">
                                            <div class="progress overlay-progress">
                                                <div class="progress-bar"
                                                     data-ng-style="{ 'width': local.uploader.progress + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="one-image-container carousel-image grid-bottom"
                                         data-ng-repeat="image in data.room_list[local.current_edit].images"
                                         data-index="{{ $index }}" dnd-sortable
                                         item="image"
                                         callback="dndCallback(info, dstIndex)"
                                         options="dndOptions">
                                        <div class="image-holder">
                                            <img data-ng-src="{{image.image_url}}?imageView/5/w/188/h/118/" />
                                            <span class="image-order" data-ng-bind="$index + 1"></span>
                                            <div class="overlay">
                                                <div class="overlay-button glyphicon glyphicon-trash"
                                                     data-ng-click="delImage( $index )"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row edit-heading">
                    <h2>客房描述</h2>
                </div>
                <div class="row edit-body grid-bottom">
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-2">客房设施</label>
                            <div class="col-md-8">
                <textarea class="input-area editor"
                          data-ng-model="data.room_list[local.current_edit].facilities"
                          required placeholder="享有风景,按次点播收费电视,电话,卫星频道,冰箱,吹风机等"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <div class="col-md-14 col-md-offset-2">
                                <div class="col-md-6" style="height: 40px" ng-repeat="service in local.service_items">
                                    <div class="row" style="line-height: 230%">
                                        <input type="checkbox"
                                               name="hotel.services[]"
                                               value="{{service.service_id}}"
                                               ng-checked="data.room_list[local.current_edit].selected_service.indexOf(service.service_id) > -1"
                                               ng-click="toggleService(service.service_id, service.name)">
                                        {{service.name}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-1 col-md-offset-2 bed-info">床型</label>
                            <div class="col-md-2">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].bed_type" required placeholder="一张大床" />
                            </div>
                            <label class="col-md-1 text-right bed-info">宽</label>
                            <div class="col-md-2">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].bed_size" required placeholder="1.5-1.8" />
                            </div>
                            <label class="col-md-1 bed-info" style="text-align: left;">m</label>
                            <label class="col-md-1" style="text-align: center;">/</label>
                            <div class="col-md-2">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].second_bed_type" placeholder="2张单床" />
                            </div>
                            <label class="col-md-1 text-right bed-info">宽</label>
                            <div class="col-md-2">
                                <input class="form-control input-sm" type="text"
                                       data-ng-model="data.room_list[local.current_edit].second_bed_size" placeholder="1.5-1.8" />
                            </div>
                            <label class="col-md-1 bed-info" style="text-align: left;">m</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <div class="col-md-4 col-md-offset-2 bed-info">
                                <label>示例:</label>
                                <label style="margin-left: 20px;">1张大床</label>
                            </div>
                            <label class="col-md-3 bed-info">宽1.8m</label>
                            <label class="col-md-1 bed-info">或</label>
                            <label class="col-md-3">2张单床</label>
                            <label class="col-md-3">宽0.9-1.3m</label>
                        </div>
                    </div>
                </div>
                <div class="row edit-heading">
                    <h2>客房限制</h2>
                </div>
                <div class="row edit-body grid-bottom">
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-2">入住人数:</label>
                            <div style="margin-top: 30px;">
                                <label class="col-md-2">入住</label>
                                <div class="col-md-2">
                                    <input class="form-control input-sm" type="text"
                                           data-ng-model="data.room_list[local.current_edit].capacity" required
                                           ng-pattern="onlyNumbers" />
                                </div>
                                <label class="col-md-1">人</label>
                                <label class="col-md-2 text-right">最多</label>
                                <div class="col-md-2">
                                    <input class="form-control input-sm" type="text"
                                           data-ng-model="data.room_list[local.current_edit].max_capacity" required
                                           ng-pattern="onlyNumbers" />
                                </div>
                                <label class="col-md-1">人</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="edit-content clearfix">
                            <label class="col-md-2">加床政策:</label>
                            <div class="col-md-14" style="margin-top: 30px;">
                                <div class="row one-policy" ng-repeat="policy in data.room_list[local.current_edit].policies"
                                     ng-if="policy.hasOwnProperty('age_1') && policy.hasOwnProperty('age_2')">
                                    <label class="col-md-3">年龄范围</label>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control input-sm" ng-model="policy.age_1"
                                               ng-pattern="onlyNumbers" />
                                    </div>
                                    <label class="col-md-1">——</label>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control input-sm col-md-2" ng-model="policy.age_2"
                                               ng-pattern="onlyNumbers" />
                                    </div>
                                    <div class="col-md-10">
                        <textarea class="editor policy-content"
                                  data-ng-model="policy.policy" placeholder="视为成人标准收费，入住必须加床"></textarea>
                                    </div>
                                    <div class="delete-btn">
                                        <span class="i i-close" ng-click="delPolicy($index)"></span>
                                    </div>
                                </div>
                                <div class="row grid-bottom">
                                    <label class="col-md-4">新增年龄范围</label>
                                    <div class="col-md-1 add-policy" ng-click="addPolicy()">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-18">
                        <textarea class="input-area editor"
                                  data-ng-model="data.room_list[local.current_edit].policy_tips"
                                  placeholder="加床/早餐/服务费"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">加床政策</label>
                                </div>
                                <div class="row">
                                    <div hi-markdown input="data.room_list[local.current_edit].bed_policy_md.md_text" output="data.room_list[local.current_edit].bed_policy_md.md_html"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">早餐费用</label>
                                </div>
                                <div class="row">
                                    <div hi-markdown input="data.room_list[local.current_edit].breakfast_md.md_text" output="data.room_list[local.current_edit].breakfast_md.md_html"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary col-xs-offset-4 col-xs-4 btn-back" data-ng-click="backToList()">
                    << 返回列表页
                </button>
                <button type="submit" class="col-xs-offset-2 col-xs-4 btn btn-hg btn-primary save-form"
                        data-ng-click="submitChanges()" data-ng-disabled="hotel_room_form.$invalid">
                    保存
                </button>
            </form>
        </section>
    </div>
</script>