<div id="home-group-container" class="container page-container" ng-controller="HomeEditGroupCtrl">
    <div class="states-section row home-group-info">
        <hi-section-head model="local.section_head" options="local.section_head"></hi-section-head>
        <div class="section-body" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
            <form name="home_group_info_form">
                <div class="col-md-4">
                    <label>封面图（大小为640x610）：</label>
                    <div hi-uploader options="local.uploader.cover"></div>
                </div>
                <table class="forms-table col-md-10 col-md-offset-2 table-text">
                    <tr>
                        <th><label>专题名称</label></th>
                        <td><input type="text" class="form-control" ng-model="data.home_group.name" /></td>
                    </tr>
                    <tr>
                        <th><label>包含内容类型</label></th>
                        <td>
                            <div hi-radio-switch options="local.radio_switch.type" model="data.home_group"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>专题大标题</label></th>
                        <td><input type="text" class="form-control" ng-model="data.home_group.title" /></td>
                    </tr>
                    <tr>
                        <th><label>专题描述</label></th>
                        <td><textarea class="form-control" rows="4" ng-model="data.home_group.brief"></textarea></td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="section-body" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
            <div class="col-md-4">
                <img class="cover-image" ng-src="{{ data.home_group.cover_url }}">
            </div>
            <div class="col-md-14 col-md-offset-2 table-text">
                <div class="row">
                    <div class="view-title col-md-4">专题名称:</div>
                    <div class="view-body-container">
                        <div class="view-body" ng-bind="data.home_group.name"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="view-title col-md-4">包含内容类型:</div>
                    <div class="view-body-container">
                        <div class="view-body" ng-bind="local.radio_switch.type.items[data.home_group.type]"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="view-title col-md-4">专题大标题:</div>
                    <div class="view-body-container">
                        <div class="view-body" ng-bind="data.home_group.title"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="view-title col-md-4">专题描述:</div>
                    <div class="view-body-container">
                        <div class="view-body" ng-bind="data.home_group.brief"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="states-section row">
        <div class="section-head">
            <h2 class="section-title">分组内的点</h2>
        </div>
        <div class="section-body">
            <div class="section-subtitle grid-top" ng-show="data.home_group.type == '1' || data.home_group.type == '3'">
                添加商品
            </div>
            <div class="section-subtitle grid-top" ng-show="data.home_group.type == '2'">添加城市</div>
            <p class="small-desc">分组图片的尺寸为230x288（桌面站）, 570x415（手机站）</p>
            <div class="section-subbody image-group-container"
                 ng-class="{ 'product-image' : data.home_group.type == '1' || data.home_group.type == '3', 'city-image' : data.home_group.type == '2' }">
                <div class="row">
                    <div class="one-image-container carousel-image grid-bottom add-image">
                        <div class="row">
                            <div class="col-md-16 col-md-offset-1"
                                 ng-show="data.home_group.type == '1' || data.home_group.type == '3'">
                                <input type="text" class="form-control" ng-model="local.search_pid"
                                       placeholder="输入商品ID" />
                            </div>
                            <div class="col-md-16 col-md-offset-1" ng-show="data.home_group.type == '2'">
                                <select
                                    chosen
                                    style="width: 100%;"
                                    ng-model="local.selected_city"
                                    ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in data.cities"
                                    data-placeholder="点击选择城市"
                                    no-results-text="'没有找到'"
                                    >
                                </select>
                            </div>
                        </div>
                        <div class="row grid-top">
                            <div class="col-md-6 col-md-offset-6">
                                <button class="btn btn-inverse add block-action" ng-click="addItem()">
                                    添加
                                    <span class="i i-refresh refresh-animate" ng-show="local.search_in_progress"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="one-image-container carousel-image grid-bottom" ng-repeat="item in data.items"
                         data-index="{{ $index }}" options="local.item_dnd.options"
                         hi-dnd item="item" callback="local.item_dnd.callback(info, dst_index)">
                        <div class="image-holder">
                            <div hi-uploader options="item.options"></div>
                            <span class="image-order" ng-bind="$index + 1" ng-hide="item.options.is_progressing"></span>
                            <div class="overlay">
                                <div class="overlay-button i i-share" ng-click="triggerItemImageChange( item )">
                                    <br /><span class="small-desc">更换图片</span>
                                </div>
                                <div class="overlay-button i i-trash" ng-click="deleteItem( $index )">
                                    <br /><span class="small-desc">删除点</span>
                                </div>
                            </div>
                        </div>
                        <div class="image-info" ng-show="data.home_group.type == '2'">
                            <h3 class="grid-top" ng-bind="item.cn_name"></h3>
                            <p ng-bind="item.en_name"></p>
                        </div>
                        <div class="image-info"
                             ng-show="(data.home_group.type == '1' || data.home_group.type == '3') && item.editing == false">
                            <h3 ng-bind="item.product_name"></h3>
                            <p ng-bind="item.product_desc"></p>
                        </div>
                        <div class="image-info"
                             ng-show="(data.home_group.type == '1' || data.home_group.type == '3') && item.editing == true">
                            <input class="form-control" ng-model="item.product_name" />
                            <textarea class="form-control" ng-model="item.product_desc"></textarea>
                        </div>
                        <button class="i toggle-edit"
                                ng-show="data.home_group.type == '1' || data.home_group.type == '3'"
                                ng-click="toggleItemState( $index )"
                                ng-class="{ 'i-edit' : item.editing == false, 'i-save' : item.editing == true }"
                                ng-disabled="!item.product_name || !item.product_desc"></button>
                        <div class="misc-info row" ng-show="data.home_group.type == '1' || data.home_group.type == '3'">
                            <span class="pull-left" ng-bind="item.city_name"></span>
                            <span class="pull-right" ng-bind="'¥' + item.price"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>