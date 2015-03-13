<link rel="stylesheet" href="themes/public/bower_components/nivo-slider/nivo-slider.css" />
<style>
.nivo-directionNav{
  display: none;
}
.nivo-controlNav{
  position: relative;
  top: -50px;
  z-index: 10;
  width: 100%;
}
.loading-mask{
  display: none;
}
.nivo-control {
  width: 12px;
  height: 12px;
  display: inline-block;
  background: url(themes/public/images/activity/disney/paging.png);
  cursor: pointer;
  font-size:0;
  -webkit-user-select:none;
  -moz-user-select:none;
  outline: none;
  margin:0 5px;
}
.nivo-control.active { background: url(themes/public/images/activity/disney/paging-cur.png); }

.main-wrap{
  background: url(themes/public/images/activity/disney/texture.png);
}
.header{
  width:1280px;
  margin:0 auto;
}
#product_list{
  width: 1000px;
  margin: 0 auto;
}
.activity-title{
  background: url(themes/public/images/activity/disney/slogan.png);
  width:1218px;
  height:200px;
  margin:-50px auto 0 auto;
}
section {
  margin-bottom: 23px;
}
.product{
  display: inline-block;
  background: #FFF;
  color: #666;
  width: 490px;
  height: 287px;
  margin-right:20px;
  margin-bottom: 20px;
  vertical-align: top;
  box-shadow: 0 0 5px 0 #BBB;
}
.product:hover{
  box-shadow: 0 0 10px 0px #A4A4A4;
  text-decoration: none;
  color:#000;

}
.product-info .tags{
  font-size: 12px;
  color: #FFF;
  padding: 2px 5px;
  background: #F60;
  vertical-align: top;
  margin-left: 10px;
}
.product-info{
  position: relative;
  overflow: hidden;
  height: 89px;
}
.product-info .product-name{
  margin: 16px 0 18px 18px;
  font-size: 18px;

}
.product-price-ctn{
  position: absolute;
  bottom: 6px;
  right: 18px;

}
.img-ctn{
  width: 490px;
  height: 198px;
}
.product-price-ctn .price{
  font-size: 30px;
  color: #F60;
  vertical-align: baseline;
}
.product-price-ctn .price-suffix{
  font-size: 14px;
  line-height: 7px;
  vertical-align: baseline;
  margin-left: 5px;
  position: relative;
  top: -1px;
}
.product-info .product-slogan{
  font-size: 12px;
  color: #F60;
  margin-left: 18px;
}
.product-info .right-arrow{
  background: url(themes/public/images/activity/disney/right-arrow.png);
  width:20px;
  height:20px;
  display: inline-block;
  position: relative;
  top: 3px;
  margin-left: 7px;
}
.section-content{
  font-size:0;
}
.section-title{
  color: #f60;
  font-size: 28px;
  line-height:30px;
  margin: 0 0 20px 32px;
  padding: 0 5px;
  position: relative;
}
.section-title .line-prev{
  border-top: 1px solid #E6DDD7;
  border-bottom: 1px solid #FFF;
  width: 27px;
  position: absolute;
  left: -31px;
  top: 16px;
}
.section-title .line-next{
  border-top: 1px solid #E6DDD7;
  border-bottom: 1px solid #FFF;
  width: 560px;
  position: absolute;
  top: 16px;
  right: 168px;
}
.bottom-panel{
  text-align: center;
  background: #ededed;
  padding: 26px 0 32px 0;
}
.info-block{
  display: inline-block;
  vertical-align: top;
  margin-right:126px;
}
.info-block i{
  display: inline-block;
  line-height: 55px;
  margin-right: 12px;
}
.info-block i.time{
  background:url(themes/public/images/activity/disney/time.png);
  width:54px;
  height:54px;
}
.info-block i.money{
  background:url(themes/public/images/activity/disney/money.png);
  width:40px;
  height:55px;
}
.info-block i.locator{
  background:url(themes/public/images/activity/disney/locator.png);
  width:43px;
  height:52px;
}
.block-ctn{
  display: inline-block;
  text-align: left;
}
.block-ctn .block-title{
  font-size: 18px;
  color: #666;
  margin-bottom: 6px;
}
.block-ctn p{
  margin: 0;
  font-size: 12px;
  color: #999;
}
.main-topbar{
  border-bottom: 1px solid rgba(0, 0, 0, 0.3);
  box-shadow: 0 1px 1px rgba(255, 255, 255, 0.3);
  background-color: transparent;

}
.main-topbar .topmenu{
  background-color: transparent;
}
.main-topbar .search{
  display:none;
}
.more{
  float: right;
  line-height: 24px;

}
.more a{
  font-size: 18px;
  line-height: 24px;
  color: #f60;
  border-bottom: 1px solid transparent;
  padding-bottom: 1px;
}
.more a:hover{
  border-bottom: 1px solid #f60;
  text-decoration: none;
}
.more .db-arrow{
  background:url(themes/public/images/activity/disney/db-right-arrow.png);
  width:14px;
  height:10px;
  display: inline-block;
  margin:0 4px;

}
</style>
<script type="text/html" id="tmpl_pro_list">
  {#each $productList($groupTitle=>$group)}
  <section>
    <div class="section-title"><div class="line-prev"></div><span>{$groupTitle}</span><div class="line-next"></div><div class="more"><a  target="_blank" href="{$group.city_code}">更多{$group.city}景点</a><i class="db-arrow"></i></div></div>

    <div class="section-content">
      {#each $group.list}
      <a class="product" target="_blank" href="/sightseeing/{$product_id}" {#if $_INDEX%2==1}style="margin-right:0"{/if}>
      <div class="img-ctn">
        <img src="themes/public/images/activity/disney/product-{$image_url}.jpg" alt=""/>
      </div>
      <div class="product-info">
        <div class="product-name"><span>{$name}</span>{#if $tags}<span class="tags">{$tags}</span>{/if}</div>
        <div class="product-slogan">{$slogan}</div>
        <div class="product-price-ctn"><span class="price">￥{$price}</span><span class="price-suffix">元起</span><i class="right-arrow"></i></div>
      </div>
      </a>
      {/each}
    </div>
  </section>
  {/each}
</script>
<div class="main-wrap for-city row-fluid">
  <div id="wrap" x-controll="AppCtrl">
    <div class="header">
      <div class="nivoSlider">
        <a target="_blank" href="/sightseeing/1328"><img src="http://hitour.qiniudn.com/652ef4169550168e06bba94251ab52d7.jpg" alt=""/></a>
        <a target="_blank" href="/sightseeing/909"><img src="http://hitour.qiniudn.com/616ea7e22344746aa206fdd64db28b7c.jpg" alt=""/></a>
        <a target="_blank" href="/sightseeing/1329"><img src="http://hitour.qiniudn.com/c8ec3288f60fa3841bd6f28e0255bf61.jpg" alt=""/></a>
      </div>
    </div>
    <div class="activity-title"></div>
    <div id="product_list">

    </div>
    <div class="bottom-panel">
      <div class="info-block">
        <i class="time"></i>
        <div class="block-ctn">
          <div class="block-title">省时</div>
          <p>你不再需要寻找售票处</p>
          <p>而不用把时间花在排队进场上</p>
        </div>
      </div>
      <div class="info-block">
        <i class="money"></i>
        <div class="block-ctn">
          <div class="block-title">超值</div>
          <p>更少的花费</p>
          <p>却能带给你更多的享受和体验</p>
        </div>
      </div>
      <div class="info-block" style="margin-right: 0">
        <i class="locator"></i>
        <div class="block-ctn">
          <div class="block-title">精华</div>
          <p>一张玩途通票玩转一座城市</p>
          <p>让你绝不错过城市的任何精华之处</p>
        </div>
      </div>
    </div>

  </div>
</div>






<script src="themes/public/bower_components/nivo-slider/jquery.nivo.slider.pack.js">

</script>
<script src="themes/public/javascripts/lib/whisker.js"></script>
<script>
  var data = {
    '香港迪士尼乐园': {city: '香港', city_code: '/HongKong/Hong_Kong', list: [
      {
        product_id: 1305,
        name: '香港迪士尼乐园1日门票',
        slogan: '史上最低价',
        price: '309',
        tags: '',
        image_url: '1'

      },
      {
        product_id: 1306,
        name: '香港迪士尼乐园2日门票',
        slogan: '2日内无限畅游',
        price: '388',
        tags: '可于7天内2日入园',
        image_url: '2'

      },
      {
        product_id: 1329,
        name: ' 香港迪士尼乐园1日亲子套票',
        slogan: '含1张迪士尼成人票和1张儿童票加玩途优惠劵',
        price: '519',
        tags: '含20元玩途优惠卷',
        image_url: '6'
      },
      {
        product_id: 1328,
        name: '香港迪士尼乐园1日情侣套票',
        slogan: '含2张迪士尼成人票加玩途优惠劵',
        price: '609',
        tags: '含20元玩途优惠卷',
        image_url: '3'

      },
      {
        product_id: 1307,
        name: '香港迪士尼乐园1日门票',
        slogan: '含机场巴士车票，方便快捷',
        price: '409',
        tags: '含深圳机场直达巴士',
        image_url: '4'

      }

    ]},
    '奥兰多迪士尼乐园': {city: '奥兰多', city_code: '/United_States/Orlando', list: [
      {
        product_id: 1344,
        name: '奥兰多迪士尼乐园1日票',
        slogan: '享受整日欢乐',
        price: '910',
        tags: '四大园区四选一',
        image_url: '7'

      },
      {
        product_id: 1345,
        name: '奥兰多迪士尼乐园2日票',
        slogan: ' 可于14天内2日入园',
        price: '1599',
        tags: '四大园区随意选择',
        image_url: '8'
      }

    ]},
    '洛杉矶迪士尼乐园': {city: '洛杉矶', city_code: '/United_States/Los_Angeles', list: [
      {
        product_id: 1333,
        name: '洛杉矶迪士尼乐园1日1园票（含接送）',
        slogan: '含酒店至园区接送',
        price: '599',
        tags: '两大园区二选一',
        image_url: '9'

      },
      {
        product_id: 909,
        name: '洛杉矶迪士尼乐园2日2园票',
        slogan: '可于14天内2日入园',
        price: '1151',
        tags: '两大园区随意选择',
        image_url: '10'

      }
    ]},
    '巴黎迪士尼乐园': {city: '巴黎', city_code: '/France/Paris', list: [
      {
        product_id: 1336,
        name: '巴黎迪士尼乐园1日门票',
        slogan: '巴黎迪士尼乐园门口取票',
        price: '479',
        tags: '两大园区二选一',
        image_url: '11'

      }
    ]}
  };
</script>
<script>

  var html=Whisker.render($('#tmpl_pro_list').html(), {productList: data});
  $('#product_list').html(html);
  $('.line-next').each(function(index,el){
    if(index==0||index==3){
      $(el).css({
        'width':'605px',
        'right':'150px'
      });
    }
  });

  $('.nivoSlider').nivoSlider({
    pauseTime: 8000,
    effect:'fade'
  });
</script>




