<script type="text/ng-template" id="save_modal.html">
  <div class="modal-header">
    <h3>您需要保存对此页面的操作么？</h3>
  </div>
  <div class="modal-body ng-scope">
    <button class="btn btn-primary" ng-click="saveForm()">保存</button>
    <button class="btn btn-warning" ng-click="navigate()">不保存</button>
  </div>
</script>
<script type="text/ng-template" id="image_modal.html">
  <div class="modal-header">
    <h3>{{message}}</h3>
  </div>
  <div class="modal-body ng-scope">
    <button class="btn btn-primary" ng-click="close()">我知道了</button>
  </div>
</script>
<script type="text/ng-template" id="sidebar.html">
  <aside class="col-xs-4 section-info">
    <h3 data-ng-bind="current_section.title"></h3>

    <p data-ng-bind="current_section.description"></p>
  </aside>
</script>
<script type="text/ng-template" id="sale_range_type.html">
  <div class="edit-body">
    <div class="input-group button-select range-or-single">
      <div class="input-wrapper" data-ng-show="1 == model.sale_range_type">
        <input type="number" min="1" class="form-control" data-ng-model="monthCount" data-ng-change="changeMonth()">
      </div>
      <div class="input-wrapper datepicker-group" data-ng-show="0 == model.sale_range_type">
        <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd" data-ng-model="model.from_date"
               is-open="from_opened" data-ng-click="from_opened = true" close-text="关闭" show-weeks="false"
               show-button-bar="false" />
        <span class="midline"></span>
        <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd" data-ng-model="model.to_date"
               is-open="to_opened" data-ng-click="to_opened = true" close-text="关闭" show-weeks="false"
               show-button-bar="false" />
      </div>
      <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
          {{options[model.sale_range_type]}} <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">
          <li data-ng-repeat='(value, label) in options'>
            <label>
              <input type="radio" name="{{name}}" value="{{value}}" data-ng-checked="value == current_item"
                     data-ng-model="model.sale_range_type" />
              {{label}}
            </label>
          </li>
        </ul>
      </div>
    </div>
  </div>
</script>
<script type="text/ng-template" id="close_date.html">
  <div class="edit-body all-close-dates">
    <button class="btn add-closedate tagsinput-add" data-ng-click="append()"></button>
    <div class="{{current_range.current_item}}-input" data-ng-repeat="current_range in all_ranges">
      <div data-ng-include="'one_close_date.html'"></div>
    </div>
  </div>
</script>
<script type="text/ng-template" id="one_close_date.html">
  <span data-ng-show="current_range.current_item == 'weekday'" class="usage-desc">例：周6;周7</span>
  <span data-ng-show="current_range.current_item == 'singleday'" class="usage-desc">例：2014-05-01;2018-09-20</span>
  <br />
  <div class="input-group button-select range-or-single">
    <div class="input-wrapper" data-ng-if="current_range.current_item != 'range'">
      <input type="text" name="data{{$index}}" class="form-control" data-ng-model="current_range.data" required>
    </div>
    <div class="input-wrapper datepicker-group" data-ng-if="current_range.current_item == 'range'">
      <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
             data-ng-model="current_range.data[0]" is-open="range_from" data-ng-click="range_from = true"
             close-text="关闭" placeholder="开始时间" show-weeks="false" show-button-bar="false" name="from{{$index}}" required />
      <span class="midline"></span>
      <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
             data-ng-model="current_range.data[1]" is-open="range_to" data-ng-click="range_to = true"
             close-text="关闭" placeholder="结束时间" show-weeks="false" show-button-bar="false" name="to{{$index}}" required />
    </div>
    <div class="input-group-btn">
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        {{current_range.items[current_range.current_item]}} <span class="caret"></span></button>
      <ul class="dropdown-menu pull-right">
        <li data-ng-repeat='(value, label) in current_range.items'>
          <label style="width: 100%;">
            <input type="radio" name="{{current_range.name}}" value="{{value}}"
                   data-ng-checked="value == current_range.current_item"
                   data-ng-model="current_range.current_item" />
            {{label}}
          </label>
        </li>
      </ul>
    </div>
  </div>
  <span class="del-closedate" data-ng-attr-data-index="{{$index}}">删除</span>
