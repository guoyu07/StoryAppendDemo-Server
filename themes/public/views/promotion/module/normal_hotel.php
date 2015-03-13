<div class="item-wrap" <? if($index%2 == 0) {?>style="background-color: #ffffff;" <?} else {?>style="background-color: #f7f7f7;"<?}?> >
    <a class="display-row" href="<?= $hotel['link_url'] ?>" target="_blank">
        <div class="head-block">
            <img src="<?= $hotel['cover_image']['image_url'] ?>?imageView2/5/w/337/h/194">
            <div class="show-price">￥<?= $hotel['show_prices']['price']?> 起</div>
        </div>
        <div class="hotel-name"><?= $hotel['description']['name'] ?></div>
        <div class="hotel-level">
            <span class="level-text">酒店星级</span>
            <div class="star-back icon-rating-star" style="width: <?= $hotel['hotel']['star_level'] * 15 - 1 ?>px;"></div>
        </div>
        <div class="hotel-highlight">
            <?= $hotel['description']['benefit'] ?>
        </div>
        <div class="hotel-location">
            <? if($hotel['hotel']['location']) {?>
            <span class="icon-location"></span>
            <span class="location-name"><?= $hotel['hotel']['location']?></span>
            <?}?>
        </div>
        <div class="cover-block">
            <div class="cover-text">为什么选择它?</div>
            <ul class="cover-summary">
                <? foreach($hotel['description']['summary'] as $summary) {?>
                    <li><span><?= $summary ?></span></li>
                <?}?>
            </ul>
            <div class="cover-look">查看详情</div>
        </div>
    </a>
</div>