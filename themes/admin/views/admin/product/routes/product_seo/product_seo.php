<script type="text/ng-template" id="ProductSeo.html">
    <div class="states-section grid-top">
        <hi-section-head options="local.section_head.seo"></hi-section-head>
        <!--编辑态-->
        <div class="section-body clearfix" ng-class="local.section_head.seo.getClass()" ng-show="local.section_head.seo.is_edit">
            <form name="seo_form" hi-watch-dirty="local.path_name">
                <table class="forms-table seo-table">
                    <tr>
                        <td class="col-md-4">
                            <label for="product_title">商品SEO标题</label>
                        </td>
                        <td>
                            <input id="product_title" type="text" class="form-control" ng-model="data.seo.title" required />
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-4">
                            <label for="product_description">商品SEO描述</label>
                        </td>
                        <td>
                            <input id="product_description" type="text" class="form-control" ng-model="data.seo.description" required />
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-4">
                            <label for="product_keywords">SEO关键词</label>
                        </td>
                        <td>
                            <input id="product_keywords" type="text" class="form-control" ng-model="data.seo.keywords" required />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <!--展示态-->
        <div class="section-body clearfix" ng-class="local.section_head.seo.getClass()"
             ng-hide="local.section_head.seo.is_edit">
            <table class="forms-table seo-table">
                <tr>
                    <td class="view-title col-md-4">商品SEO标题</td>
                    <td class="view-body" ng-bind="data.seo.title"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">商品SEO描述</td>
                    <td class="view-body" ng-bind="data.seo.description"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">SEO关键词</td>
                    <td class="view-body">
                        <span class="pad-right" ng-repeat="keyword in local.keywords" ng-bind="keyword"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</script>