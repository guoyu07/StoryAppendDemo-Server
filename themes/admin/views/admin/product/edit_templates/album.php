<script type="text/ng-template" id="editProductAlbum.html">
  <form name="product_album_form" novalidate>
    <div data-ng-include="'editProductLandsAlbum.html'"></div>
    <div data-ng-include="'editProductLandsList.html'"></div>
  </form>
  <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form" data-ng-click="submitChanges()">
    保存
  </button>
</script>


<script type="text/ng-template" id="editProductLandsAlbum.html">
  <div class="edit-section clearfix">
    <sidebar name='editProductLandsAlbum'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action">
      <div class="row edit-heading with-dot">
        <h2>是否需要为商品关联景点专辑？</h2>
      </div>
      <div class="row edit-body">
        <radio-switch options="radio_options.need_land" model="local_model"></radio-switch>
        <div class="desc" data-ng-show="local_model.need_land == 1">输入景点专辑ID，将专辑内的景点与商品进行关联。</div>
        <div class="link-album" data-ng-show="local_model.need_land == 1">
          <div class="form-group col-xs-4">
            <input type="number" data-ng-model="albums.land.album_id" data-ng-change="local_model.valid_land = false"
                   min="1" placeholder="专辑ID" class="form-control">
            <span class="input-icon fui-check" data-ng-show="local_model.valid_land"></span>
          </div>
          <button class="btn btn-inverse" data-ng-show="!local_model.valid_land" data-ng-click="updateLandAlbum()">关联专辑
						<span class="glyphicon glyphicon-refresh refresh-animate"
                  data-ng-show="local_model.link_progress_land == true"></span></button>
					<span class="desc" data-ng-show="local_model.valid_land">查看专辑：<a
              data-ng-href="{{albums.land.album_info.link}}" target="_blank">{{albums.land.album_info.title}}</a></span>

            <br/>
            <span class="desc" style="float: left; clear: left;font-size: 16px;">专辑显示名称：</span>
            <input type="text" data-ng-model="albums.land.album_name" class="form-control" style="float: left; width: 320px;">

            <div class="desc" style="clear:both; margin-top: 12px;" data-ng-show="local_model.need_land == 1">
                <span style="float: left;">景点地图</span>
                <button class="btn btn-sharp col-xs-3 col-xs-offset-1"
                        data-ng-show="local_model.album_map == ''" data-ng-click="addAlbumMap()">添加
                </button>
                <button class="btn btn-sharp col-xs-3 col-xs-offset-1"
                        data-ng-show="local_model.album_map != ''" data-ng-click="editAlbumMap()">编辑
                </button>
                <a data-ng-href="{{ local_model.album_map }}" target="_blank">
                    <img data-ng-show="local_model.edit_album_map == 0 && local_model.album_map != ''"
                         style="float: left; margin-top: 12px; width: 640px; height: 155px; z-index: 100;"
                         data-ng-src="{{ local_model.album_map }}"></a>
                <pick-ticket-map data-ng-show="local_model.edit_album_map == 1" actions="local_model.album_actions"
                                 mapid="local_model.album_map_id"
                                 editing="local_model.edit_album_map" points="local_model.album_points"
                                 mapinfo="local_model.album_mapinfo">

            </div>
        </div>
      </div>
    </section>
  </div>
</script>

<script type="text/ng-template" id="editProductLandsList.html">
  <div class="edit-section last clearfix">
    <sidebar name='editProductLandsList'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action">
      <div class="row edit-heading align-middle">
        <h2>添加文字景点列表
          <button class="btn tagsinput-add" data-ng-click="addList()"></button>
        </h2>
      </div>
        <div class="row edit-body one-location-group" data-ng-show="lands_list.length > 0" style="padding-bottom: 20px;">
            <span style="float: left;">名称：</span><input type="text" class="form-control input" data-ng-model="local_model.landinfo_md_title">
        </div>
      <div class="row edit-body location-groups">
        <div class="one-location-group grid-bottom" data-ng-repeat="current_list in lands_list">
          <div class="clearfix grid-bottom">
            <input type="text" class="form-control input" data-ng-model="current_list.title" />
            <button class="btn btn-sharp col-xs-3 col-xs-offset-1" data-ng-click="delList($index)">删除分组</button>
          </div>
          <markdown input="current_list.list.md_text" output="current_list.list.md_html"></markdown>
        </div>
      </div>
    </section>
  </div>
</script>