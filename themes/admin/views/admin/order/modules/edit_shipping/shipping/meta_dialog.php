<div class="states-section">
    <div id="meta-dialog" ng-if="local.show_pax_dialog">
        <div class="cover-layer" ng-click="dismissDialog()">
            <div class="loading-container" ng-show="local.show_pax_dialog && local.dialog_loading">
                <span class="i i-refresh refresh-animate"></span>
                <label>Loading...</label>
            </div>
        </div>

        <div class="section-body row dialog-container" ng-show="!local.dialog_loading">
            <div class="section-subtitle" ng-bind="data.dialog_user.label"></div>
            <div class="section-subbody row">
                <div class="col-md-6 one-meta passenger-meta" ng-repeat="meta in data.dialog_user.meta">
                    <label class="meta-label" ng-bind="meta.label"></label>
                    <span class="meta-value" ng-bind="meta.value"></span>
                </div>
            </div>
        </div>
    </div>
</div>