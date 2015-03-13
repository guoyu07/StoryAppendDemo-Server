<div class="main-wrap">
  <section class="city-cover-ctn" id="city_cover_ctn" ms-controller="cityInfo">

    <div class="">
    <div class="fix-wrap">
      <div class="city-name-ctn">
        <div class="city-cn-name">{{cn_name}}</div>
<!--        <div class="city-en-name">{{en_name}}</div>-->
      </div>
      <div class="nav-ctn">
        <nav>
            <a href="<?= $this->createUrl('home/index'); ?>">首页</a>
            <span class="sep">></span>
            <a ms-attr-href="{{country_url}}">{{country_name}}</a>
            <span class="sep">></span>
            <a href="javascript:;">{{cn_name}}</a>
        </nav>
      </div>
        <div class="img-ctn" ms-css-background-image="url({{city_image.banner_image_url}})">
        </div>
        <div class="header-st">
        <div class="header-t">
            <!--          <div class="section-title">选择是件美好的事情</div>-->
            <!--          <div class="section-subtitle">随心选择符合您出行计划和预算的旅程</div>-->
            <div class="fix-hold">
                <div class="">
                    <div class="group-list-ctn" ms-controller="groupCatalog">
                        <ul class="catalog-list" id="catalog_list">
                            <li class="eachtab" data-hover="{{group.name}}" ms-repeat-group="list" ms-attr-gid="{{group.group_id}}" ms-data-index="$index"
                                ms-class-1="active:group.active" ms-click="switchGroup" ms-mouseover="changeOver($event)" ms-mouseout="resetLine">
                                <img src="themes/public/images/common/hot.png" style="opacity:0.7" class="popular-img" ms-if="group.type == 2" alt=""/>
                                <img src="themes/public/images/common/all.png" style="opacity:0.7" class="popular-img" alt="" ms-if="group.type == 1"/>{{group.name}}
                            </li>
                        </ul>
                        <div class="underline-ctn">
                            <div class="underline" ms-css-left="{{cLeft}}" ms-css-width="{{cWidth}}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
  </section>

  <div class="main-content ">
<!--    <section class="favorite container" ms-controller="favorite">-->
<!--      <div class="section-title">深受大家喜爱</div>-->
<!--      <div class="favorite-pro-list" >-->
<!--        <div class="row"  ms-css-width="{{Math.ceil(products.length/4)*1020}}"  ms-css-left="-{{showIndex*1020}}" msx-css-transform="translateX(-{{showIndex*1020}}px)">-->
<!--          <div class="product-block col-md-3" ms-repeat-el="products">-->
<!--            <a class="product" ms-attr-href="{{el.link_url}}">-->
<!--              <div class="cover-ctn"><img ms-attr-src="{{el.cover_image.image_url}}?imageView/1/w/233/h/178" alt=""/>-->
<!--              </div>-->
<!--              <div class="special-tag" ms-if="el.show_prices.special_info">-->
<!--                <h4>立减</h4>-->
<!--                <div>-->
<!--                  <em>￥{{el.show_prices.discount}}</em>-->
<!--                </div>-->
<!--              </div>-->
<!---->
<!--              <div class="product-info">-->
<!--                <div class="product-name">{{el.description.name}}</div>-->
<!--              </div>-->
<!--              <div class="price-ctn">-->
<!--                <span class="orig-price">￥{{el.show_prices.orig_price}}</span><span>￥{{el.show_prices.price}}</span>-->
<!--              </div>-->
<!---->
<!--              <div class="product-mask">-->
<!--                <div class="cell-wrap">-->
<!--                  <div class="product-desc">{{el.description.summary}}</div>-->
<!--                </div>-->
<!---->
<!--              </div>-->
<!--            </a>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--      <div class="arrow-left" ms-if="products.length>4&&showIndex>0" ms-click="turnLeft"></div>-->
<!--      <div class="arrow-right" ms-if="products.length>4&&showIndex<Math.ceil(products.length/4)-1" ms-click="turnRight"></div>-->
<!--    </section>-->
    <section class="all-product">
      <div class="group-ctn container" ms-controller="groupInfo">
        <div ms-if="type!=1&&type!=2">
          <div class="group-desc">
            {{description}}
          </div>
          <div class="hitour-specialist" style="display: none;">
            <div class="avantar">--Snowy 玩途美国旅行专家</div>
          </div>
        </div>
          <div class="product-list row padding-row">
            <div class="product-block col-md-6" ms-repeat-el="products">
              <a class="product-lg" target="_blank" ms-attr-href="{{el.link_url}}">
                <div class="cover-ctn-lg"><img ms-attr-src="{{el.cover_image.image_url}}?imageView/1/w/488/h/356" alt=""/>
                </div>
                <div class="activity-tag" ms-if="el.activity_info && el.activity_info.length != 0">
                    <img ms-src="{{el.activity_info.tag_large_url}}">
                </div>
                <div class="special-tag" ms-if="el.show_prices.special_info">
                  <h4>立减</h4>
                  <div>
                    <em>￥{{el.show_prices.discount}}</em>
                  </div>
                  <div class="channel">{{el.show_prices.special_info.reseller}}<br>{{el.show_prices.special_info.slogan}}</div>
<!--                  <div class="middle-mock">a</div>-->
                </div>

                <div class="product-info">
                  <div class="product-name-lg">{{el.description.name|html}}</div>
                  <div class="product-tmp"></div>
                </div>
                <div class="price-ctn">
                  <span class="orig-price-lg"><span class="rmb"></span>{{el.show_prices.orig_price}}<i class="line-through"></i></span><span><span class="rmb"></span>{{el.show_prices.price}}</span>
                </div>
                <div class="product-mask-ctn"><div class="product-mask">
                    <div class="cell-wrap-lg">
                      <div class="product-desc-lg">{{el.description.summary}}</div>
                    </div>

                  </div></div>

              </a>
            </div>


          </div>
      </div>
    </section>
    <!-- <div class="row">
         <div class="catalog-ctn col-md-3">

         </div>
         <div class="content-list col-md-9" ms-controller="groupInfo">
             <div ms-if="type=='99'" class="group-title">
                 <div class="group-img-ctn"><img ms-attr-src="{{cover_image_url}}?imageView/1/w/340/h/307" alt=""/></div>
                 <div class="group-info">
                     <div class="group-title">{{name}}</div>
                     <div class="group-desc">{{description}}</div>

                 </div>

             </div>
             <div ms-if="type!='99'" class="group-head">
                 <h1>全部商品</h1>
                 <div class="tag-ctn">
                     <span ms-repeat-tag="tags" class="tag">{{tags.name}}</span>
                 </div>
             </div>

         </div>
     </div>-->
  </div>
</div>
<div class="back-top">
</div>
