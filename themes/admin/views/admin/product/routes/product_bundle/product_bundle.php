<script type="text/ng-template" id="ProductBundle.html">
    <div class="states-section">
        <div class="section-head">
            <h2 class="section-title">商品挂接</h2>
        </div>
        <div class="one-bundle-group-set">
            <div class="set-title">套餐包含</div>
            <div class="one-bundle-group tags-container" ng-init="group_index = 0; current_tag = local.input_tag[group_index];">
                <div class="row">
                    <div class="group-title col-md-16">
                        酒店多选一
                    </div>
                    <button class="btn btn-inverse block-action" ng-show="current_tag.is_edit" ng-click="toggleBundle(group_index)">
                        保存
                    </button>
                    <button class="btn btn-inverse block-action" ng-show="!current_tag.is_edit" ng-click="toggleBundle(group_index)">
                        编辑
                    </button>
                </div>

                <div class="input-row row" ng-show="current_tag.is_edit">
                    <div class="col-md-9">
                        <input type="number" class="form-control" placeholder="{{ current_tag.placeholder }}" ng-model="current_tag.search_text" min="0" />
                        <button class="btn btn-inverse block-action add" ng-click="addBundleProduct(current_tag.search_text, group_index)" ng-disabled="!current_tag.search_text">
                            <span ng-bind="current_tag.button_label"></span>
                            <span class="i i-refresh refresh-animate" ng-show="current_tag.in_progress"></span>
                        </button>
                    </div>
                    <div class="col-md-9">
                        <span class="process-msg" ng-class="{ 'error' : current_tag.is_error }" ng-show="current_tag.message" ng-bind="current_tag.message"></span>
                    </div>
                </div>

                <div class="bundle-list">
                    <div class="one-item clearfix" ng-repeat="item in data.bundles[group_index].items">
                        <div class="col-md-2">
                            {{$index + 1}}
                        </div>
                        <div class="col-md-13">
                            {{item.binding_product_id}} - {{item.product.description.name}}
                        </div>
                        <div class="col-md-3 text-right" ng-show="current_tag.is_edit">
                            <a ng-click="deleteBundleProduct(item.product.product_id, group_index)">取消挂接</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="one-bundle-group" ng-init="group_index = 1">
                <div class="group-title">套餐赠送商品</div>
            </div>
        </div>
        <div class="one-bundle-group-set">
            <div class="set-title">独享特惠</div>
            <div class="one-bundle-group" ng-init="group_index = 2">

            </div>
        </div>
    </div>
</script>