</script>
<script type="text/ng-template" id="passenger_info_select_box.html">
  <div class="one-passenger-edit-box input-section {{names.en_name}}">
    <h4>{{names.cn_name}}信息</h4>

    <div class="one-passenger-list-box">
				<span class="container-box">
				<button data-ng-repeat="checkeditem in allcriteria" class="btn one-criteria" data-ng-value="{{checkeditem.id}}" data-ng-click="uncheckItem(checkeditem.id)" data-ng-show="isChecked(checkeditem.id)">
          {{checkeditem.label}}
        </button>
			</span>
      <button class="btn add-criteria tagsinput-add" data-ng-click="hidelist = !hidelist" data-ng-class="{ expand: !hidelist }"></button>
    </div>
    <div class="one-passenger-select-box" data-ng-hide="hidelist">
				<span data-ng-repeat="uncheckeditem in allcriteria">
					<h4 class="group-title" data-ng-show="uncheckeditem.group">{{uncheckeditem.group_title}}</h4>
					<button class="btn one-criteria one-allcriteria" data-ng-value="{{uncheckeditem.id}}" data-ng-class="{ checked: isChecked(uncheckeditem.id) }" data-ng-click="toggleItem(uncheckeditem.id, $event)" data-ng-show="!uncheckeditem.group">
            {{uncheckeditem.label}}
          </button>
				</span>
    </div>
  </div>
</script>
<script type="text/ng-template" id="close_any_date.html">
    <div class="edit-body all-close-dates">
        <button class="btn add-closedate tagsinput-add" data-ng-click="append()"></button>
        <div class="{{current_range.current_item}}-input" data-ng-repeat="current_range in all_ranges">
            <div data-ng-include="'single_close_date.html'"></div>
        </div>
    </div>
</script>
<script type="text/ng-template" id="single_close_date.html">
    <span data-ng-show="current_range.current_item == 'weekday'" class="usage-desc">例：周6;周7</span>
    <span data-ng-show="current_range.current_item == 'singleday'" class="usage-desc">例：2014-05-01;2018-09-20</span>
    <br />
    <div class="input-group button-select range-or-single">
        <div class="input-wrapper" data-ng-if="current_range.current_item != 'range'">
            <input type="text" name="data{{$index}}" class="form-control" data-ng-model="current_range.data" required>
        </div>
        <div class="input-wrapper datepicker-group" data-ng-if="current_range.current_item == 'range'">
            <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                   min="current_range.min_date" max="current_range.max_date"
                   data-ng-model="current_range.data[0]" is-open="range_from" data-ng-click="range_from = true"
                   close-text="关闭" placeholder="开始时间" show-weeks="false" show-button-bar="false" name="from{{$index}}" required />
            <span class="midline"></span>
            <input type="text" class="form-control datepicker" datepicker-popup="yyyy-MM-dd"
                   min="current_range.min_date" max="current_range.max_date" data-test="{{current_rante.min_date}}"
                   data-ng-model="current_range.data[1]" is-open="range_to" data-ng-click="range_to = true"
                   close-text="关闭" placeholder="结束时间" show-weeks="false" show-button-bar="false" name="to{{$index}}" required />
        </div>
        <div class="input-group-btn">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {{current_range.items[current_range.current_item]}} <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right">
                <li data-ng-repeat='(value, label) in current_range.items'>
                    <label style="width: 100%">
                        <input type="radio" name="{{current_range.name}}" value="{{value}}"
                               data-ng-checked="value == current_range.current_item"
                               data-ng-model="current_range.current_item" />
                        {{label}}
                    </label>
                </li>
            </ul>
        </div>
    </div>
    <span class="del-closedate" data-ng-attr-data-index="{{$index}}">删除</span>
</script>