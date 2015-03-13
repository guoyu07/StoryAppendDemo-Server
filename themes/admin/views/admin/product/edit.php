<script type="text/javascript">
    var request_urls = JSON.parse('<?php echo !empty($request_urls) ? json_encode($request_urls) : json_encode(array()); ?>');
</script>
<div data-ng-app="ProductEditApp" data-ng-controller="ProductEditCtrl" page-init>
    <div class="row no-space" id="breadcrumbs">
        <a href="<?= $request_urls['back'] ?>" class="col-xs-1 fui-arrow-left"></a>
        <a href="<?= $request_urls['back'] ?>;city_code:{{ product_detail.city_code }}" class="col-xs-2">
            {{product_detail.city_cn_name}}
        </a>
        <a href="<?= Yii::app()->params['urlPreview'] ?>{{ product_detail.product_id }}" class="product-name"
           target="_blank">
            <span class="input-icon fui-eye"></span>{{ product_detail.product_id }} -- {{ product_detail.name }}
        </a>
        <a href="{{ new_version_url }}" class="product-name" target="_blank">
            新版编辑
        </a>
        <span style="color:#fff;margin-left: 128px;"></span>
        <a href="<?= Yii::app()->params['urlPreviewOnTest'] ?>{{ product_detail.product_id }}" class="product-name"
           target="_blank">
            <span class="input-icon fui-eye"></span>在Test上预览</a>
        <a ng-href="<?= Yii::app()->params['WEB_PREFIX'] ?>admin/city/edit/city_code/{{ product_detail.city_code }}"
           class="product-name"
           target="_blank">
            <span class="input-icon fui-eye"></span>城市运营</a>
        <button class="top-bar-btn" ng-click="copyProduct()">+复制新增</button>
    </div>
    <div class="row no-space fixed-container" id="product-edit-container">
        <nav class="col-xs-4 list-group" id="edit-menu" data-ng-class="{ 'has-combo': product_detail.is_combo == '1' }">
            <div class="list-group-item">
                <h4 class="list-group-item-heading">
                    商品状态设置
                </h4>
            </div>
            <div class="list-group-item">
                <div class="dropdown product-status">
                    <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        {{ editing_state_name }}
                        <span class="caret"></span>
                    </button>
                    <span class="dropdown-arrow"></span>
                    <ul class="dropdown-menu">
                        <li data-ng-repeat="status in product_status" class="ng-scope">
                            <a class="status {{status.class}}" data-ng-click="changeProductStatus(status.status_id)"
                               data-ng-hide="status.status_id == product_detail.status">{{status.status_name}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="separator"></div>
            <div class="list-group-item" data-ng-repeat="value in menu"
                 data-ng-class="{ active: value.id == current_menu }">
                <h4 data-ng-if="value.group == true && value.display" class="list-group-item-heading">
                    {{value.label}}
                </h4>
                <a href="#/{{value.id}}" data-ng-if="value.group == false && value.display" class="list-group-item-text"
                   id="{{value.id}}">
                    <span class="badge" data-ng-class="value.status"></span>
                    {{value.label}}
                </a>
            </div>
        </nav>
        <section class="col-xs-14" id="edit-area">
            <alert data-ng-show="alerts.length > 0" type="alerts[0].type" close="delAlert()">
                {{alerts[0].data}}
            </alert>
            <div data-ng-view></div>
        </section>
    </div>

    <?php $path = __DIR__ . '/edit_templates/'; ?>
    <?php include_once $path . "directives.php" ?>

    <?php include_once $path . "info.php" ?>
    <?php include_once $path . "rules.php" ?>
    <?php include_once $path . "price.php" ?>

    <?php include_once $path . "passenger.php" ?>

    <?php include_once $path . "voucher.php" ?>
    <?php include_once $path . "shipping.php" ?>

    <?php include_once $path . "seo.php" ?>
    <?php include_once $path . "description.php" ?>
    <?php include_once $path . "tourPlan.php" ?>
    <?php include_once $path . "image.php" ?>
    <?php include_once $path . "album.php" ?>
    <?php include_once $path . "related.php" ?>
    <?php include_once $path . "qna.php" ?>
    <?php include_once $path . "comment.php" ?>
    <?php include_once $path . "coupon.php" ?>
    <?php include_once $path . "TripPlan.php" ?>
    <?php include_once $path . "hotel.php" ?>
    <?php include_once $path . "bundle.php" ?>
    <?php include_once $path . "hotelRoom.php" ?>
</div>
