<div class="special-tag">
    <img class="fixed-bg" ms-src="{{ product.activity_info.tag_url }}" ms-visible="product.activity_info && product.activity_info.length != 0 && product.activity_info.tag_url" />
    <div class="custom" ms-visible="(!product.activity_info || product.activity_info.length == 0) && product.show_prices.special_info">
        {{ product.show_prices.special_info.reseller }}{{ product.show_prices.special_info.slogan }}
        {{ (1 - product.show_prices.price / product.show_prices.orig_price) * 100 | number(0) }}% OFF
    </div>
</div>