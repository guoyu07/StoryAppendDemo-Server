<script type="text/ng-template" id="editProductDesc.html">
    <form name="product_desc_form" novalidate>
        <div class="edit-section clearfix">
            <sidebar name='editProductDesc'></sidebar>
            <section class="col-xs-13 col-xs-offset-1 section-action">
                <div class="row edit-heading">
                    <h2>简要描述</h2>
                </div>
                <div class="row edit-body">
                    <textarea class="input-area area editor" data-ng-model="desc.cn_summary"
                              required>{{desc.cn_summary}}</textarea>
                </div>
                <div class="row edit-heading">
                    <h2>详细介绍</h2>
                </div>
                <div class="row edit-body">
                    <textarea class="input-area area editor" data-ng-model="desc.cn_description"
                              required>{{desc.cn_description}}</textarea>
                </div>
                <div class="row edit-heading">
                    <h2>商品卖点</h2>
                </div>
                <div class="row edit-body">
                    <input type="text" class="form-control" maxlength="10" placeholder="最多10个字"
                           data-ng-model="desc.cn_benefit">
                </div>
            </section>
        </div>

        <div class="edit-section clearfix">
            <sidebar name='editPackageDesc'></sidebar>
            <section class="col-xs-13 col-xs-offset-1 section-action">
                <div ng-if="local.is_package || (!local.is_package && (local.is_combo || local.sale_in_package))">
                    <div class="row edit-heading">
                        <h2>套餐包含</h2>
                    </div>
                    <div class="row edit-body">
                        <textarea class="input-area area editor" data-ng-model="desc.cn_package_service">{{desc.cn_package_service}}</textarea>
                    </div>
                </div>
                <div ng-if="!local.is_package && !local.is_combo && !local.sale_in_package">
                    <div class="row edit-heading">
                        <h2>服务包含简介</h2>
                    </div>
                    <div class="row edit-body">
                        <textarea class="input-area area editor" data-ng-model="desc.cn_package_gift">{{desc.cn_package_gift}}</textarea>
                    </div>
                </div>
                <div ng-if="local.is_package">
                    <div class="row edit-heading">
                        <h2>独享特惠</h2>
                    </div>
                    <div class="row edit-body">
                        <textarea class="input-area area editor" data-ng-model="desc.cn_package_recommend">{{desc.cn_package_recommend}}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="edit-section clearfix last">
            <section class="col-xs-18 section-action gutter-padding">
                <div class="row edit-heading">
                    <h2>包含服务</h2>
                </div>
                <div class="row edit-body">
                    <markdown input="desc.cn_service_include.md_text" output="desc.cn_service_include.md_html"></markdown>
                </div>
                <div class="row edit-heading">

                    <h2>兑换及使用&nbsp;&nbsp;<a data-ng-href="{{ desc.product_detail }}" target="_blank">新版</a> </h2>
                </div>
                <div class="row edit-body">
                    <markdown input="desc.cn_how_it_works.md_text" output="desc.cn_how_it_works.md_html"
                              required="true"></markdown>
                </div>
            </section>
        </div>
    </form>
    <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
            data-ng-click="submitChanges()">
        保存
    </button>
</script>