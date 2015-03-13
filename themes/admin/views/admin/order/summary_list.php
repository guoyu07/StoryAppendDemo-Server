<div id="order-summary-container" data-ng-controller="OrderSummaryListCtrl" class="container page-container">
    <div class="row">
        <div class="search-ctn col-md-12 col-md-offset-3">
            <div class="basic-search row">
                <div class="col-md-18">
                    <div class="button-selections">
                        <button class="btn btn-select-option"
                                data-ng-repeat="item in local.visible_search_options"
                                data-ng-click="toggleSelection($index)"
                                data-ng-class="{ selected: item.status == 1 }">
                            {{item.value}}
                        </button>
                    </div>
                    <input type="text" class="form-control basic-search-text" ng-model="local.search_text"
                           hi-enter="search()" />
                </div>
            </div>

            <div class="row advance-search">
                <div class="supplier-and-status row">
                    <div class="row" style="margin-bottom: 10px;">
                        <label class="col-md-3 text-right">
                            供应商
                        </label>

                        <div class="col-md-6">
                            <select chosen
                                    style="width: 100%;"
                                    ng-model="local.grid_options.query.query_filter.filter_supplier_id"
                                    ng-change="filterSupplier()"
                                    ng-options="supplier.supplier_id as supplier.name group by supplier.group for supplier in local.supplier_list track by supplier.supplier_id"
                                    data-placeholder="选择供应商"
                                    no-results-text="'没有找到'"
                                >
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" ng-show="local.show_advance_search">
                    <div class="row">
                        <label class="col-md-3 text-right">
                            订单分类状态
                        </label>
                        <div class="col-md-14">
                            <div class="row">
                                <label class="hi-radio col-md-3" ng-repeat="status in local.order_status">
                                    <input
                                        type="radio"
                                        name="one_status"
                                        value="{{ status.key }}"
                                        ng-model="local.search_order_status"
                                        >
                                    <span class="inner-text" ng-bind="status.label"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-md-3 text-right">
                            商品名称/ID
                        </label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" ng-model="local.search_product_text"
                                   hi-enter="search()" />
                        </div>

                        <label class="col-md-2 text-right">
                            出行人
                        </label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" ng-model="local.search_passenger_text"
                                   placeholder="中文/拼音"
                                   hi-enter="search()" />
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-md-3 text-right">
                            下单日期
                        </label>

                        <div class="col-md-6">
                            <quick-datepicker ng-model='local.search_added_from_date' disable-timepicker='true'
                                              date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                        <label class="col-md-2 text-center">
                            ——
                        </label>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='local.search_added_to_date' disable-timepicker='true'
                                              date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-md-3 text-right">
                            使用日期
                        </label>

                        <div class="col-md-6">
                            <quick-datepicker ng-model='local.search_tour_from_date' disable-timepicker='true'
                                              date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                        <label class="col-md-2 text-center">
                            ——
                        </label>
                        <div class="col-md-6">
                            <quick-datepicker ng-model='local.search_tour_to_date' disable-timepicker='true'
                                              date-format='yyyy-M-d'></quick-datepicker>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-md-offset-3 search-btn">
                            <button class="btn btn-inverse" ng-click="search()">搜索</button>
                        </div>
                        <div class="col-md-3">
                            <label class="clear-search" data-ng-click="clearCriteria()">清空搜索条件</label>
                        </div>
                    </div>
                </div>
                <div class="row expand-criteria">
                    <div class="col-md-3 col-md-offset-7">
                        <button class="expand-btn" ng-show="!local.show_advance_search"
                                data-ng-click="toggleAdvanceSearch()">高级搜索
                        </button>
                        <button class="expand-btn" ng-show="local.show_advance_search"
                                data-ng-click="toggleAdvanceSearch()">收起
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hi-grid options="local.grid_options"></hi-grid>
</div>