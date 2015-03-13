<div ng-if="local.tab_options.current_tab.path == 'place'">
    <div ng-controller="RedeemPlaceCtrl">
        <hi-section-head model="local.section_head.place" options="local.section_head.place"></hi-section-head>
        <div class="redeem-section" ng-show="local.section_head.place.is_edit">
            <form name="redeem_place_form" hi-watch-dirty="local.path_name">
                <div class="row clearfix">
                    <h3 class="col-md-9 place-text">是否需要为商品关联接送点专辑？</h3>
                    <div class="col-md-7 place-radio">
                        <hi-radio-switch options="local.radio_options.need_special"
                                         model="local_model"></hi-radio-switch>
                    </div>
                </div>
                <div class="row">
                    <div class="album-text" data-ng-show="local_model.need_special == 1">输入景点专辑ID，将专辑内的景点与商品进行关联。</div>
                    <div class="link-album clearfix" data-ng-show="local_model.need_special == 1">
                        <div class="form-group col-xs-4">
                            <input type="text" data-ng-model="albums.special.album_id"
                                   data-ng-change="local_model.valid_special = false" min="1" placeholder="专辑ID"
                                   class="form-control">
                            <span class="input-check-btn i-check" data-ng-show="local_model.valid_special"></span>
                        </div>
                        <button class="btn btn-inverse btn-sharp" data-ng-show="!local_model.valid_special"
                                data-ng-click="updateSpecialAlbum()">关联专辑
                                <span class="i i-refresh refresh-animate"
                                      data-ng-show="local_model.link_progress_special == true"></span>
                        </button>
					    <span class="album-text" data-ng-show="local_model.valid_special">查看专辑：
                            <a data-ng-href="{{albums.special.album_info.link}}" target="_blank">{{albums.special.album_info.title}}</a></span><br />
                    </div>
                    <div data-ng-show="local_model.valid_special && local_model.need_special == 1">
                        <p class="album-text">
                            此专辑内包含{{albums.special.landinfos.length}}个地点信息, 点击右侧按钮添加新的分组
                            <button class="btn toggle-btn tags-input-add" data-ng-click="addGroup()"></button>
                        </p>
                        <div class="one-location-group" data-ng-repeat="current_group in groups">
                            <div class="clearfix">
                                <span class="album-text group-title">分组名称</span>
                                <input type="text" class="group-name form-control input"
                                       data-ng-model="current_group.title" />
                                <button class="btn btn-sharp col-xs-3 col-xs-offset-1" data-ng-show="$index != 0"
                                        data-ng-click="delGroup($index)">删除分组
                                </button>
                            </div>
                            <div class="one-location-group-selection">
                                <button class="btn one-criteria criteria-with-x"
                                        data-ng-repeat="landinfo in albums.special.landinfos"
                                        data-ng-click="toggleItem(landinfo.landinfo_id, current_group.items)"
                                        data-ng-class="{ checked: current_group.items.indexOf(landinfo.landinfo_id.toString()) == -1 }">
                                    {{landinfo.name}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="album-text" data-ng-show="local_model.need_special == 1">
                        <span style="float: left;">接送地点地图</span>
                        <button class="btn btn-sharp col-xs-3 col-xs-offset-1"
                                data-ng-show="local_model.picket_ticket_map == ''" data-ng-click="addPickTicketMap()">添加
                        </button>
                        <button class="btn btn-sharp col-xs-3 col-xs-offset-1"
                                data-ng-show="local_model.picket_ticket_map != ''" data-ng-click="editPickTicketMap()">
                            编辑
                        </button>
                        <a data-ng-href="{{ local_model.pick_ticket_map }}" target="_blank">
                            <img
                                data-ng-show="local_model.edit_pick_ticket_map == 0 && local_model.pick_ticket_map != ''"
                                style="float: left; margin-top: 12px; width: 813px; height: 197px; z-index: 100;"
                                data-ng-src="{{ local_model.pick_ticket_map }}"></a>
                        <hi-pick-ticket-map data-ng-show="local_model.edit_pick_ticket_map == 1"
                                            mapid="local_model.pick_ticket_map_id" actions="local_model.actions"
                                            editing="local_model.edit_pick_ticket_map" points="local_model.points"
                                            mapinfo="local_model.mapinfo">

                    </div>
                </div>
            </form>
        </div>
        <div class="section-body clearfix" ng-show="!local.section_head.place.is_edit">
            <div ng-if="local_model.need_special == 0">
                <span>不需要关联接送地点专辑</span>
            </div>
            <div ng-if="local_model.need_special == 1">
                <div class="section-subtitle">专辑名称</div>
                <div class="section-subbody">
                    {{albums.special.album_info.title}}
                </div>
                <div class="section-subtitle">专辑分组</div>
                <div class="section-subbody">
                    <table class="redeem-place-table">
                        <tr class="header">
                            <th>
                                分组名称
                            </th>
                            <th>
                                包含地点
                            </th>
                        </tr>
                        <tr class="body" data-ng-repeat="current_group in groups">
                            <td>
                                {{current_group.title}}
                            </td>
                            <td>
                                <p data-ng-repeat="landinfo in albums.special.landinfos" ng-if="current_group.items.indexOf(landinfo.landinfo_id.toString()) > -1">{{landinfo.name}}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>