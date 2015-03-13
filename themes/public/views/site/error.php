<div id="ad_image" class="container set-bg" style="background-image:url( <?= $image_url; ?> );">

  <div class="notification">
    <p class="notification-title">
      <?= $error_slogan; ?>
    </p>
    <p class="link-zone">
      <a href="<?= $this->request_urls['home']; ?>">返回首页 ></a><br />
      <a href="<?= $this->request_urls['target']; ?>">查看<?= $target_name; ?>的其他商品 ></a>
    </p>
  </div>
  <div class="gradient"></div>
  <div class="ad-zone">
    <label class="ad-title"><a href="<?= $this->request_urls['product']; ?>"><?= $target_product['name']; ?></a></label>
    <label class="ad-location"><i class="icon-location"></i><?= $target_product['location']; ?></label>
    <p id="ad_content"><?= $target_product['description']; ?></p>
  </div>

</div>