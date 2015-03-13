<script type="text/ng-template" id="editProductComment.html">
    <div class="edit-section last clearfix">
        <div class="col-md-13 col-md-offset-1">
            <form name="product_seo" novalidate>
                <div class="row edit-heading with-dot">
                    <h2> 商品评论 </h2>
                </div>
                <div class="row edit-body">
                    <div class="row">
                        <button class="col-xs-offset-6 col-xs-6 btn btn-hg btn-primary save-form i i-plus"
                                data-ng-click="addComments()">
                            添加评论
                        </button>
                    </div>
                    <div class="row comment-ctn" data-ng-repeat="comment in data.comments">
                        <div class="delete-block" ng-click="delComments( $index )">
                            <span class="i i-close"></span>
                        </div>
                        <div class="block-ctn" ng-show="comment.is_edit">
                            <div class="row mr10">
                                <div class="row info-ctn">
                                    <div class="col-md-4 col-md-offset-1">
                                        <img
                                            ng-src="{{comment.customer.avatar_url}}"
                                            class="user-avatar" height="120" width="120" />
                                    </div>
                                    <div class="col-md-7">
                                        <div class="col-md-18 user-name" ng-bind = "comment.customer.firstname">
                                        </div>
                                        <div class="col-md-8">
                                            <button class="change-btn btn btn-hg btn-primary" ng-click = "toggleCustomer($index)">
                                                变更用户
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 rating-ctn">
                                        <div class="row">
                                            <div class="col-md-7 rating-title">
                                                玩途服务 :
                                            </div>
                                            <div class="col-md-10 rate-ctn">
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 1" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 1)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 1" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 1)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 2" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 2)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 2" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 2)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 3" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 3)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 3" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 3)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 4" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 4)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 4" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 4)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 5" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 5)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 5" height="16"
                                                     width="16" ng-click = "changeHitourRate($index, 5)"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 rating-title">
                                                供应商服务:
                                            </div>
                                            <div class="col-md-10 rate-ctn">
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 1" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 1)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 1" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 1)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 2" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 2)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 2" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 2)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 3" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 3)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 3" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 3)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 4" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 4)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 4" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 4)"/>
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 5" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 5)"/>
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 5" height="16"
                                                     width="16" ng-click = "changeSupplierRate($index, 5)"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-15">
                                        <textarea class="form-control commment-ctn"
                                                  ng-model="comment.content" placeholder="请输入长度为10—255之间的评论" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <button class="i block-btn i-save pull-right" style="margin-right: 20px;" valid-support
                                    support="local_supports" ng-click="toggleEdit($index)"></button>
                        </div>
                        <div class="block-ctn" ng-hide="comment.is_edit">
                            <div class="row mr10">
                                <div class="row info-ctn">
                                    <div class="col-md-4 col-md-offset-1">
                                        <img
                                            ng-src="{{comment.customer.avatar_url}}"
                                            class="user-avatar" height="120" width="120" />
                                    </div>
                                    <div class="col-md-7">
                                        <div class="col-md-18 user-name" ng-bind = "comment.customer.firstname">
                                        </div>
                                    </div>
                                    <div class="col-md-6 rating-ctn">
                                        <div class="row">
                                            <div class="col-md-7 rating-title">
                                                玩途服务 :
                                            </div>
                                            <div class="col-md-10">
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 1" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 1" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 2" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 2" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 3" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 3" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 4" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 4" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.hitour_service_level >= 5" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.hitour_service_level < 5" height="16"
                                                     width="16" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 rating-title">
                                                供应商服务:
                                            </div>
                                            <div class="col-md-10">
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 1" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 1" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 2" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 2" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 3" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 3" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 4" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 4" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_1.png"
                                                     ng-show="comment.supplier_service_level >= 5" height="16"
                                                     width="16" />
                                                <img src="/themes/admin/images/comments/star_2.png"
                                                     ng-show="comment.supplier_service_level < 5" height="16"
                                                     width="16" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-15" ng-bind-html = "comment.display_content"></div>
                                </div>
                            </div>
                            <button class="i block-btn i-edit pull-right" style="margin-right: 20px;"
                                    ng-click="toggleEdit($index)"></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</script>