<div class="states-section" ng-if="local.tab_options.current_tab.path == 'city'">
    <div ng-controller="InfoCityCtrl">
        <hi-section-head options="local.section_head"></hi-section-head>
        <!--编辑态-->
        <div class="section-body clearfix" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
            <form name="basicinfo_city_form" hi-watch-dirty="local.path_name">
                <table class="forms-table">
                    <tr>
                        <td>
                            <label>所属城市</label>
                        </td>
                        <td>
                            <select chosen required style="width: 200px;" data-placeholder="选择城市"
                                    ng-model="data.info.city_code"
                                    ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in local.search_list.cities">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>更多城市</label>
                        </td>
                        <td class="col-md-12 pad-top">
                            <hi-select-tag options="local.select_tag.other_cities" select="local.search_list.cities"
                                           model="data.info.other_cities"></hi-select-tag>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <!--显示态-->
        <div class="section-body clearfix" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
            <table class="forms-table">
                <tr>
                    <td class="view-title" style="width: 100px;">所属城市</td>
                    <td class="view-body" ng-bind="getLabel(local.search_list.cities, 'city_code', data.info.city_code, 'city_name')"></td>
                </tr>
                <tr>
                    <td class="view-title">更多城市</td>
                    <td class="view-body">
                        <span class="pad-right" ng-repeat="city in data.info.other_cities" ng-bind="getLabel(data.info.other_cities, 'city_code', city.city_code, 'city_name')"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>