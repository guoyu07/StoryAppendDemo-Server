<div class="main-wrap" ms-controller="productInfo">
  <div class="head-title">{{mainProduct.name}}</div>
  <table class="product-table" >
    <thead>
    <tr>
      <th class="th-name">服务名称</th>
      <th>市场价</th>
      <th>独享价</th>
      <th>数量</th>
      <th>小计</th>
      <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td class="td-name">

        <div class="product-content">
          <h2>{{mainProduct.hotelName}}</h2>
          <div class="dib color-orange">入住信息：</div>
          <div class="hotel-info">{{hotelInfo|html}}</div>
        </div>
      </td>
      <td class="td-orice">&yen;{{mainProduct.origPrice}}</td>
      <td class="td-price">&yen;{{mainProduct.price}}</td>
      <td class="td-quantity">{{mainProduct.num}}间</td>
      <td class="td-subtotal">&yen;{{mainProduct.price}}</td>
      <td class="td-operation"></td>
    </tr>
    <tr class="selected-list" ms-repeat-el="selectedList" ms-class="editable:el.edit">
        <td class="td-name">
            <div class="product-content">
                <h2><span>{{el.description.name}}</span><span class="add-label"></span></h2>
            </div>
            <div class="product-detail" ms-class="vhidden:el.edit">{{el.tour_date}}&nbsp;&nbsp;{{el.special.cn_name}}&nbsp;&nbsp;{{el.departure.show||''}}</div>
            <div class="edit-panel" ms-visible="el.edit">
                <div class="color-orange">填写基本信息：</div>
                <div class="field-ctn" ms-if="el.date_rule.need_tour_date==1">
                    <div  class="tour-date" ms-class-1="error:el.error.date"  ms-class-2="ani-blink:el.error.date" ms-attr-data-index="$index">
                        <div class='date-text'>{{el.tour_date||'请选择您的出行日期'}}</div>
                        <i class="i-calendar"></i>
                        <i class="i-arrow-down"></i>
                    </div>
                </div>
                <div class="field-ctn" ms-if="el.specialCodes.length>0">
                    <div class="select-holder">
                        <div class="select" ms-class-1="error:el.error.special" ms-class-2="ani-blink:el.error.special" ms-click="showList(el,'show_special',$event)">
                            {{el.special.cn_name||el.description.special_title}}
                            <i class="i-arrow-down"></i>
                        </div>
                        <div class="select-box" ms-visible="el.show_special">
                            <div class="option" ms-repeat-sp="el.specialCodes" ms-click="selectSpecial(el,sp,$event)">&yen;{{sp.price.price}} {{sp.cn_name}}</div>
                        </div>

                    </div>
                </div>
                <div class="field-ctn" ms-if="el.departures.length>0">
                    <div class="select-holder">
                        <div class="select" ms-class-1="error:el.error.departure" ms-class-2="ani-blink:el.error.departure" ms-click="showList(el,'show_departure',$event)">
                            {{el.departure.show||el.description.departure_title}}
                            <i class="i-arrow-down"></i>
                        </div>
                        <div class="select-box" ms-visible="el.show_departure">
                            <div class="option" ms-repeat-dp="el.departures" ms-click="selectDeparture(el,dp,$event)">{{dp.showTime}}{{dp.departure_point}}</div>
                        </div>

                    </div>
                </div>
                <div class="confirm-btn" ms-click="confirmBundle(el)">确定</div>
            </div>
        </td>
        <td class="td-oprice" ><div  ms-repeat="el.ticket_types" ms-visible="$val.quantity>0">&yen;{{$val.prices.origPrice}}</div></td>
        <td class="td-price" ><span ms-if="el.bundle_info">赠送</span><div ms-if="!el.bundle_info" ms-repeat="el.ticket_types" ms-visible="$val.quantity>0">&yen;{{$val.prices.price}}</div></td>
        <td class="td-quantity"><div  ms-repeat="el.ticket_types" ms-visible="$val.quantity>0">{{$val.quantity}}{{$val.quantifier}}</div></td>
        <td class="td-subtotal"><span span ms-visible="el.bundle_info">--</span><span ms-visible="!el.bundle_info">&yen;{{el.subtotal}}</span></td>
        <td class="td-operation"><a href="javascript:;" ms-if="el.editable&&!el.edit" ms-click="modifyOptional(el)">修改<span ms-visible="el.editable&&!el.edit&&el.removable">/</span></a><a href="javascript:;" ms-if="el.removable" ms-click="removeOptional(el)">删除</a></td>
    </tr>
    <!--<tr class="selected-list" ms-repeat-el="selectedList">
      <td class="td-name">

        <div class="product-content">
          <h2><span>{{el.name}}</span><span class="add-label"></span></h2>
          <div class="fl color-green" ms-if="el.specialCode">套餐：{{el.specialName}}</div>

        </div>
      </td>
      <td class="td-oprice" style="line-height: 27px;">&yen;{{el.origPrice}}</td>
      <td class="td-price" style="line-height: 27px;">&yen;{{el.price}}</td>
      <td class="td-quantity"><div ms-class="disable:el.quantity==1"  ms-click="reduce(el)">-</div><input type="text" readonly  ms-value="{{el.quantity}}"/><div ms-class="disable:el.quantity==Math.min(99,mainProduct.pax_num)" ms-click="add(el)">+</div><span class="quantity-tips" ms-visible="el.showTips">最多可以选择{{Math.min(20,mainProduct.pax_num)}}人出行</span></td>
      <td class="td-subtotal"><span>&yen;{{el.quantity*el.price}}</span><div class="price-tips"><div class="price-tips-box">价格按成人价统计，儿童优惠在下一步生成<div class="arrow-up"></div></div></div></td>
      <td class="td-operation"><a href="javascript:;" ms-if="el.specialCode" ms-click="modifyOptional($index)">修改/</a><a href="javascript:;" ms-click="removeOptional($index)">删除</a></td>
    </tr>-->
    </tbody>
  </table>
  <div class="price-bar">
    <div>
      <div class="sum-orig-price">市场价总计：<em>&yen;{{sub_total_orig}}</em></div>
      <div class="sum-price">独享总计：<em><span>&yen;</span>{{sub_total}}</em><i>元</i></div>
    </div>
    <div>
      省：&yen;{{sub_total_orig-sub_total}}
    </div>

  </div>
  <div class="middle-bar">
    <div class="option-title">充实自己的旅行，您可以选择更多行程</div>

  </div>

  <div class="optional-product">
    <ul class="optional-list" ms-mouseleave="onMouseout">
      <li ms-repeat-el="optionalList" ms-class="active:el.checked" ms-mouseover="onHover($index,el)" ms-click="selectOptional($index,el)"><i class="checkbox"></i>+{{el.price.price}}元得{{el.description.name}}</li>
    </ul>
    <div class="more-info" ms-controller="moreInfo" ms-visible="show" ms-css-top="{{-6+40*index}}">
      <div class="arrow-left"></div>
      <div class="more-title">
        <h3>{{name}}</h3>

        <div class="price-ctn"><i>&yen;{{origPrice}}</i>特惠价：&yen;<em>{{price}}</em>起</div>
      </div>
      <div class="fl color-green">包含：</div>
      <div class="content-list">{{contentList|html}}</div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>
