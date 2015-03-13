<script type="text/ng-template" id="meta.html">
    <div class="section-subtitle row">
        <span ng-bind="pax.label"></span>
        <button class="btn btn-inverse" ng-if="is_leader == 1 && !local.section_head.passenger_info.is_edit" ng-click="syncCommonInfo()">同步出行人信息</button>
    </div>
    <div class="section-subbody row">
        <div class="col-md-6 one-meta passenger-meta" ng-repeat="meta in pax.meta">
            <label class="meta-label" ng-bind="meta.label"></label>
            <span class="meta-value" ng-bind="meta.value" ng-hide="local.section_head.passenger_info.is_edit"></span>
            <div class="meta-value-container" ng-if="local.section_head.passenger_info.is_edit && meta.input_type == 'text'">
                <input type="text" ng-model="meta.value" class="form-control" />
            </div>
            <div class="meta-value-container" ng-if="local.section_head.passenger_info.is_edit && meta.input_type == 'date'">
                <quick-datepicker ng-model='meta.date' disable-timepicker='true' date-format='yyyy-M-d'></quick-datepicker>
            </div>
            <div class="meta-value-container" ng-if="local.section_head.passenger_info.is_edit && meta.input_type == 'enum'">
                <select chosen
                        style="width: 99%;"
                        ng-model="meta.select"
                        ng-options="item.value as item.title for item in meta.dropdown track by item.value"
                        data-placeholder="点击选择{{meta.label}}"
                    ></select>
            </div>
            <div class="meta-value-container" ng-if="local.section_head.passenger_info.is_edit && meta.input_type == 'age'">
                <select chosen
                        style="width: 99%;"
                        ng-model="meta.value"
                        ng-options="age for age in meta.dropdown"
                    ></select>
            </div>
        </div>
    </div>
</script>