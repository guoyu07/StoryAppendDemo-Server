<!doctype html>
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

    <meta charset="UTF-8">
    <title>Top Navigation</title>
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
        .fbg-about {
            width: 18px;
            margin-right: -15px;
            margin-bottom: 3px;
        }

        /*Base*/
        html { color:#544f4b; background:#FFF; font:12px "\5B8B\4F53", sans-serif;}
        body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, code, form, fieldset, legend, input, button, textarea, select, p, blockquote, th, td { margin:0; padding:0 }
        table { border-collapse:collapse; border-spacing:0 }
        fieldset, img { border:0 }
        address, button, caption, cite, code, dfn, em, input, optgroup, option, select, strong, textarea, th, var { font-style:normal; font-weight:normal }
        del, ins { text-decoration:none }
        li { list-style:none }
        caption, th { text-align:left }
        h1, h2, h3, h4, h5, h6 { font-size:100%; font-weight:normal }
        q:before, q:after { content:'' }
        abbr, acronym { border:0; font-variant:normal }
        sup { vertical-align:baseline }
        sub { vertical-align:baseline }
        legend { color:#000 }
        a { color:#333; text-decoration:none; }
        a:hover { text-decoration:none; }
        a{outline: none;}
        a:focus{outline:0;}
        input:focus { outline: none; }

        /*Top Nav*/
        .cf:before,.cf:after{content:"\0020";display:table;}
        .cf:after {clear:both;}
        .cf {zoom:1;}
        .tripTop{overflow: hidden;position: relative;z-index: 2;width: 100%;}
        .trip-top{height: 29px;background: #f4f4f4;border-bottom: 1px solid #bdbdbd;}
        .trip-top-bg{width: 100%;height: 30px;background: #f4f4f4;border-bottom: 1px solid #bdbdbd;position: absolute;z-index: 3;top: 0px;left: 0px;}
        .top-inner{position: relative;width: 1000px;margin: 0 auto;z-index: 4;overflow: hidden;height: 30px;}
        .top-l{float: left;line-height: 30px;}
        .top-l li{float: left;margin-right: 19px;}
        .top-r{float: right;line-height: 30px;}
        .top-r li{float: left;margin-left: 8px;}
        .tripTop a{color: #125397;}
        .tripTop a:hover{text-decoration: underline;}

        /*Footer*/
        #center_9 { width: 100%; }
        #footer { padding-top: 20px; width: 980px; height: 40px; margin: 0 auto; text-align: center; line-height: 40px; clear: both; position: relative; color: #125397; }
        #footer span,#footer .record { font-family: Arial,Helvetica,sans-serif; color: #FFF; }
        .foot_logo { width: 20px; height: 21px; position: absolute; left: 165px; top: 100px; background-position: 0px -29px; }
        #footer a { color: #125397; margin: 0; }
        #ft_about{ background: url(fbg_about.png) 0 0 no-repeat; padding-left: 18px; }

    </style>
</head>
<body>

    <!--top nav-->
    <div class="tripTop">
        <div class="trip-top-bg"></div>
        <div class="top-inner cf">
            <ul class="top-r cf">
                <li><a href="http://go.sogou.com/">返回旅游首页</a>&nbsp;|</li>
                <li><a href="javascript:addbm();">加入收藏</a>&nbsp;|</li>
                <li><a target="_blank" href="http://123.sogou.com/about/feedback.html">意见反馈</a></li>
            </ul>
            <ul class="top-l cf">
                <li><a href="http://123.sogou.com/" target="_blank">网址大全</a></li>
                <li><a href="http://kan.sogou.com/" target="_blank">电影</a></li>
                <li><a href="http://kan.sogou.com/teleplay/" target="_blank">电视剧</a></li>
                <li><a href="http://cp.sogou.com/" target="_blank">彩票</a></li>
                <li><a href="http://tuan.sogou.com/" target="_blank">团购</a></li>
                <li><a href="http://mai.sogou.com/" target="_blank">购物</a></li>
                <li><a href="http://app.sogou.com/" target="_blank">手机必备</a></li>
                <li><a href="http://xiaoshuo.sogou.com/" target="_blank">小说</a></li>
                <li><a href="http://haha.sogou.com/" target="_blank">笑话</a></li>
            </ul>
        </div>
    </div>

    <div id="wrap" >
        <?= $content; ?>
        <div class="loading-mask">
            <i class="x-loading"></i>
        </div>
    </div>

    <!--footer-->
    <div id="center_9" class="center" pbflag="footer"><div id="footer"><img class="fbg-about" src="themes/public/images/activities/fridaysale/sougou_about.png"><a href="http://123.sogou.com/about/" id="ft_about" target="_blank">关于我们</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://weibo.com/gosogou" target="_blank">官方微博</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="http://123.sogou.com/about/feedback.html">意见反馈</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.sogou.com/docs/terms.htm" target="_blank">免责声明</a>&nbsp;&nbsp;Copyright ©&nbsp;2015 Sogou.com.&nbsp;All Rights Reserved.&nbsp;<a href="http://www.miibeian.gov.cn/" target="_blank" id="ft_icp">京ICP证050897号</a><span class="foot_logo ct"></span></div></div>


    <script type="text/javascript">
        //加入收藏
        function addbm() {
            try {
                if (document.all) {
                    window.external.AddFavorite(document.URL, document.title)
                } else if (window.sidebar) {
                    window.sidebar.addPanel(document.title, document.URL, "")
                } else if (window.external) {
                    window.external.AddFavorite(document.URL, document.title)
                } else if (window.opera && window.print) {
                    return true
                }
            } catch (e) {
                alert("您好，您的浏览器不支持自动加入收藏，请使用浏览器菜单手动设置，祝您使用愉快！")
            }
        }
    </script>
    <script type="text/javascript">
        var spb_vars={"ptype":"travel","pcode":"hitourzt"};
    </script>

    <script type="text/javascript" src="http://d.123.sogou.com/u/pb/pb.692148.js?V=1"></script>

</body>
</html>