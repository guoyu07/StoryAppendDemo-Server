<div class="item-wrap">
    <a class="product-image-row" ms-href="product.detail.link_url" target="_blank">
        <img ms-src="{{ product.detail.cover_image }}?imageView2/5/w/243/h/200">
    </a>
    <div class="product-intro-row">
        <h3 class="product-name"><a ms-href="product.detail.link_url" target="_blank">{{ product.detail.description.name }}</a></h3>
        <div class="tour-price price-font">
            <div class="orig-price">
                &yen;<em>{{ product.detail.price.orig_price }}</em>
            </div>
            <div class="now-price">
                &yen;<em>{{ product.detail.price.price }}</em>/{{ product.detail.price.title }}
            </div>
        </div>
    </div>
</div>