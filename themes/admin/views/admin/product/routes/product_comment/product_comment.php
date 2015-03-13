<script type="text/ng-template" id="ProductComment.html">
    <div class="states-section grid-top">
        <div class="section-head">
            <h2 class="section-title">用户点评</h2>
        </div>
        <div class="col-md-offset-1 col-md-16 comment-body">
            <div class="row">
                <button class="col-md-offset-7 col-md-4 btn comment-btn i i-plus"
                        ng-click="addComments()">
                    添加评论
                </button>
            </div>
            <form name="comment_list_form" novalidate>
                <div class="row comment-ctn" ng-repeat="comment in data.comments">
                    <div class="delete-block" ng-click="delComments( $index )">
                        <span class="i i-close"></span>
                    </div>
                    <div class="block-ctn">
                        <div class="row info-ctn">
                            <div class="col-md-6" ng-bind="comment.customer.firstname">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-inverse" ng-click="toggleCustomer($index)"
                                        ng-show="comment.is_edit">
                                    变更用户
                                </button>
                            </div>
                            <label class="col-md-3">点评分数：</label>
                            <div class="col-md-6" ng-show="comment.is_edit">
                                <select
                                    chosen
                                    style="width: 100%;"
                                    ng-model="comment.hitour_service_level"
                                    ng-options="score for score in local.comment_scores"
                                    disable-search="true">
                                </select>
                            </div>
                            <label class="col-md-6" ng-hide="comment.is_edit" ng-bind="comment.hitour_service_level"></label>
                        </div>
                        <div class="row grid-top grid-bottom">
                            <div class="col-md-18" ng-show="comment.is_edit">
                                <textarea class="form-control height-control" required ng-model="comment.content"></textarea>
                            </div>
                            <div class="col-md-18" ng-hide="comment.is_edit" ng-bind="comment.content"></div>
                        </div>
                        <button class="save-btn btn btn-inverse col-md-2 col-md-offset-8" ng-disabled="comment_list_form.$invalid" ng-click="toggleEdit($index)" ng-show="comment.is_edit">保存</button>
                        <button class="i edit-btn i-edit" ng-click="toggleEdit($index)" ng-hide="comment.is_edit"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</script>



