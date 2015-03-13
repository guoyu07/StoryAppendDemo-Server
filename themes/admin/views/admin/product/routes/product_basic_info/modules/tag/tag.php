<div class="states-section" ng-if="local.tab_options.current_tab.path == 'tag'">
    <div ng-controller="InfoTagCtrl">
        <div class="view-tag-container">
            <div class="row view-tag-content">
                <div class="single-tag" ng-repeat="tag in product_tags">
                    <span ng-if="tag.tag.parent_tag_id > 0" ng-bind="tag.tag.parent_tag_name"></span>
                    <span ng-if="tag.tag.parent_tag_id > 0">/</span>
                    <span ng-bind="tag.tag.name"></span>
                </div>
            </div>
            <div class="row">
                <button class="col-md-4 col-md-offset-7 btn config-btn" ng-click="editTag()">配置商品标签</button>
            </div>
        </div>

        <div class="edit-tag-container" ng-show="local.edit_tag">
            <div class="overlay"></div>
            <div class="edit-tag-dialog">
                <div class="row">
                    <label class="dot-head">为此商品选择标签</label>
                </div>
                <div class="row tag-selector">
                    <div class="tag-level-selector col-md-8">
                        <div class="row single-tag" ng-repeat="tag in parent_tag">
                            <div class="col-md-1 tag-check-box" ng-click="selectTag('parent', $index)">
                                <div class="check-box" ng-class="{'disabled' : tag.has_child == '1'}">
                                    <i class="i" ng-class="{'i-check' : local.selected_parent_tags.indexOf(tag.tag_id) > -1}"></i>
                                </div>
                            </div>
                            <div class="col-md-13 tag-content" ng-class="{'selected' : local.current_parent_tag == tag.tag_id}"
                                 ng-click="selectTag('parent', $index)">
                                <div class="col-md-16 tag-name" ng-bind="tag.name">
                                </div>
                                <div class="col-md-2 tag-arrow" ng-show="tag.has_child == '1'">
                                    <div class="arrow" ng-class="{'selected' : local.current_parent_tag == tag.tag_id}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="vertical-separate-lines col-md-1"></div>
                    <div class="tag-level-selector col-md-8">
                        <div class="row single-tag" ng-repeat="tag in sub_tag" ng-show="local.current_parent_tag == tag.parent_tag_id">
                            <div class="col-md-1 tag-check-box" ng-click="selectTag('sub', $index)">
                                <div class="check-box">
                                    <i class="i" ng-class="{'i-check' : local.selected_sub_tags.indexOf(tag.tag_id) > -1}"></i>
                                </div>
                            </div>
                            <div class="col-md-13 tag-content" ng-click="selectTag('sub', $index)">
                                <div class="col-md-16 tag-name" ng-bind="tag.name"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <button class="col-md-4 col-md-offset-7 btn config-btn" ng-click="applyTagChange()">确定</button>
                </div>
                <div class="close-btn" ng-click="closeTagDialog()">
                    <i class="i i-close"></i>
                </div>
            </div>
        </div>
    </div>
</div>