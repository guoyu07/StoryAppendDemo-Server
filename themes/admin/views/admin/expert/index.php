<div id="editor-list-container" ng-controller="ExpertListCtrl" class="container page-container">
    <div class="editor-head clearfix">
        当前共有{{local.total}}位专家,点击
        <button class="btn btn-inverse" ng-click="addExpert()">添加</button>
    </div>
    <div class="editor-list">
        <div class="editor-container clearfix" ng-repeat="expert in data.experts">
            <div class="delete-block" ng-click="deleteExpert( $index )">
                <span class="i i-close"></span>
            </div>
            <div class="col-md-4 head-img">
                <div hi-uploader options="expert.uploader" ng-show="expert.edit"></div>
                <img class="expert-img" ng-src="{{expert.avatar}}" alt="" ng-hide="expert.edit"/>
            </div>
            <div class="col-md-14">
                专家姓名:
                <input type="text" ng-model="expert.name"
                       class="form-control disabled-text" required
                       ng-disabled="expert.edit == false" />
                专家介绍:
                <textarea ng-model="expert.brief"
                    class="form-control disabled-text" required rows="3"
                    ng-disabled="expert.edit == false"></textarea>
            </div>
            <span class="i block-btn"
                  ng-class="{ 'i-edit': expert.edit == false, 'i-save': expert.edit == true }"
                  ng-click="toggleEdit($index)" ></span>
        </div>
    </div>
</div>