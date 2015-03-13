<div id="for_test" class="container page-container" ng-controller="SiteTestCtrl">
    <h1 class="super-large text-center">后台标准集合</h1>
    <section class="text-content pad-top">
        <h1>我是1号标题</h1>
        <h2>我是2号标题</h2>
        <h3>我是3号标题</h3>
        <h4>我是4号标题</h4>
        <h5>我是5号标题</h5>
        <h6>我是6号标题</h6>
        <p>
            我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字我是一坨默默无闻的文字
        </p>
        <p class="small-desc">
            我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字我是一坨默默无闻的小文字
        </p>
    </section>
    <section class="buttons pad-top">
        <h2>Buttons</h2>
        <div class="row">
            <div class="col-md-6">
                <h3>保存／编辑</h3>
                <div class="col-md-9">
                    <button class="block-action btn btn-inverse">保存</button>
                    <br />
                    <button class="block-action btn btn-inverse hover">保存</button>
                    <br />
                    <button class="block-action btn btn-inverse active">保存</button>
                </div>
                <div class="col-md-9">
                    <button class="block-action btn btn-inverse">编辑</button>
                    <br />
                    <button class="block-action btn btn-inverse hover">编辑</button>
                    <br />
                    <button class="block-action btn btn-inverse active">编辑</button>
                </div>
            </div>
            <div class="col-md-6">
                <h3>新增／删除</h3>
                <button class="block-action add btn btn-inverse">新增</button>
                <br />
                <button class="block-action add btn btn-inverse hover">新增</button>
                <br />
                <button class="block-action add btn btn-inverse active">新增</button>
            </div>
            <div class="col-md-6">
                <h3>展开／收起</h3>
                <div class="col-md-9">
                    <button class="toggle-btn btn btn-inverse"></button>
                    <br />
                    <button class="toggle-btn btn btn-inverse hover"></button>
                </div>
                <div class="col-md-9">
                    <button class="toggle-btn expanded btn btn-inverse"></button>
                    <br />
                    <button class="toggle-btn expanded btn btn-inverse hover"></button>
                </div>
            </div>
        </div>
    </section>
    <section class="input pad-top">
        <h2>输入</h2>
        <div class="row">
            <div class="col-md-6">
                <h3>普通</h3>
                <input class="form-control" placeholder="我是一个placeholder" />
            </div>
            <div class="col-md-6">
                <h3>日历</h3>
                <p class="small-desc">https://github.com/adamalbrecht/ngQuickDate</p>
                {{ data.input.date }}
                <quick-datepicker ng-model='data.input.date' disable-timepicker='true'
                                  date-format='yyyy-M-d'></quick-datepicker>
            </div>
            <div class="col-md-6">
                <h3>图片上传</h3>
                <p class="small-desc">上传组件是固定高度，宽度基于真实图片大小等比计算</p>
                <div hi-uploader options="local.uploader.options"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>选择框带optgroup</h3>

                <p class="small-desc">https://github.com/localytics/angular-chosen</p>
                <select
                    chosen
                    style="width: 100%;"
                    ng-model="data.chosen.current_city"
                    ng-options="city.city_code as city.cn_name group by city.country_code for city in data_set.cities"
                    disable-search="true"
                    no-results-text="'没有找到'"
                    data-placeholder="选择一个选项"
                    >
                </select>
            </div>
            <div class="col-md-6">
                <h3>选择框加搜索</h3>

                <select
                    chosen
                    style="width: 100%;"
                    ng-model="data.chosen.current_city_with_search"
                    ng-options="city.city_code as city.cn_name group by city.country_code for city in data_set.cities"
                    no-results-text="'没有找到'"
                    data-placeholder="选择一个选项"
                    >
                </select>
            </div>
            <div class="col-md-6">
                <h3>选择框</h3>

                <select
                    chosen
                    style="width: 100%;"
                    multiple="multiple"
                    ng-model="data.chosen.current_cities"
                    ng-options="city.city_code as city.cn_name group by city.country_code for city in data_set.cities"
                    no-results-text="'没有找到'"
                    data-placeholder="选择一个选项"
                    >
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <h3>文本框</h3>
                <textarea class="form-control" rows="8"></textarea>
            </div>
            <div class="col-md-9">
                <h3>Markdown文本框</h3>
                <div hi-markdown options="local.markdown.options" input="local.markdown.input"
                     output="local.markdown.output"></div>
            </div>
        </div>
    </section>
    <section class="components pad-top">
        <h2>组件</h2>
        <div class="row">
            <div class="col-md-9">
                <h3>文本加下拉</h3>
                {{ local.dropdown.values }}
                <div hi-input-dropdown options="local.dropdown.options" model="local.dropdown.values"></div>
            </div>
            <div class="col-md-9">
                <h3>Radio</h3>
                {{ data.one_fruit }}<br />
                <label class="hi-radio" ng-repeat="fruit in data_set.fruits">
                    <input
                        type="radio"
                        name="one_fruit"
                        value="{{ fruit }}"
                        ng-model="data.input_set.one_fruit"
                        >
                    <span class="inner-text" ng-bind="fruit"></span>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <h3>Tagged Checkbox</h3>
                {{ data.input_set.tagged_fruits }}<br />
                <div class="row">
                    <div class="one-tag" ng-repeat="fruit in data_set.fruits"
                         ng-class="{ 'selected' : data.input_set.tagged_fruits.indexOf(fruit) > -1 }"
                         ng-click="toggleSelection(fruit, data.input_set.tagged_fruits)" ng-bind="fruit"></div>
                </div>
            </div>
            <div class="col-md-9">
                <h3>Checkbox</h3>
                {{ data.input_set.selected_fruits }}<br />
                <label class="hi-checkbox" ng-repeat="fruit in data_set.fruits">
                    <input
                        type="checkbox"
                        name="data.input_set.selected_fruits[]"
                        value="{{ fruit }}"
                        ng-checked="data.input_set.selected_fruits.indexOf(fruit) > -1"
                        ng-click="toggleSelection(fruit, data.input_set.selected_fruits)"
                        >
                    <span class="inner-text" ng-bind="fruit"></span>
                </label>
            </div>
        </div>
        <div class="row">
            <h3>表单</h3>
            <p class="small-desc">TODO: Grid</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>抬头1</th>
                        <th>抬头2</th>
                        <th>抬头3</th>
                        <th>抬头4</th>
                        <th>抬头5</th>
                        <th>抬头6</th>
                        <th>抬头7</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1875</td>
                        <td>梦幻“时光之翼”（原“海之颂”）现场演出【20:40场】</td>
                        <td>¥ 428 / ¥ 408</td>
                        <td>新加坡</td>
                        <td>spring.H</td>
                        <td>编辑中</td>
                        <td><span class="i i-eye"></span></td>
                    </tr>
                    <tr>
                        <td>1875</td>
                        <td>梦幻“时光之翼”（原“海之颂”）现场演出【20:40场】</td>
                        <td>¥ 428 / ¥ 408</td>
                        <td>新加坡</td>
                        <td>spring.H</td>
                        <td>编辑中</td>
                        <td><span class="i i-eye"></span></td>
                    </tr>
                    <tr>
                        <td>1875</td>
                        <td>梦幻“时光之翼”（原“海之颂”）现场演出【20:40场】</td>
                        <td>¥ 428 / ¥ 408</td>
                        <td>新加坡</td>
                        <td>spring.H</td>
                        <td>编辑中</td>
                        <td><span class="i i-eye"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class="overlay-section pad-top">
        <h2>弹窗</h2>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-inverse" id="test_alert">通知弹窗，类似于alert</button>
                <div hi-overlay options="local.overlay.options_one"></div>
            </div>
            <div class="col-md-6">
                <button class="btn btn-inverse" id="test_confirm">确认弹窗，类似于confirm</button>
                <div hi-overlay options="local.overlay.options_two"></div>
            </div>
            <div class="col-md-6">
                <button class="btn btn-inverse" ng-click="alertDefaultMsg()">使用rootCtrl里面的overlay</button>
            </div>
        </div>
    </section>
    <section class="breadcrumb-section pad-top">
        <h2>面包屑</h2>
        <div class="breadcrumb">
            <div class="go-back border part">
                <span class="i i-arrow-left"></span>
            </div>
            <div class="current-position border part">
                香港
            </div>
            <div class="main-content part">
                <span class="i i-eye"></span>
                我是一段莫名奇妙的文字
                <div class="pull-right">
                    <span class="i i-plus"></span>
                    复制新增
                </div>
            </div>
        </div>
    </section>
    <section class="states pad-top">
        <h2>两态以及各种组件</h2>
        <div class="states-section" style="width: 60%;">
            <hi-section-head model="data.section_head" options="local.section_head"></hi-section-head>
            <div class="section-body" ng-class="local.section_head.getClass()" ng-show="local.section_head.is_edit">
                <form name="test_form">
                    <div class="section-subtitle">随便填写一些</div>
                    <div class="section-subbody">
                        <table class="forms-table">
                            <tr>
                                <th><label for="test_input">中文</label></th>
                                <td><input id="test_input" type="text" class="form-control" ng-model="data.input.test_input"
                                           ng-required="true" /></td>
                            </tr>
                            <tr>
                                <th><label for="test_input2">EN</label></th>
                                <td><input id="test_input2" type="text" class="form-control text-center"
                                           ng-model="data.input.test_input2" /></td>
                            </tr>
                        </table>
                    </div>
                    <div class="section-subtitle">随便选一个看看吧</div>
                    <div class="section-subbody">
                        {{ local.radio_switch.value | json }}
                        <div hi-radio-switch options="local.radio_switch.options" model="local.radio_switch.value"></div>
                    </div>
                    <div class="section-subtitle">随便选一个带注释的</div>
                    <div class="section-subbody">
                        {{ local.radio_switch_1.value | json }}
                        <style>
                            .radio-switch.with-notice .show-label {
                                width: 30%; /*这个你自己定*/
                            }
                        </style>
                        <div hi-radio-switch options="local.radio_switch_1.options"
                             model="local.radio_switch_1.value"></div>
                    </div>
                    <div class="section-subtitle">新增一个什么东西吧
                        <button class="btn btn-inverse block-action add" ng-click="addBlock()">新增</button>
                    </div>
                    <div class="section-subbody">
                        <div class="one-block" ng-repeat="block in data.blocks">
                            <div class="delete-block" ng-click="delBlock( $index )">
                                <span class="i i-close"></span>
                            </div>
                        </div>
                    </div>
                    <div class="section-subtitle">关联一个什么东西吧</div>
                    <div class="section-subbody">
                        <div hi-input-tag options="local.input_tag" model="data.input_tag.all_tags"></div>
                    </div>
                    <div class="section-subtitle">Tag选择</div>
                    <div class="section-subbody">
                        <div class="tags-select-container">
                            <div class="selected-tags-wrapper">
                                <h2>我是一个大标题</h2>
                                <div class="selected-tags">
                                    <div class="one-tag selected" ng-repeat="tag in data.tags_select.select_tags"
                                         ng-bind="tag"></div>
                                </div>
                            </div>
                            <div class="all-tags-wrapper">
                                <div ng-repeat="set in data.tags_select.all_tag_sets">
                                    <h3 ng-bind="set.title"></h3>
                                    <div class="one-set-tags">
                                        <div class="one-tag" ng-repeat="tag in set.tags"
                                             ng-class="{ 'selected': tag.indexOf('i') > -1 }" ng-bind="tag"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="section-body" ng-class="local.section_head.getClass()" ng-hide="local.section_head.is_edit">
                <div class="row" ng-repeat="record in data.section_body.view_rows">
                    <div class="view-title" ng-bind="record.title"></div>
                    <div class="view-body-container">
                        <div class="view-body" ng-bind="record.body"></div>
                        <div class="view-row" ng-repeat="row in record.items">
                            <div class="view-title" ng-bind="row.title"></div>
                            <div class="view-body" ng-bind="row.body"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>