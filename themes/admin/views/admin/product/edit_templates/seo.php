<script type="text/ng-template" id="editProductSeo.html">
    <div class="edit-section last clearfix">
        <div class="col-md-13 col-md-offset-1">
            <form name="product_seo" novalidate>
                <div class="row edit-heading with-dot">
                    <h2>商品SEO</h2>
                </div>
                <div class="row edit-body">
                    <div class="row grid-bottom">
                        <label class="info-title col-md-5">
                            商品页面标题
                        </label>
                        <div class="col-md-13">
                            <input type="text" class="form-control" ng-model="data.seo.title" required />
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <label class="info-title col-md-5">
                            商品页面描述
                        </label>
                        <div class="col-md-13">
                            <input type="text" class="form-control" ng-model="data.seo.description" required />
                        </div>
                    </div>
                    <div class="row grid-bottom">
                        <label class="info-title col-md-5">
                            商品页面关键词
                        </label>
                        <div class="col-md-13">
                            <input type="text" class="form-control" ng-model="data.seo.keywords" required />
                        </div>
                    </div>
                </div>
            </form>
            <button class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form" data-ng-click="submitChanges()">
                保存
            </button>
        </div>
    </div>
</script>