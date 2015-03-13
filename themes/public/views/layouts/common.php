<!DOCTYPE html>
<html>
<!---

        //\    //\   //\   //////////\   ////////\    //\   //\    /////////\
       //\/   //\/  //\/   \\\//\\\\\/  //\\\\//\/   //\/  //\/   ///    //\/
      /////////\/  //\/      //\/      //\/  //\/   //\/  //\/   /////////\/
     //\/\\\//\/  //\/      //\/      //\/  //\/   //\/  //\/   //\/ //\\\/
    //\/   //\/  //\/      //\/      ////////\/   ////////\/   //\/  \/\\
    \\/    \\/   \\/       \\/       \\\\\\\\/    \\\\\\\\/    \\/    \/\\

!-->
<head>
  <?php
  $this->beginContent('//layouts/commonHead');
  $this->endContent();
  ?>

  <!--globals definition-->
  <script type="text/javascript">
    var $header_info = eval('('+ '<?php echo !empty($this->header_info) ? json_encode($this->header_info) : json_encode(array()); ?>' +')');
    var $request_urls = eval( '('+'<?php echo !empty($this->request_urls) ? json_encode($this->request_urls) : json_encode(array()); ?>' +')');
    var $current_page = '<?= $this->current_page; ?>';
  </script>
  <!--resource locate-->
  <?php
  $deploy_dir=Yii::app()->params['DEPLOY_STATE']=='release'?'resource.release':'resource';
  $this->beginContent('//'.$deploy_dir.'/common.res');
  $this->endContent();
  if (isset($this->resource_refs)) {
    $resPath = $this->resource_refs;
  } else {
    $resPath = strtolower(str_replace('Controller', '', get_class($this))) . '.res';
  }
  $this->beginContent('//'.$deploy_dir.'/' . $resPath);
  $this->endContent();
  ?>
</head>

<body>
    <div class="loading-mask">
        <i class="x-loading"></i>
    </div>
<?php if(!empty($this->staticData)&&Yii::app()->params['DATA_STATICIZE']){?>
<section class="data-section">
 <?php foreach($this->staticData as $url=>$data){?>
     <div id="<?php echo $url?>"><?php echo $data?></div>
 <?php } ?>
</section>
<?php } ?>
  <?php include_once( 'new_header.php' ); ?>
  <?= $content; ?>
  <?php include_once( 'footer.php' ); ?>
  <?php include_once( 'overlay.php' ); ?>
<script  src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=2709461202" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="http://qzonestyle.gtimg.cn/qzone/openapi/qc_loader.js" data-appid="100547865" data-redirecturi="http://<?=$_SERVER['HTTP_HOST']?>" charset="utf-8"></script>

<script type="text/javascript">
    //baidu statistics
    var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
    document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F91856bd91b936d99158ba9b6b7020a48' type='text/javascript'%3E%3C/script%3E"));
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?ee329a05c1a41d05df5ff1b70ee592a2";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();

    //google statistics
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-44367282-1', 'auto');
    ga('send', 'pageview');



  </script>
    <!-- Piwik -->
    <script type="text/javascript">
        var _paq = _paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//113.31.82.135/piwik/";
            _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['setSiteId', 1]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <noscript><p><img src="//113.31.82.135/piwik/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
    <!-- End Piwik Code -->
</body>

</html>
