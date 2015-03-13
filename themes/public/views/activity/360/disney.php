<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="【玩途】">
<title>迪士尼乐园 欢乐来袭</title>
<link rel="stylesheet" href="/themes/public/bower_components/nivo-slider/nivo-slider.css" />
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
.nivo-control {
  width: 12px;
  height: 12px;
  display: inline-block;
  background: url(/themes/public/images/activity/disney/paging.png);
  cursor: pointer;
  font-size:0;
  -webkit-user-select:none;
  -moz-user-select:none;
  outline: none;
  margin:0 5px;
}
.nivo-control.active { background: url(/themes/public/images/activity/disney/paging-cur.png); }

.main-wrap{
  background: url(/themes/public/images/activity/disney/texture.png);
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
  background: url(/themes/public/images/activity/disney/slogan.png);
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
  background: url(/themes/public/images/activity/disney/right-arrow.png);
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
  background:url(/themes/public/images/activity/disney/time.png);
  width:54px;
  height:54px;
}
.info-block i.money{
  background:url(/themes/public/images/activity/disney/money.png);
  width:40px;
  height:55px;
}
.info-block i.locator{
  background:url(/themes/public/images/activity/disney/locator.png);
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
}
.more .db-arrow{
  background:url(/themes/public/images/activity/disney/db-right-arrow.png);
  width:14px;
  height:10px;
  display: inline-block;
  margin:0 4px;

}
</style>
<style type="text/css">
  /*reset*/
  html{-webkit-text-size-adjust:none}
  body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td,div{margin:0;padding:0}
  body,button,input,select,textarea{font:12px/1.5 simsun}h1,h2,h3,h4,h5,h6{font-size:100%}address,cite,dfn,em,var{font-style:normal}
  code,kbd,pre,samp{font-family:courier new,courier,monospace}ul,ol{list-style:none}a,a:hover{text-decoration:none}
  a:focus{outline:none;-moz-outline:none}sup{vertical-align:text-top}sub{vertical-align:text-bottom}
  legend{color:#000}fieldset,img,a{border:0 none;outline:0 none}input,textarea{outline:none}
  button,input,select,textarea{font-size:100%}table{border-collapse:collapse;border-spacing:0}
  abbr,article,aside,bb,datagrid,datalist,details,dialog,eventsource,figure,figcaption,footer,header,mark,menu,meter,nav,
  output,progress,section,time{display:block;height:auto}textarea{resize:none}
  .clearfix:after{clear:both;content:"\20";display:block;font-size:0;height:0;line-height:0;visibility:hidden}
  .clearfix{display:block;zoom:1;clear:none !important;}
  /*头部样式*/
  #hd .container{width:1000px;height:50px;margin:0 auto;position: relative;z-index: 1000}
  #hd {height:50px;background:#fff;}
  #hd .logo {height:50px;width:145px;float:left;}
  #hd .le-nav{float:left;margin-left:50px;_display:inline;}
  #hd .le-nav li{float:left;height:50px;border-right:1px solid #e5e5e5;position:relative;}
  #hd .le-nav li.last{border-right:none;}
  #hd .le-nav li a{float:left; display:block;padding-right:12px;line-height:50px;height:47px;font-size:14px;color:#666;font-family:Microsoft Yahei;overflow:hidden;}
  #hd .le-nav li a:hover,#hd .le-nav li a.hover{text-decoration:none;background:#f8f3f0;}
  #hd .le-nav li a:hover{color:#09c;}
  #hd .le-nav li i{margin:10px 8px 0 12px;float:left;height:30px;width:20px;background:url(http://p0.qhimg.com/t01dcaa87d742d04de9.png) no-repeat;}
  #hd .le-nav .home:hover{border-bottom:3px solid #DC1B50;}
  #hd .le-nav .sub-nav{display:none;}
  #hd .le-nav .hover{display:block;}

  #hd .le-nav .holiday:hover,#hd .le-nav .hover{border-bottom:3px solid #f8f3f0;}
  #hd .le-nav .sub-nav-holiday{border-bottom:3px solid #C6F;}

  #hd .le-nav .flight:hover{border-bottom:3px solid #f8f3f0;}
  #hd .le-nav .sub-nav-flight{border-bottom:3px solid #4BB4EB;}

  #hd .le-nav .hotel:hover{border-bottom:3px solid #f8f3f0;}
  #hd .le-nav .sub-nav-hotel{border-bottom:3px solid #EC7579;}

  #hd .le-nav .train:hover{border-bottom:3px solid #f8f3f0;}
  #hd .le-nav .sub-nav-train{border-bottom:3px solid #FFC000;}

  #hd .le-nav .tuan:hover{border-bottom:3px solid #4FBDB0;}
  #hd .le-nav .tripmap:hover{border-bottom:3px solid #FD604D;}
  #hd .le-nav .website:hover{border-bottom:3px solid #C8B897;}
  #hd .le-nav .home i{background-position:0 0;}
  #hd .le-nav .flight i{background-position:-30px 0;}
  #hd .le-nav .hotel i{background-position:-60px 0;}
  #hd .le-nav .train i{background-position:-120px 0;}
  #hd .le-nav .tuan i{background-position:-90px 0;}
  #hd .le-nav .holiday i{background-position:-150px 0;}
  #hd .le-nav .tripmap i{background-position:-180px 0;}
  #hd .le-nav .website i{background-position:-210px 0;}
  #hd .sub-nav{position:absolute;top:50px;left:0;background:#f8f3f0;width:100%;padding:0;text-align:center;}
  #hd .sub-nav dt{width:100%;text-align:center;}
  #hd .sub-nav dt a{float:none;padding:0;}
  /*底部样式*/
  #ft {background: #fff;height: 87px;text-align: center;color: #999;}
  #ft .channel{width:1000px;margin:10px auto;}
  #ft .channel h3{height:30px;margin:10px 0;background:url(http://p8.qhimg.com/t01b3478d1015e652f5.png) center repeat-x;width:100%;}
  #ft .channel h3 span{display:inline-block;padding:0 20px;background:#fff;color:#333;font-size:18px;font-family:Microsoft Yahei;font-weight:500;}
  #ft .channel p{height:24px;line-height:24px;overflow:hidden;}
  #ft .channel p a{float:left;background:url(http://p0.qhimg.com/t01cab8bc124bb43a36.png) no-repeat;margin-left:54px;padding-left:30px;color:#069;line-height:24px;_display:inline;}
  #ft .channel p a:hover{color:#f60;text-decoration:none;}
  #ft .channel p .flight{background-position:0 0;}
  #ft .channel p .inter-flight{background-position:0 -24px;}
  #ft .channel p .hotel{background-position:0 -48px;}
  #ft .channel p .inter-hotel{background-position:0 -72px;}
  #ft .channel p .holiday{background-position:0 -96px;}
  #ft .channel p .tickets{background-position:0 -120px;}
  #ft .channel p .tuan{background-position:0 -144px;}
  #ft .channel p .flight:hover{background-position:-100px 0;}
  #ft .channel p .inter-flight:hover{background-position:-100px -24px;}
  #ft .channel p .hotel:hover{background-position:-100px -48px;}
  #ft .channel p .inter-hotel:hover{background-position:-100px -72px;}
  #ft .channel p .holiday:hover{background-position:-100px -96px;}
  #ft .channel p .tickets:hover{background-position:-100px -120px;}
  #ft .channel p .tuan:hover{background-position:-100px -144px;}
  #ft p {height: 20px;line-height: 20px;}
  #ft .link {padding: 19px 0 4px;border-top: 6px solid #de3c12;}
  #ft a {color: #999;}
  #ft a:hover{text-decoration:underline;}
  #ft .link a {margin: 0 5px;}
</style>
<script src="/themes/public/bower_components/jquery/jquery.min.js"></script>
<script src="/themes/public/javascripts/lib/whisker.js"></script>

</head>
<body>
<div id="hd">
  <div class="container">
    <div class="logo"><a href="http://go.360.cn/" title="360旅游" target="_blank"><img src="http://p0.qhimg.com/t01b831aecc51b5ca05.png" alt="360旅游" /></a></div>
    <ul class="le-nav clearfix">
      <li><a href="http://go.360.cn/" target="_blank" class="home"><i></i>首页</a></li>
      <li>
        <a href="http://go.360.cn/holiday" target="_blank" class="holiday"><i></i>旅游度假</a>
        <dl class="sub-nav sub-nav-holiday">
          <dt><a href="http://go.360.cn/holiday/visa" target="_blank" class="visa">签证</a></dt>
        </dl>
      </li>
      <li>
        <a href="http://go.360.cn/flight" target="_blank" class="flight"><i></i>机票</a>

      </li>
      <li>
        <a href="http://go.360.cn/hotel" target="_blank" class="hotel"><i></i>酒店</a>
        <dl class="sub-nav sub-nav-hotel">
          <dt><a href="http://www.booking.com/?aid=364336" target="_blank" class="visa">海外酒店</a></dt>
          <dt><a href="http://go.360.cn/hotel/apartment" target="_blank" class="visa">度假公寓</a></dt>
        </dl>
      </li>
      <li>
        <a href="http://go.360.cn/train" target="_blank" class="train"><i></i>火车票</a>
        <dl class="sub-nav sub-nav-train">
          <dt><a href="http://go.360.cn/bus" target="_blank" class="visa">长途汽车</a></dt>
        </dl>
      </li>
      <li><a href="http://go.360.cn/top10" target="_blank" class="tuan"><i></i>限时特价</a></li>
      <li><a href="http://go.360.cn/tripmap" target="_blank" class="tripmap"><i></i>景点大全</a></li>
      <li class="last"><a href="http://go.360.cn/website" target="_blank" class="website"><i></i>旅游网址</a></li>
    </ul>
  </div>
</div>
<script type="text/html" id="tmpl_pro_list">
  {#each $productList($groupTitle=>$group)}
  <section>
    <div class="section-title"><div class="line-prev"></div><span>{$groupTitle}</span><div class="line-next"></div><div class="more"><a  target="_blank" href="{$group.city_code}">更多{$group.city}景点</a><i class="db-arrow"></i></div></div>

    <div class="section-content">
      {#each $group.list}
      <a class="product" target="_blank" href="/sightseeing/{$product_id}" {#if $_INDEX%2==1}style="margin-right:0"{/if}>
      <div class="img-ctn">
        <img src="/themes/public/images/activity/disney/product-{$image_url}.jpg" alt=""/>
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

<div id="ft">
  <div class="channel">
    <h3><span>热门搜索</span></h3>
    <p>
      <a href="http://go.360.cn/flight" class="flight" target="_blank">特价机票</a>
      <a href="http://go.360.cn/flight#inter" class="inter-flight" target="_blank">国际机票</a>
      <a href="http://go.360.cn/hotel" class="hotel" target="_blank">超值酒店</a>
      <a href="http://www.booking.com/?aid=364336" class="inter-hotel" target="_blank">海外酒店</a>
      <a href="http://go.360.cn/holiday" class="holiday" target="_blank">旅游度假</a>
      <a href="http://go.360.cn/ticket/search" class="tickets" target="_blank">景点门票</a>
      <a href="http://go.360.cn/top10" class="tuan" target="_blank">限时特价</a>
    </p>
  </div>
  <div class="activity">
    <iframe src="http://go.360.cn/zt/activity.html" frameborder="0" width="1000" height="220" scrolling="no"></iframe>
  </div>
  <p class="link">
    <a href="http://feedback.hao.360.cn/" target="_blank">意见反馈</a>
    |
    <a href="http://e.weibo.com/360lvyou" target="_blank">官方微博</a>
    |
    <a href="javascript:" onclick="try{ window.external.AddFavorite('http://go.360.cn/','360旅游'); return false;} catch(e){ (window.sidebar)?window.sidebar.addPanel('360旅游','http://go.360.cn/',''):alert('请使用按键 Ctrl+d，收藏360旅游'); }finally{return false;}">加入收藏</a>
    |
    <a href="http://hao.360.cn/about.html" target="_blank">关于我们</a>
    |
    <a href="http://www.360.cn/about/contactus.html" target="_blank">联系方式</a>
  </p>
  <p class="copyright">
    Copyright © 360网址导航. All Rights Reserved.
    <a href="http://www.miibeian.gov.cn/" target="_blank">京ICP证080047号</a>
  </p>
</div>





<script src="/themes/public/bower_components/nivo-slider/jquery.nivo.slider.pack.js">

</script>

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
<script src="/themes/public/javascripts/360/monitor-v1.1.1.min.js"></script>


<script type="text/javascript" src="http://s0.qhimg.com/lib/qwrap/115.js"></script>
<script type="text/javascript">
  (function(){
    W('#hd .le-nav').delegate('li','mouseenter',function(){
      var me =W(this);
      me.query('>a').addClass('hover');
      me.query('.sub-nav').addClass('hover');
    });
    W('#hd .le-nav').delegate('li','mouseleave',function(){
      var me =W(this);
      me.query('>a').removeClass('hover');
      me.query('.sub-nav').removeClass('hover');
    });
  }());
</script>

<script type="text/javascript">
  //baidu statistics
  var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
  document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F91856bd91b936d99158ba9b6b7020a48' type='text/javascript'%3E%3C/script%3E"));
  //google statistics
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-44367282-1', 'auto');
  ga('send', 'pageview');
</script>

</body>
</html>