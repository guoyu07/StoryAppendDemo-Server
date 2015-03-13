<!DOCTYPE HTML>
<html>
<head>
  <?php
  $this->beginContent('//layouts/commonHead');
  $this->endContent();
  ?>

  <script type="text/javascript">
    var $request_urls = JSON.parse( '<?php echo !empty($this->request_urls) ? json_encode($this->request_urls) : json_encode(array()); ?>' );
  </script>
  <?php
  $deploy_dir=Yii::app()->params['DEPLOY_STATE']=='release'?'resource.release':'resource';
  $this->beginContent('//'.$deploy_dir.'/base.res');
  $this->endContent();
  if (isset($this->resource_refs)) {
    $resPath = $this->resource_refs;
  } else {
    $resPath = strtolower(str_replace('Controller', '', get_class($this))) . '.res';
  }
  $this->beginContent('//'.$deploy_dir.'/' . $resPath);
  $this->endContent();
  ?>

  <style type="text/css">
    .loading-mask {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 30000;
      background-color: #FFF;
      text-align: center;
    }
    .loading-mask .x-loading {
      background: url(themes/public/images/common/loading.GIF);
      width: 128px;
      height: 32px;
      position: absolute;
      left: 50%;
      margin-left: -64px;
      top: 50%;
      margin-top: -16px;
    }
    #wrap{
      position: relative;
    }
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
    .clearfix{display:block;zoom:1}
    /*头部样式*/
    #hd *{
      -webkit-box-sizing: content-box;
      box-sizing: content-box;

    }
    #ft *{
      -webkit-box-sizing: content-box;
      box-sizing: content-box;
    }
    #hd .container{width:1000px;height:50px;margin:0 auto;}
    #hd {height:50px;background:#fff;z-index: 360;position: relative;}
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
<!--合作方内容 开始-->
<div id="wrap" >
  <?= $content; ?>
  <div class="loading-mask">
    <i class="x-loading"></i>
  </div>
</div>
<!--合作方内容结束-->
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
<script src="http://s1.qhimg.com/!12e1a56b/monitor_go_zt.js"></script>
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
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://hiworld.hitour.cc/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();

</script>
<noscript><p><img src="http://hiworld.hitour.cc/piwik/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
<script src="http://stat.union.360.cn/js/qunion.js"></script>
<script>
  $('body').delegate('a', 'click', function() {
    QUnion
        .use('go', 'AD4Bl1ORwfxu3ZNEy7kcPLhorKJYHp9MS8aI_ged-mWQjb2qFX5vnGU0Csiz6tVT')
        .sendLog({
          to: 69
        });
  });
</script>
</body>
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

</html>
