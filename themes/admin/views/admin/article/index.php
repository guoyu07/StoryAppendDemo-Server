<div id="article-grid-container" ng-controller="ArticleGridCtrl" class="container page-container">
    <div class="row" style="margin-bottom: 40px;">
        <div class="col-md-14">
            <div class="col-md-5">
                <h3 class="text-center" style="margin: 0;">
                    搜索文章 <span class="i i-refresh refresh-animate" ng-show="local.grid_options.in_progress"></span>
                </h3>
            </div>
            <div class="col-md-10">
                <div class="col-md-8">
                    <select
                        chosen
                        style="width: 100%;"
                        ng-model="local.search_city"
                        ng-options="city.city_code as (city.city_name + ' ' + city.city_pinyin) group by city.group for city in local.cities"
                        data-placeholder="点击选择城市"
                        no-results-text="'没有找到'"
                        ng-change="searchArticle()"
                        >
                    </select>
                </div>
                <div class="col-md-10">
                    <input type="text" class="form-control" ng-model="local.search_text" placeholder="搜索文章名称／代码"
                           hi-enter="searchArticle()" />
                </div>
            </div>
            <div class="col-md-3">
                <button class="block-action btn btn-inverse" ng-click="searchArticle()">搜索</button>
            </div>
        </div>
        <div class="col-md-2 col-md-offset-2">
            <button class="btn btn-inverse pull-right" ng-click="goToArticle()">添加文章</button>
        </div>
    </div>

    <hi-grid options="local.grid_options"></hi-grid>
</div>