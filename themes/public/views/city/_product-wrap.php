<div class="product-wrap clearfix">
    <div class="product-name-block">
        <h2 class="product-name-cnname">{{ product.description.name }}</h2>
    </div>
    <div class="product-img-block">
        <?php include(dirname(__FILE__) . '/_special-tag.php'); ?>
        <img ms-src="{{ product.cover_image.image_url }}?imageView/5/w/180/h/130">
    </div>
    <div class="product-service-block" ms-html="product.description.service_include"></div>
    <div class="product-price">
        <div class="hi-price">
            &yen;<em>{{ product.show_prices.price }}</em>&nbsp;&nbsp;起
        </div>
        <div class="orig-price">
            市场价 &yen;<em>{{ product.show_prices.orig_price }}</em>
        </div>
        <div class="sales-block">
            已售
            <span class="sales_volume">{{ product.sales_volume }}</span>
        </div>
    </div>
</div>