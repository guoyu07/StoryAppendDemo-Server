<header id="hi-header" ms-controller="header">
    <div class="main-header clearfix">
        <div class="logo-navigator">
            <a href="<?= $this->createUrl('home/index'); ?>" class="icon-slogan"></a>
            <div class="navigate-ctn">
                <div class="btn-dropdown">
                    <?php if(empty($this->header_info)) {
                        $header_info = '去哪儿';
                    } else if(empty($this->header_info['city'])) {
                        $header_info = $this->header_info['country']['cn_name'];
                    } else {
                        $header_info = $this->header_info['country']['cn_name'] . ' — ' . $this->header_info['city']['cn_name'];
                    } ?>
                    <span class="btn-srh" id="header_info"><?= $header_info; ?></span>
                    <span class="toggle-drop-icon"></span>
                </div>
                <ul class="navigate-overlay">
                    <?php foreach($this->navigator_data as $continent ) {
                        $submenu_id = 'submenu-' . $continent['continent_id']; ?>
                        <li class="sub-menu" data-submenu-id="<?= $submenu_id; ?>">
                            <div class="nav-menu">
                                <h3><?= $continent['cn_name'] . ' ' . $continent['en_name']; ?></h3>
                                <?php foreach($continent['countries'] as $country) {
                                    if($country['is_hot'] == 1) { ?>
                                        <h4><a href="<?= $country['link_url']; ?>"><?= $country['cn_name']; ?></a></h4>
                                    <?php } } ?>
                                <span class="enter-symbol icon-arrow-right"></span>
                            </div>
                            <div class="nav-popover" id="<?= $submenu_id; ?>">
                                <h3><?= $continent['cn_name'] . ' ' . $continent['en_name']; ?></h3>
                                <?php foreach($continent['countries'] as $country) { ?>
                                <div class="country-ctn">
                                    <h4><a href="<?= $country['link_url']; ?>"><?= $country['cn_name']; ?></a></h4>
                                    <div class="cities-ctn">
                                        <?php foreach($country['city_groups'] as $cities) {
                                            if($country['country_code'] == 'US' || $country['country_code'] == 'NZ' ) { ?>
                                                <div>
                                                    <h5><?= $cities['name']; ?></h5>
                                                    <?php foreach($cities['cities'] as $city) { ?>
                                                    <span><a href="<?= $city['link_url']; ?>"><?= $city['cn_name']; ?></a></span>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <?php foreach($cities['cities'] as $city) { ?>
                                                    <span><a href="<?= $city['link_url']; ?>"><?= $city['cn_name']; ?></a></span>
                                                <?php } ?>
                                            <?php } } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                <div class="clear-shadow"></div>
            </div>
        </div>
        <div class="account-panel" id="account_panel" data-is-logged="<?= Yii::app()->customer->isCustomerLogged() ?>" ms-controller="accountPanel">
            <div ms-if="!isLogged" class="access-ctn">
                <a id="login" ms-click="buildOverlay('login')">登录</a>
                <a id="register" ms-click="buildOverlay('register')">注册</a>
            </div>
            <div ms-if="isLogged" class="account-ctn clearfix" ms-visible="isLogged" style="display:none;">
                <div class="account-menu">
                    <a class="my-account">我的</a>
                    <ul class="action-list">
                        <li><a href="<?= $this->request_urls['headerAccount'] ?>" ref="nofollow">我的账户</a></li>
                        <li><a href="<?= $this->request_urls['headerCoupon'] ?>" ref="nofollow">我的优惠券</a></li>
                        <li><a ref="nofollow" class="logout" ms-click="logout()">退出</a></li>
                    </ul>
                </div>
                <a href="<?= $this->request_urls['headerOrders']; ?>" ref="nofollow" class="my-order">
                    我的订单
                </a>
                <a href="<?= $this->request_urls['headerFavorite']; ?>" ref="nofollow" class="my-favorite">
                    我的收藏
                </a>
            </div>
        </div>
    </div>
</header>
