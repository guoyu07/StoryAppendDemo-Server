<div class="page column">
    <div class="head-image"
         style="background: #000 url(<?= $article['head_image_url'] ?>?imageView/5/w/1400/h/330) center center no-repeat; background-size: 100%;">
        <div class="column-title-ctn">
            <div class="column-title clearfix">
                <span class="image"></span>
                <i class="vertical-bar"></i>
                <span class="title"><?= $article['title'] ?></span>
            </div>
        </div>
        <div class="nav-bar-ctn">
            <div class="nav-bar">
                <a href="<?= $this->createUrl('home/index'); ?>">首页</a>
                <span class="seperator"></span>
                <a href="<?= $article['city']['link_url'] ?>"><?= $article['city']['cn_name'] ?></a>
            </div>
        </div>
    </div>

    <div class="main-content clearfix">
        <div class="left-content">
            <div class="left-section">
                <p class="main-desc"><?= $article['brief'] ?></p>
                <?php
                foreach ($article['sections'] as $section) {
                    ?>
                    <div class="article">
                        <h2><i class="title-box"></i><?= $section['section_title'] ?></h2>
                        <?php
                        foreach ($section['items'] as $item) {
                            if ($item['type'] == 1) {
                                echo '<p class="desc">' . $item['text_content'] . '</p>';
                            } else if ($item['type'] == 2) {
                                echo '<img src="' . $item['image_url'] . '?imageView2/2/w/650" alt=""/>';
                                if (!empty($item['image_title'])) echo '<h3>' . $item['image_title'] . '</h3>';
                            } else if ($item['type'] == 3) {
                                ?>
                                <div class="product-wrap">
                                    <div class="product-name"><?= $item['product_title'] ?></div>
                                    <div class="product-box" onclick="openProductDetail(<?= $item['product_detail']['product_id'] . ',' . $item['product_detail']['type'] . ",'" . str_replace('000', $item['product_detail']['product_id'], $product_link) . "'"; ?>)">
                                        <img src="<?= $item['product_detail']['cover_image']['image_url'] ?>?imageView2/5/w/130" alt="<?= $item['product_detail']['description']['name']; ?>" />
                                        <div class="product-content">
                                            <div class="product-title">
                                                <?= $item['product_detail']['description']['name'] ?>
                                                <em>
                                                    <i>&yen;</i>
                                                    <?= $item['product_detail']['show_prices']['price'] ?>
                                                </em>
                                            </div>
                                            <span class="learn-more">了解更多></span>
                                        </div>
                                    </div>
                                    <div class="product-desc">
                                        <div class="vertical-bar"></div>
                                        <?= $item['product_description'] ?>
                                    </div>
                                </div>
                            <?php
                            } else if ($item['type'] == 4) {
                                if (!empty($item['text_content'])) {
                                    echo '<p class="img-desc">' . $item['text_content'] . '</p>';
                                }
                            }
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="the-end">
                <hr/>
                <span class="end-image"></span>
                <hr/>
            </div>
        </div>
        <div class="right-cover">
            <div class="right-content">
                <?php if(!empty($article['product_all'])) { ?>
                    <div class="right-title">文章提及的体验</div>
                    <div class="right-list">
                        <?php foreach ($article['product_all'] as $product) { ?>
                            <a href="<?= $product['link_url'] ?>#from=right">
                                <div class="right-card clearfix">
                                    <div class="right-picture">
                                        <img src="<?= $product['cover_image']['image_url'] ?>?imageView2/5/w/66/h/66" />
                                    </div>
                                    <div class="right-des">
                                        <?= $product['description']['name'] ?>
                                    </div>
                                    <div class="right-price">
                                        <span class="price-wrap">￥<?= $product['show_prices']['price'] ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>
    <?php if (!empty($article['other_articles']['data'])) { ?>
    <div class="footer-content">
        <div class="aside clearfix">
            <div class="aside-title">
                更多玩法
            </div>
            <div class="aside-container">
                <?php foreach ($article['other_articles']['data'] as $k => $a) { ?>
                    <a class="column" href="<?= $a['link_url']; ?>"
                       style="background: url(<?= !empty($a['group_cover_image']) ? $a['group_cover_image'] : $a['head_image_url']; ?>?imageView2/1/w/230/h/274) top center no-repeat; background-size: 100% 100%;">
                        <div class="back-shadow clearfix">
                            <span class="column-name"><?= $a['title']; ?></span>
                            <span class="column-num"><?= $a['product_count']; ?>种体验 ></span>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <!--  绑定商品 详情弹窗  -->
    <?php include(dirname(__FILE__) . '/../module/bundle/bundle.html'); ?>
</div>
<script>
    var isIE9andLess = document.all && !window.atob;
    function openProductDetail( parentId, product_type, url ) {
        if( isIE9andLess ) {
            window.location.href = url;
        } else {
            bundleModel.showBundleModal(parentId, product_type);
        }
    }
    $(window).ready(function(e) {
    });
</script>