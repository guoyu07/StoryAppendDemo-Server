<div class="order-ctn" ms-controller="order">
  <section class="show-price-ctn">
    <span class="show-price"><em>{{show_prices.price}}</em>起</span>
    <span class="market-price"><em>{{show_prices.orig_price}}</em></span>
    <span id="quantifier">每成人</span>
    <span class="market-label">门市价</span>
  </section>
  <section  ms-controller="tourDate" ms-visible="flag==1">
    <div ms-class="section-head icon-calendar {{status}}">
      出行日期
    </div>
    <div id="tour_date"></div>
    <div class="section-preview" ms-visible="status=='collapse'" ms-click="openDatePicker">
      {{selectedDate}}
    </div>
    <div class="date-legend" ms-visible="status!='collapse'" >
      <div style="margin-bottom: 10px;"><div class="date-enable"></div>可选使用日期</div>
      <div><div class="date-disable"></div>关闭时间</div>
    </div>
  </section>


  <section class="special-code" ms-controller="specialCode" ms-visible="flag==1">
    <div ms-class="section-head icon-three-bar {{status}}" >
      套餐选择
    </div>
    <div class="w-selector" ms-visible="status!='collapse'">
      <div class="w-option" ms-attr-title="{{el.cn_name}}"   ms-click="selectOption($event,$index,'special_codes')" ms-mouseover="onHover" ms-mouseout="onLeave" ms-attr-data-index="{{$index}}" ms-repeat-el="special_codes">
        {{el.cn_name}}
      </div>

    </div>
    <div class="w-item-tips" id="special_tips" ms-controller="specialTips" ms-attr-style="top:{{top}}px;display:{{visible}}" >
      <div class="tips-content" ms-if="description!=''"><h3>{{name}}</h3><p>{{description}}</p></div>
      <div class="price-ctn" >
        <div ms-repeat-el="specialPrices">{{el.ticketType}}<div class="price-wrap"><em>{{el.price}}</em><strong>{{el.marketPrice}}</strong></div></div>
      </div>
      <div class="tips-arrow"></div>
    </div>
    <div class="section-preview" ms-visible="status=='collapse'" ms-click="openSection('specialCode')">
      <div class="selected-special">{{selectedSpecial}}</div>
      <div class="special-price">
        <div ms-repeat-el="specialPrices">{{el.ticketType}} ￥{{el.price}}</div>
      </div>
    </div>
  </section>
  <section class="departure" ms-controller="departure" ms-controller="departure" ms-visible="flag==1">
    <div ms-class="section-head icon-three-bar {{status}}" >
      接送地点
    </div>
    <div class="w-selector" ms-visible="status!='collapse'">
      <div class="w-option"  ms-click="selectOption($event,$index,'')" ms-attr-data-index="{{$index}}" ms-repeat-el="departures"  ms-attr-title="{{el.showTime}} {{el.departure_point}}">
        {{el.showTime}} {{el.departure_point}}
      </div>

    </div>
    <div class="section-preview" ms-visible="status=='collapse'" ms-click="openSection('departure')">
      <div class="selected-departure">{{selectedDeparture}}</div>

    </div>
  </section>

  <section class="quantity " ms-controller="ticketType" ms-visible="flag==1">
  <div ms-class="section-head icon-person {{status}}" ms-click="close">人数选择</div>
    <div class="ticket-ctn" ms-repeat-el="ticketTypes" ms-visible="status!='collapse'">
      <div class="ticket-type">{{el.name}}</div>
      <div class="ticket-range" ms-if="el.age_range">（{{el.age_range}}）</div>
      <div class="ticket-price"><em>{{el.price}}</em><strong>{{el.orig_price}}</strong></div>
      <div class="num-counter">
        <span class="reduce" ms-click="counterReduce($index)"></span>
        <input type="text" ms-attr-value="{{el.quantity}}" readonly="true"/>
        <span class="add" ms-click="counterAdd($index)"></span>
      </div>
    </div>
    <div class="ticket-rule icon-exclamation-circle" ms-class="ticket-tips-show:ticketTips" >{{ticketTips}}</div>
    <div class="section-preview" ms-click="open" ms-visible="status=='collapse'">
      <div class="ticket-wrap" ms-repeat-el="ticketTypes">{{el.name}}&nbsp;&nbsp;{{el.quantity}}</div>
    </div>
  </section>
  <section class="sum-price" ms-controller="sumPrice" ms-visible="flag==1">
    <div>总价</div>
    <strong>{{sumPrice}}</strong>
    <input class="buy-btn" ms-class="processing:inCheckout=='下单中...'" type="button" ms-visible="toBuy" ms-click="checkout" ms-value="{{inCheckout}}"/>
  </section>
  <section class="discount-ctn" ms-controller="sumPrice" ms-visible="flag==1">
  <div class="discount-wrap">
    <h4>折扣 <span>（相比门市价）</span></h4>
    <strong>{{discount}}%</strong>
  </div>
    <div class="save-wrap">
      <h4>为您节省</h4>
      <strong>{{reduce}}</strong>
    </div>
    <div class="top-arrow"></div>
  </section>

</div>




<div class="contact-us">

</div>
<section class="guarantee-ctn">
  <h2 class="icon-hitour">玩途保障</h2>

  <div class="guarantee-wrap">
    <h3>退款保障</h3>

    <p>在规定时间内未兑换的玩途通票，皆可享受
      无理由全额退款，最大程度上保证你旅行安
      排的灵活性。</p>
  </div>
  <div class="guarantee-wrap">
    <h3>安心保障</h3>

    <p>我们向每位订购通票的客户赠送7天15万保
      额太平洋境外旅行意外险。为你的平安归来
      而全心守望</p>
  </div>
  <div class="guarantee-wrap">
    <h3>双倍赔偿</h3>

    <p>如你在目的地无法兑换相应服务，我们将按
      未兑换部分消费金额的双倍向你赔偿.</p>
  </div>
</section>
<section class="subject-ctn" ms-controller="subject">
  <div class="section-title">
    <div class="title-block"></div><h3>精彩专题</h3>
  </div>
  <a class="subject-wrap" ms-attr-href="{{link_url}}#{{el.group_id}}" target="_blank" ms-repeat-el="subjects" ms-attr-style="background:url({{el.cover_image_url}}) no-repeat">
    <div class="subject-product-title">{{el.name}}</div>
    <span class="subject-product-count">{{subjects.length}}个商品</span>
    <div class="subject-mask"></div>
  </a>
</section>

<section class="related-products-ctn" ms-controller="subject">
  <div class="section-title">
    <div class="title-block"></div><h3>精彩商品</h3>
  </div>
  <a class="related-product" ms-attr-href="{{el.link_url}}" target="_blank" ms-repeat-el="related" ms-attr-style="background:url({{el.image_url}}) no-repeat">
    <div class="related-product-info">
      <div class="rp-name">{{el.name}}</div>
      <div class="rp-price i-rmb">{{el.show_prices.price}}</div>
    </div>
  </a>

</section>