<section class="grid-top states-section" ng-if="local.tab_options.current_tab.path == 'pass_classic'">
    <div ng-controller="ServicePassClassicCtrl">
        <hi-section-head options="local.section_head.album"></hi-section-head>
        <div class="section-body clearfix" ng-show="local.section_head.album.is_edit">
            <form name="service_pass_classic_form" hi-watch-dirty="local.path_name">
                <table class="forms-table pass-classic-table">
                    <tr>
                        <td>
                            <label>需要关联景点专辑</label>
                        </td>
                        <td>
                            <div class="col-md-5">
                                <hi-radio-switch options="local.radio_options.need_album" model="data.album"></hi-radio-switch>
                            </div>
                        </td>
                    </tr>
                    <tr ng-if="data.album.need_album == '1'">
                        <td>
                            <label>输入景点专辑ID</label>
                        </td>
                        <td class="link-album">
                            <div class="col-md-4">
                                <input type="number" min="1" placeholder="专辑ID" class="form-control" ng-change="local.valid_land = false" ng-model="data.album.album_id" required />
                                <span class="input-check-btn i i-check" ng-show="local.valid_land"></span>
                            </div>
                            <div class="col-md-14">
                                <button class="btn btn-inverse" ng-show="!local.valid_land" ng-click="updateLandAlbum()">
                                    关联专辑
                                    <span class="i i-refresh refresh-animate" ng-show="local.linking_album == true"></span>
                                </button>
                            <span class="album-text" ng-show="local.valid_land">
                                查看专辑：<a target="_blank" ng-href="{{ data.album.album_info.link }}" ng-bind="data.album.album_info.title"></a>
                            </span>
                            </div>
                        </td>
                    </tr>
                    <tr ng-show="data.album.need_album == '1'">
                        <td>
                            <label>专辑显示名称</label>
                        </td>
                        <td>
                            <div class="col-md-4">
                                <input type="text" class="form-control" ng-model="data.album.album_name" />
                            </div>
                        </td>
                    </tr>
                    <tr ng-show="data.album.need_album == '1'">
                        <td>
                            <label>编辑地图</label>
                            <br />
                            <button class="block-action add btn btn-inverse" ng-click="editAlbumMap()">
                                <span ng-show="data.album.album_map == ''">添加</span>
                                <span ng-hide="data.album.album_map == ''">编辑</span>
                            </button>
                        </td>
                        <td>
                            <a ng-href="{{ data.album.album_map }}" target="_blank">
                                <img class="album-map" ng-show="!local.edit_album_map && data.album.album_map != ''" ng-src="{{ data.album.album_map }}" />
                            </a>
                            <hi-pick-ticket-map ng-show="local.edit_album_map" actions="local.album_actions" mapid="local.album_map_id" editing="local.edit_album_map" points="local.album_points" mapinfo="local.album_mapinfo"></hi-pick-ticket-map>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="section-body clearfix" ng-hide="local.section_head.album.is_edit">
            <p class="small-desc" ng-hide="data.album.need_album == '1'">此商品没有挂接景点</p>
            <p class="small-desc" ng-show="data.album.need_album == '1'">
                此商品挂接了“{{data.album.album_name}}”的景点专辑
            </p>
            <img class="album-map" ng-show="data.album.need_album == '1' && data.album.album_map != ''" ng-src="{{ data.album.album_map }}" />
        </div>
    </div>
</section>