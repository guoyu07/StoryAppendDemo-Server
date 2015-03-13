<div class="states-section" ng-controller="InfoLocationCtrl" ng-if="local.tab_options.current_tab.path == 'location'">
    <button class="btn btn-inverse add-btn" ng-click="addLocation()">添加</button>
    <div class="section-head">
        <h2 class="section-title">景点位置</h2>
    </div>
    <div class="section-body location-items">
        <form name="location_form">
            <div class="location-block" ng-repeat="location in data.locations"
                 hi-dnd item="location" data-index="{{ $index }}"
                 callback="local.dnd.callback( info, dst_index )" options="local.dnd.options">
                <table class="forms-table">
                    <tr>
                        <td class="col-md-offset-2 col-md-3">
                            <label>中文名称</label>
                        </td>
                        <td>
                            <input type="text" ng-model="location.zh_name" ng-disabled="location.edit == false"
                                   class="form-control disabled-text"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-offset-2 col-md-3">
                            <label>英文名称</label>
                        </td>
                        <td>
                            <input type="text" ng-model="location.en_name" ng-disabled="location.edit == false"
                                   class="form-control disabled-text"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-offset-2 col-md-3">
                            <label>中文地址</label>
                        </td>
                        <td>
                            <input type="text" ng-model="location.zh_address" ng-disabled="location.edit == false"
                                   class="form-control disabled-text"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-offset-2 col-md-3">
                            <label>英文地址</label>
                        </td>
                        <td>
                            <input type="text" ng-model="location.en_address" ng-disabled="location.edit == false"
                                   class="form-control disabled-text"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-offset-2 col-md-3">
                            <label>景点坐标</label>
                        </td>
                        <td>
                            <input type="text" ng-model="location.latlng" ng-disabled="location.edit == false"
                                   class="form-control disabled-text" placeholder="例(25.131265,55.116935)请务必使用英文模式的标点" ng-pattern="latLng"/>
                        </td>
                    </tr>
                </table>
                <span class="location-order">{{ location.display_order }}</span>
                <span class="i block-btn"
                  ng-class="{ 'i-edit': location.edit == false, 'i-save': location.edit == true }"
                  ng-click="toggleGroupEdit( $index )"
                  ng-disabled="location_form.$invalid && local.edit_group == true"></span>
                <div class="close-block">
                    <div class="i i-close close-btn" ng-click="deleteLocation( $index )"></div>
                </div>
            </div>
        </form>
    </div>
</div>