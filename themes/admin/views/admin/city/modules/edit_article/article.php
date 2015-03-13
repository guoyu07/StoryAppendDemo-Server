<div class="states-section" ng-show="local.current_menu == '6'">
    <div class="section-head">
        <h2 class="section-title">体验分组</h2>
    </div>
    <div class="section-body groups-edit-container other">
        <div class="one-block text-left col-md-6">
            <div class="item-list-container ungrouped">
                <ul class="item-list">
                    <li ng-repeat="article in local.articles">
                        <a class="item-name" target="_blank" ng-href="{{local.article_url + article.article_id}}"
                           ng-bind="(article.status == 1 ? ' ★' : '') + article.article_id + ' - ' + article.title"></a>
                        <span class="i i-enter"
                              ng-click="addArticleToColumn( 'experience', article.article_id )"></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-12 hi-grid">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-6 col-md-offset-5">
                    <input type="text" class="form-control" ng-model="data.columns.experience.name"
                           placeholder="输入分组名称" />
                </div>
                <div class="col-md-2">
                    <button class="block-action add btn btn-inverse" ng-click="updateColumnInfo( 'experience' )">
                        保存
                    </button>
                </div>
            </div>
            <div class="row grid-bottom">
                <div class="col-md-6 col-md-offset-5">
                    <input type="text" class="form-control" ng-model="local.search_text" placeholder="输入文章ID" />
                </div>
                <div class="col-md-1">
                    <button class="block-action add btn btn-inverse"
                            ng-click="addArticleToColumn( 'experience', local.search_text )">
                        添加
                    </button>
                </div>
                <p class="picture-desc">文章图片尺寸：web(375x200)，手机(600x296)，PAD(330x164)</p>
            </div>
            <table class="table table-striped" id="article_grid">
                <thead>
                    <tr>
                        <th style="width: 10%;">排序</th>
                        <th style="width: 8%;">文章ID</th>
                        <th style="width: 30%;">文章图片</th>
                        <th style="width: 40%;">文章名称</th>
                        <th style="width: 12%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="article in data.columns.experience.columns">
                        <td>
                            <input class="form-control"
                                   ng-value="{{$index + 1}}"
                                   ng-model="article.display_order"
                                   ng-blur="updateArticleOrder( 'experience', article.article_id )" />
                        </td>
                        <td>
                            <span ng-bind="article.article_id"></span>
                        </td>
                        <td>
                            <hi-uploader options="article.uploader"></hi-uploader>
                        </td>
                        <td>
                            <span ng-show="article.status == 1">★</span>
                            <a target="_blank" ng-href="{{local.article_url + article.article_id}}">
                                <span ng-bind="article.article.title"></span>
                            </a>
                        </td>
                        <td>
                            <button class="block-action add btn btn-inverse"
                                    ng-click="deleteArticleFromColumn( 'experience', $index )">
                                删除
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>