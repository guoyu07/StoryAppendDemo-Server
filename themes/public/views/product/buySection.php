<section class="order-panel m-h">
    <!--GTA request-->
    <div class="GTA-notice">
        <div class="notice-content">
            <div class="single-p">
                <div class="super-circle"></div>
                <div class="p-content">亲爱的顾客，商品预订名额有限，玩途会第一时间帮您预订，并以邮件形式通知您预定结果。</div>
            </div>
            <div class="single-p">
                <div class="super-circle"></div>
                <div class="p-content">如果预订失败，玩途会48小时内为您协调门票时间或处理退款，让您后顾无忧。</div>
            </div>
        </div>
    </div>
    <div class="slider-ctn">

    </div>

    <!--  <div class="input-ctn" >
          <div class="input-wrap" ms-if="flag==1" id="tour_date" ms-controller="tourDate" ms-class-1="enable:enabled==1" ms-class-2="active:state=='complete'" ms-class-3="disabled:enabled==0" ms-class-4="error:state=='error'"ms-click="Open">
            <div for="tour_date" class="head-title icon-calendar" ms-class="upper:state=='complete'"><span>{{tour_date_title}}</span></div>
            <div class="input-preview" ms-visible="state=='complete'">{{selectedDate}}</div>
            <i class="drop-arrow" ms-class-1="icon-arrow-up:state=='editing'" ms-class-2="icon-arrow-down:state!='editing'"></i>
          </div>
          <div class="input-wrap quantity" ms-controller="ticketType" ms-class-1="enable:enabled==1" ms-class-2="active:state!='initial'" ms-class-3="disabled:enabled==0" ms-click="Open">
            <div class="head-title icon-empty-person" ms-class="upper:state=='complete'">数量选择</div>
            <i class="drop-arrow" ms-class-1="icon-arrow-up:state=='editing'" ms-class-2="icon-arrow-down:state!='editing'"></i>
            <div class="input-preview" ms-visible="state=='complete'">
              <div class="ticket-wrap" ms-repeat-el="ticketTypes">{{el.name}}&nbsp;{{el.quantity}}</div>
            </div>
            <div class="quantity-selector" ms-visible="state=='editing'">
              <div class="ticket-ctn" ms-repeat-el="ticketTypes" ms-visible="status!='collapse'">
                <div class="ticket-type">{{el.name}}<div class="ticket-range" ms-if="el.age_range">（{{el.age_range}}）</div></div>


                <div class="ticket-price"><em>{{el.price}}</em><strong>{{el.orig_price}}</strong></div>
                <div class="num-counter">
                  <span class="reduce" ms-click="counterReduce($index,$event)"></span>
                  <input type="text" ms-attr-value="{{el.quantity}}" readonly="true"/>
                  <span class="add" ms-click="counterAdd($index,$event)"></span>
                </div>
                <div class="ticket-desc" >{{el.description|html}}</div>
              </div>
              <div class="ticket-rule icon-exclamation-circle" ms-class="ticket-tips-show:ticketTips"><span>{{ticketTips}}</span></div>
            </div>
          </div>
          <div class="input-wrap departure" ms-if="flag==1" ms-controller="departure" ms-class-1="enable:enabled==1" ms-class-2="active:state=='complete'" ms-class-3="disabled:enabled==0" ms-class-4="error:state=='error'" ms-click="Open">
            <div class="head-title" ms-class="upper:state=='complete'"><i class="icon-three-bar"></i>{{departure_title}}</div>
            <div class="input-preview" ms-visible="state=='complete'">{{selectedDeparture}}</div>
            <i class="drop-arrow" ms-class-1="icon-arrow-up:state=='editing'" ms-class-2="icon-arrow-down:state!='editing'"></i>
            <div class="w-selector" ms-visible="state=='editing'">
              <div class="w-option" ms-attr-data-index="{{$index}}" ms-repeat-el="departures"  ms-attr-title="{{el.showTime}} {{el.departure_point}}" ms-click="selectOption">
                {{el.showTime}} {{el.departure_point}}
              </div>
            </div>
          </div>
    </div>
      <div class="special-code-ctn" ms-controller="specialCode">
        <div class="head-title" ms-if="flag==1">选择适合您的不同套餐</div>
          <div class="special-options clearfix" ms-if="flag==1">
              <button class="option-item" ms-class="active:selected.special_code==sp.special_code" ms-click="selectSpecial($index)" ms-repeat-sp="special_codes" >{{sp.cn_name}}</button>
          </div>
        <div class="special-wrap" ms-if="special_codes.length>0">
          <div class="special-info" ms-if="flag==1">
            <h3>{{selected.cn_name}}</h3>
            <p>{{selected.description}}</p>
          </div>
          <div class="ticket-type-ctn" ms-class="single-special:flag==0" ms-if="state=='show'">
            <div ms-repeat-tk="ticketTypes"><span>{{tk.name}}&nbsp;&nbsp;X&nbsp;{{tk.quantity}}</span><strong><i class="rmb"></i>{{calcPrice(selected.special_code,tk,1)}}</strong><em><i class="rmb"></i>{{calcPrice(selected.special_code,tk,2)}}</em></div>

          </div>

          <div class="buy-wrap" ms-if="state=='show'">
            <div class="subtotal"><i class="rmb"></i>{{calcPrice(selected.special_code,1,3)}}</div>
            <div class="discount">为您节省{{calcPrice(selected.special_code,1,4)}}元</div>
            <button class="buy-btn" ms-click="toBuy(selected.special_code)">购买</button>
          </div>
        </div>
      </div>
      <div class="product-rule col-ctn" ms-controller="buyNotice">
        <div class="content-list container">
          <ul>
            <li class="r-redeem">
              <h3>兑换规则</h3>
              <p>{{rules.redeem_desc|html}}</p>
            </li>

            <li class="r-return">
              <h3>退款限制</h3>
              <p>{{rules.return_desc}}</p>
            </li>
            <li class="r-shipping">
              <h3>发货限制</h3>
              <p>{{rules.shipping_desc}}</p>
            </li>
          </ul>
        </div>
      </div>-->
</section>