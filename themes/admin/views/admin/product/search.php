<script type="text/javascript">
    var request_urls = JSON.parse('<?php echo !empty($request_urls) ? json_encode($request_urls) : json_encode(array()); ?>');
    var qs = "<?= $qs ?>";
</script>
<div id="product-search-container" data-ng-app="ProductApp" data-ng-controller="productListCtrl">
    <nav id="search-bar" class="row">
        <div class="form-group col-xs-3 search-filter product-city">
            <select chosen
                    style="width: 100%;"
                    ng-model="filterCriteria.city"
                    ng-options="city as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in cities track by city.city_code"
                    no-results-text="'没有找到'"
                    ng-change="filterResult()"
                >
            </select>
        </div>
        <div class="form-group col-xs-3 search-filter product-vendor">
            <select chosen
                    style="width: 100%;"
                    ng-model="filterCriteria.supplier"
                    ng-options="vendor as vendor.name group by vendor.group for vendor in vendors track by vendor.supplier_id"
                    no-results-text="'没有找到'"
                    ng-change="filterResult()"
                >
            </select>
        </div>
        <div class="form-group col-xs-3 search-filter product-type">
            <select chosen
                    style="width: 200px;"
                    no-results-text="'没有找到'"
                    ng-model="filterCriteria.type"
                    ng-options="type as type.label for type in product_type track by type.value"
                    ng-change="filterResult()"
                >
            </select>
        </div>
        <div class="form-group col-xs-4 search-filter product-search-query">
            <input data-on-enter-blur data-on-blur-change="filterResult()" data-ng-model="filterCriteria.product"
                   type="text" class="form-control" placeholder="商品关键字／商品id" />
            <span class="input-icon fui-search"></span>
        </div>
        <div class="col-md-2 pull-right">
            <button class="btn btn-inverse" ng-click="goToImport()">GTA商品导入</button>
        </div>
        <div class="col-md-2 pull-right">
            <button class="btn btn-inverse" ng-click="productCheck()">商品检查</button>
        </div>
    </nav>
    <table class="table table-striped" id="product-list">
        <thead>
            <tr class="heading-row">
                <th style="width: 6%;"></th>
                <th data-ng-repeat="header in headers" style="width: {{header.width}}">
                    <span data-ng-if="header.value==''" class="sort-heading">{{ header.title }}</span>
					<span data-ng-if="header.value!=''" class="sort-heading">
					<sort-by onsort="onSort" sortDir="filterCriteria.sortDir" sortedby="filterCriteria.sortedBy"
                             sortValue="{{ header.value }}">{{ header.title }}
                    </sort-by>
					</span>
                </th>
                <th style="width: 8%;">
                    <div class="dropdown product-status">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">状态<span
                                class="caret"></span></button>
                        <span class="dropdown-arrow"></span>
                        <ul class="dropdown-menu">
                            <li data-ng-repeat="one_status in status">
                                <a class="status {{one_status.class}}"
                                   data-ng-click="updateStatus(one_status.status_id)">{{one_status.status_name}}</a>
                            </li>
                        </ul>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr data-ng-repeat="product in products">
                <td>
                    <a data-ng-href="<?= Yii::app()->params['urlPreview'] ?>{{product.product_id}}"
                       class="input-icon fui-eye preview-link" target="_blank"></a>
                </td>
                <td>
                    {{product.product_id}}
                </td>
                <td>
                    <a href="#" data-ng-click="editProduct(product.product_id)" class="edit-product-link">
                        {{product.name}}
                    </a>
                </td>
                <td>
                    {{product.price}}
                </td>
                <td>
                    {{product.city_cn_name}}
                </td>
                <td>
                    {{product.m_name}}
                </td>
                <td class="text-center status status{{product.status}}">
                    {{statusConvert[product.status]}}
                </td>
            </tr>
        </tbody>
    </table>
    <div data-ng-show="productsCount == 0" class="text-center">
        没有结果
    </div>
    <ht-pagination totalpages="totalPages" currentpage="filterCriteria.pageNumber"
                   selectpage="selectPage"></ht-pagination>
</div>
