<header class="main-header pr" ms-controller="header">
  <!--Navigation-->
    
  <div class="container header-navbar clearfix pr">
    <div class="nav-left">
      <a href="<?= $this->createUrl('home/index'); ?>" class="icon-slogan"></a>
      <!--ms-class="active: show_dropdown"-->
      <div class="pr header-btn-group clearfix">
        <div class="btn-dropdown" id="btn_dropdown">
          <div class="keeper" ms-visible="is_default_header">
            <button class="btn-srh">去哪儿玩</button>
            <span class="btn-toggle"></span>
          </div>
          <div class="keeper" ms-visible="!is_default_header && !current_city.cn_name">
            <button class="btn-srh" ms-text="header_display.country "></button>
            <span class="btn-toggle"></span>
          </div>
          <div class="keeper" ms-visible="!is_default_header && current_city.cn_name">
            <button class="btn-srh" ms-text="header_display.country"></button>
            <div class="separator"></div>
            <button class="btn-srh" ms-text="header_display.city"></button>
            <span class="btn-toggle"></span>
          </div>
          <span class="hidden-shadow"></span>
        </div>

        <div class="dropdown-list-container clearfix" id="dropdown_list">
          <div class="countries-list">
            <div class="one-continent one-list" ms-repeat-continent="continents" ms-data-continentid="continent.continent_id">
              <h3 class="list-name">{{ continent.cn_name }}</h3>
              <ul class="list-content clearfix">
                <li class="list-item" ms-repeat-country="continent.countries"
                    ms-class="active: current_country.country_code == country.country_code">
                  <a class="one-country" ms-mouseout="onDropListOut" ms-mouseover="onDropListHover(continent.continent_id, $index);" ms-href="{{ country.link_url }}" ms-data-countrycode="country.country_code" ms-data-continentid="continent.continent_id">{{ country.cn_name }}</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="cities-list">
            <a ms-href="{{ current_country.link_url }}" title="查看国家主页" class="one-country list-name">{{ current_country.cn_name }}</a>
            <div class="one-city-group one-list clearfix" ms-repeat-group="current_city_groups">
              <h3 class="list-name" ms-visible="showGroup( group.name )">{{ group.name }}</h3>
              <ul class="list-content clearfix" ms-class-1="part-row: showGroup( group.name )">
                <li class="list-item" ms-repeat-city="group.cities" ms-class="active: current_city.city_code == city.city_code">
                  <a class="one-city" ms-href="{{city.link_url}}">{{ city.cn_name }}</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

      <div class="nav-right" id="account_panel" data-is-logged="<?= Yii::app()->customer->isCustomerLogged() ?>"
           ms-controller="accountPanel">
          <div ms-if="!isLogged">
              <a class="navbar-link interval" ms-click="buildOverlay( 'login' )">登录</a>
              <a class="navbar-link" ms-click="buildOverlay( 'register' )">注册</a>
          </div>
          <div ms-if="isLogged">
              <div class="logged-in" ms-class="active: show_account == true">
                  <a class="navbar-link-withIcon" ms-click="toggleAccount">
                      <i class="icon-empty-person"></i><br>
                      <span class="username">我的</span>
                      <span class="btn-toggle"></span>
                  </a>
                  <a class="navbar-link-withIcon border-left" ms-href="{{ url_orders }}">
                      <i class="icon-file"></i><br>
                      <span>我的订单</span>
                  </a>
                  <a class="navbar-link-withIcon border-left" ms-href="{{ url_favorite }}">
                      <i class="icon-heart-empty"></i><br>
                      <span>我的收藏</span>
                  </a>
                  <ul class="action-list">
                      <li class="one-action-li">
                          <a class="one-action-a" ms-href="{{ url_account }}">我的账户</a>
                      </li>
                      <li class="one-action-li">
                          <a class="one-action-a" ms-href="{{ url_coupon }}">我的优惠券</a>
                      </li>
                      <!--<li class="one-action-li" style="display: none;">
                          <a class="one-action-a" ms-href="{{ url_fund }}">旅游基金
                              <div class="notice-point"><span>2</span></div>
                          </a>
                      </li>-->
                      <li class="one-action-li">
                          <a class="one-action-a color-red" ms-click="logout">登出</a>
                      </li>
                  </ul>
              </div>
          </div>
      </div>
  </div>

  <!--Dropdown-->
  <div class="container header-dropdown pr">
    <!--ms-visible="show_dropdown"-->

  </div>
</header>