<div class="progress-bar" ms-controller="productInfo">
  <div class="btn-ctn">
    <a class="back-btn button" href="javascript:history.go(-1);">上一步</a>
    <button class="next-btn button" ms-click="appendCart">下一步,填写联系人信息</button>
  </div>
</div>
<div class="product-dialog-mask"ms-controller="productDialog" ms-visible="show">
<div class="product-dialog" >
  <a class="close-btn" href="javascript:;" ms-click="close"></a>
  <h3>{{name}}</h3>
  <div class="product-form">
    <!--<div class="field-ctn">
      <label for="tourdate">使用时间：</label>
      <input type="text" id="tourdate"/>
    </div>
    <div class="field-ctn">
      <label for="tourdate">使用人数：</label>

      <span class="select-holder">
        <select name="" id="">
          <option value="">1</option>
          <option value="">2</option>
        </select>
        <i class="arrow-down"></i>
      </span>

      <span class="ticket-label">成人</span>
      <span class="sep">|</span>
      <span class="select-holder">
        <select name="" id="">
          <option value="">1</option>
          <option value="">2</option>
        </select>
        <i class="arrow-down"></i>
      </span>
      <span class="ticket-label">儿童(8-16岁)</span>
    </div>
    <div class="field-ctn departure">
      <label for="">接送地点：</label>
      <span class="select-holder">
        <select name="" id="">
          <option value="">1</option>
          <option value="">2</option>
        </select>
        <i class="arrow-down"></i>
      </span>
    </div>-->
    <div class="field-ctn">
      <label for="">选择套餐：</label>
      <ul>
        <li ms-repeat-el="specialCodes" ms-class="active:el.checked" ms-click="selectSpecial($index)"><i class="radio"><i class="radio-inner"></i></i>{{el.cn_name}}<em>&yen;{{el.price}}</em></li>
      </ul>
    </div>
    <!--<button class="confirm-btn" ms-click="confirm">确定</button>-->
  </div>
</div>
</div>

