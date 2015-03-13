<section class="buy-section">
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
    <div class="hi-carousel fade" id="base_carousel">
        <div class="fix-ctn">

            <div class="carousel-list">
                <div class="carousel-item" ms-repeat-item="sliders"
                     ms-class="active : $index == 0"
                     ms-css-background-image="url('{{ item.image_url }}?imageView/5/w/688/h/419')"
                     data-repeat-rendered="initSlider">
                    <div class="item-above">
                        <div class="text-wrap">
                            <h3 class="item-name">{{ item.name }}</h3>
                            <p class="item-desc">{{ item.short_desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="to to-prev"><i class="icon-arrow-left"></i></div>
            <div class="to to-next"><i class="icon-arrow-right"></i></div>
            <div class="index-list">
                <i class="to to-index" ms-repeat-index="sliders"
                   ms-class="active : $index == 0"></i>
            </div>
            <img ms-class="activity-tag-flashsale"
                 ms-if="activity_info && activity_info.length != 0 && activity_info.show_activity_tag == 1
                 && (activity_info.name == 'FlashSale' || activity_info.name == 'FridaySale')"
                 src="themes/public/images/activities/activity_tag_flashsale.png">
            <img ms-class="activity-tag-{{activity_info.activity_id}}"
                 ms-if="activity_info && activity_info.length != 0 && activity_info.show_activity_tag == 1
                 && !(activity_info.name == 'FlashSale' || activity_info.name == 'FridaySale')"
                 ms-src="themes/public/images/activities/activity_tag_{{activity_info.activity_id}}.png">
        </div>
    </div>
    <div class="order-panel">
        <div class="price-ctn">
            <div class="sum-price" >{{show_prices.title}}<em>&yen;{{show_prices.price}}</em><i>{{ activity_info && activity_info.length != 0 && activity_info.name == 'FlashSale' ? '' : '起'}}</i></div>
            <div class="orig-price"><i>{{activity_info && activity_info.length != 0 && activity_info.name == 'FlashSale' ?'玩途价':'门市价'}}</i><em>&yen;{{show_prices.orig_price}}</em></div>
        </div>
        <div class="field-ctn" ms-controller="TourDate" ms-if="flag">
            <div class="field-title">您的{{tour_date_title}}</div>
            <div class="field-wrap" ms-class="{{status}}" id="tour_date" ms-click="open">
                <i class="head-icon icon-calendar"></i>
                <div class="input-preview">{{date_text}}</div>
                <i class="i-arrow-down"></i>
            </div>
            <div class="error-tips" ms-visible="status=='empty'">请选择出行日期
                <div class="arrow-right"></div>
            </div>
        </div>
        <div class="field-ctn" ms-controller="SpecialGroup">
            <div class="special-group-ctn" ms-repeat-gp="special_groups">
            <div class="field-title">{{gp.title}}</div>
            <div class="field-wrap"  ms-click="open(gp,$event)" ms-class="{{gp.status}}">
                <i class="head-icon icon-menu"></i>
                <div class="input-preview">{{gp.select.cn_name}}</div>
                <i class="i-arrow-down"></i>
            </div>

            <div class="select-box" ms-visible="gp.show">
                <div class="select-box-ctn">
                    <div class="option" ms-repeat-sp="gp.special_codes" ms-click="select($outer.$index,sp)">{{sp.cn_name}}
                    </div>
                </div>
                <i class="arrow-up"></i>
            </div>
            <div class="error-tips" ms-visible="gp.status=='empty'">请选择{{gp.title}}
                <div class="arrow-right"></div>
            </div>
            </div>
        </div>
        <div class="field-ctn" ms-controller="TicketType">
            <div class="field-title">出行人数选择</div>
            <div class="field-wrap quantity" ms-class="{{status}}" ms-click="open">
                <i class="head-icon icon-empty-person"></i>
                <div class="input-preview">
                    <div class="ticket-wrap" ms-visible="status=='complete'" ms-repeat-el="ticket_types">{{el.name}}
                                                                                                         {{el.quantity}}
                    </div>
                </div>
                <i class="i-arrow-down"></i>

            </div>
            <div class="quantity-selector" ms-visible="show">
                <div class="arrow-up"></div>
                <div class="ticket-ctn" ms-class="border-top:$first" ms-repeat-el="ticket_types">
                    <div class="ticket-type">
                        {{el.name}}
                        <div class="ticket-range" ms-if="el.age_range">({{el.age_range}})</div>
                    </div>
                    <div class="ticket-price"><em ms-visible="el.price>0">&yen;{{el.price}}</em></div>
                    <div class="num-counter">
                        <span class="reduce" ms-click="counterReduce($index,$event)"></span>
                        <input type="text" ms-value="{{el.quantity}}" readonly="true" />
                        <span class="add" ms-click="counterAdd($index,$event)"></span>
                    </div>
                    <div class="ticket-desc">{{el.description}}</div>
                    <div ms-if="!$last" class="line-seperator"></div>
                </div>

                <div class="confirm">确定</div>
                <div class="ticket-rule icon-exclamation-circle" ms-class="ticket-tips-show:ticket_tips"><span>{{ticket_tips}}</span>
                </div>
            </div>

            <div class="error-tips" ms-visible="status=='empty'">请选择出行人数
                <div class="arrow-right"></div>
            </div>
        </div>
        <div class="field-ctn" ms-controller="Departure" ms-if="flag">
            <div class="field-title">{{departure_title}}</div>
            <div class="field-wrap" ms-attr-title="{{status=='disable'?'请先选择出行日期':''}}" ms-click="open"
                 ms-class="{{status}}">
                <i class="head-icon icon-menu"></i>
                <div class="input-preview">{{departure_text}}</div>
                <i class="i-arrow-down"></i>
            </div>

            <div class="select-box" ms-visible="show">
                <div class="select-box-ctn">
                    <div class="option" ms-repeat-dp="departures" ms-click="selectDeparture(dp)">{{dp.showTime}} {{dp.departure_point}}
                    </div>
                </div>
                <i class="arrow-up"></i>
            </div>
            <div class="error-tips" ms-visible="status=='empty'">请选择{{departure_title}}
                <div class="arrow-right"></div>
            </div>
        </div>
        <div class="field-ctn special-ctn " ms-if="flag" ms-class="collapse-special:selected_index>=0"
             ms-css-height="{{30+(special_codes.length)*53}}" ms-controller="SpecialCode">
            <div class="field-title">{{special_title}}<span class="special-title-desc">（共有<em>{{special_codes.length}}</em>个套餐供选择）</span></div>
            <div class="special" ms-repeat-sp="special_codes" ms-mouseenter="mousein($index,sp)"
                 ms-mouseleave="mouseout($index,sp.description)" ms-css-top="{{30+$index*53}}"
                 ms-css-z-index="{{100-Math.abs(special_code.index-$index)}}" ms-class="checked:sp.checked"
                 ms-css-transition="{{0.1+(0.5/(special_codes.length-1))*$index}}s" ms-click="select(sp)"><i
                    class="radio"><i class="radio-inner"></i></i><span class="special-wrap">{{sp.cn_name}}</span><span
                    class="special-price">&yen;{{sp.price.price}}</span><i ms-visible="selected_index!=-1" class="i-arrow-down"></i></div>
            <div class="special-desc" ms-class="fadein:show_desc" ms-css-top="top">
                <h3>{{cur_special.cn_name}}</h3>
                <p ms-visible="cur_special.description">*{{cur_special.description}}</p>
                <div class="ticket-holder" >
                    <div class="ticket-item" ms-repeat-tk="ticket_prices"><span>{{tk.name}}</span><em>&yen;{{tk.price}}</em><strong>&yen;{{tk.orig_price}}</strong></div>
                </div>
                <div class="arrow-right"></div>
            </div>
            <div class="special-stack-ctn">
                <div class="special-stack special-stack-1"></div>
                <div class="special-stack special-stack-2"></div>
            </div>

        </div>
        <div class="summary-holder" ms-controller="TicketType">
            <div class="summary"  ms-visible="show_panel">
                <div class="seperator"></div>
                <div class="summary-line">
                    <ul>
                        <li ms-repeat-tk="ticket_types"><span>{{tk.name}} x{{tk.quantity}} </span><em>&yen;{{tk.price}}</em><strong>&yen;{{tk.orig_price}}</strong>
                        </li>
                    </ul>
                </div>
                <div class="summary-price">总计：<em>&yen;{{sum_price}}</em></div>

            </div>
            <button class="buy-btn" id="buy_btn" ms-class="disable:!show_panel" ms-click="addCart($event)">{{buy_label||'预订'}}</button>
        </div>

    </div>
</section>