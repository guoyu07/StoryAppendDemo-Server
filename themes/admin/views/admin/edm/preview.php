<?php $image_dir = '/themes/admin/views/admin/edm/images/'; ?>
<div id="edm-preview-container" class="page-container" ng-controller="EDMPreviewCtrl">
    <div style="width: 640px; margin: 0 auto;">
        <table id="logo-container" border="0" cellspacing="0" class="editable">
            <tbody>
                <tr style="height: 100px;">
                    <td style="text-align: center;">
                        <img src="<?= $image_dir ?>logo_big.png" width="162" height="45" alt="玩途自由行" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="head-container" border="0" cellspacing="0" ng-style="local.style">
            <tbody>
                <tr style="height: 160px;">
                    <td style="text-align: center;">
                        <a ng-href="{{ data.base.title_link }}" style="text-decoration: none; color: #000000;">
                            <span style="font-size: 54px; line-height: 74px;" ng-bind="data.base.title"></span>
                            <br />
                            <span style="font-size: 26px; line-height: 26px;" ng-bind="data.base.small_title"></span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        <p style="margin: 40px 0; padding: 0 40px; font-size: 20px; line-height: 48px;"
                           ng-bind-html="data.base.description">
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="set-container" border="0" cellspacing="0" ng-repeat="group in data.groups" ng-class-odd="'odd'"
               ng-class-even="'even'">
            <tbody>
                <tr>
                    <td style="text-align: center;">
                        <p style="margin: 45px 0 60px 0; font-size: 32px; line-height: 32px;">
                            <a ng-href="{{ group.title_link }}" style="text-decoration: none; color: #000000;"
                               ng-bind="group.title"></a>
                        </p>
                    </td>
                </tr>
                <tr ng-repeat="product in group.group_products">
                    <td>
                        <table class="set-products-container" border="0" cellspacing="0">
                            <tr>
                                <td style="padding: 0 30px;">
                                    <a style="text-decoration: none;" title="{{ product.product_name }}"
                                       href="{{ product.product_link }}" target="email">
                                        <img ng-src="{{ product.product_image }}?imageView2/5/w/580/h/250" width="580"
                                             height="250" alt="{{ product.product_name }}"
                                             style="width: 580px; height: 250px;" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 40px 20px 40px;">
                                    <p style="color: #000000; font-size: 26px; font-weight: bolder; line-height: 32px; margin: 12px 0; padding: 0;">
                                        <a href="{{ product.product_link }}"
                                           style="text-decoration: none; color: #000000;"
                                           ng-bind="product.product_name">
                                        </a>
                                    </p>
                                    <p style="color: #777777; font-size: 20px; line-height: 30px; margin: 0; padding: 0;">
                                        <a href="{{ product.product_link }}"
                                           style="text-decoration: none; color: #777777;"
                                           ng-bind="product.product_description">
                                        </a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 30px 40px 30px; text-align: right;">
						<span style="vertical-align: middle;">
							<span
                                style="font-size: 14px; line-height: 14px; color: #c1c1c1; text-decoration: line-through; vertical-align: baseline;">{{product.orig_price}}元</span>
							<span style="font-size: 16px; line-height: 30px; color: #ff6600; vertical-align: baseline;">
								<span style="font-size: 30px;" ng-bind="product.price"></span>
								元起
							</span>
						</span>
                                    &nbsp;&nbsp;
                                    <a href="{{ product.product_link }}"
                                       style="color: #ffffff; padding: 10px 24px; background: #ff6600; text-decoration: none;">去看看</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="foot-container"
               style="width: 640px; border-collapse: collapse; border: 0; font-family: 微软雅黑, 'Hiragino Sans GB', 'Microsoft YaHei', 'WenQuanYi Micro Hei', sans-serif; background: #f9f9f9; border-bottom: 1px solid #c1c1c1;"
               border="0" cellspacing="0">
            <tbody>
                <tr style="height: 32px;">
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td style="width: 90px;"></td>
                    <td style="width: 150px; text-align: center;">
                        <img src="<?= $image_dir ?>wechat.png" width="139" height="139" alt="玩途官方微信" />
                        <p style="color: #525252; font-size: 24px; line-height: 24px; margin: 20px 0 15px 0; padding: 0;">
                            微信客服</p>
                        <p style="color: #525252; font-size: 18px; line-height: 30px; margin: 0; padding: 0;">
                            关注玩途微信<br />
                            获得最新优惠资讯
                        </p>
                    </td>
                    <td style="width: 60px;"></td>
                    <td style="width: 340px;">
                        <table id="contact-container"
                               style="width: 340px; border-collapse: collapse; border: 0; font-family: 微软雅黑, 'Hiragino Sans GB', 'Microsoft YaHei', 'WenQuanYi Micro Hei', sans-serif;"
                               border="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="<?= $image_dir ?>weibo.png" width="61" height="60"
                                             alt="玩途官方微博" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="http://weibo.com/u/3211176940" title="玩途官方微博"
                                           style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">@玩途</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">关注玩途获取最新动态</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr style="height: 25px;">
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="<?= $image_dir ?>phone.png" width="60" height="60"
                                             alt="玩途官方电话" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="tel:4000101900" title="玩途官方电话" style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">400-010-1900</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">400-010-1900</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr style="height: 25px;">
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width: 60px;">
                                        <img src="<?= $image_dir ?>email.png" width="60" height="60"
                                             alt="玩途官方邮箱" />
                                    </td>
                                    <td style="width: 20px;">
                                    </td>
                                    <td style="width: 260px;">
                                        <a href="mailto:service@hitour.cc" title="玩途官方邮箱"
                                           style="text-decoration: none;">
                                            <span style="font-size: 22px; color: #000000;">service@hitour.cc</span>
                                            <br />
                                            <span style="font-size: 18px; color: #999999;">发送意见反馈</span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr style="height: 32px;">
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>
        <table id="subscribe-container"
               style="width: 640px; border-collapse: collapse; border: 0; font-family: 微软雅黑, 'Hiragino Sans GB', 'Microsoft YaHei', 'WenQuanYi Micro Hei', sans-serif;"
               border="0" cellspacing="0">
            <tbody>
                <tr>
                    <td style="font-size: 16px; line-height: 24px; color: #525252; text-align: center; padding: 30px 0;">
                        这是一封自动产生的邮件，请勿回复！<br />
                        如果您对玩途的邮件内容感兴趣可以
                        <a style="color: #525252;" title="订阅玩途邮件" href="{$PLUGINLINK=subscribe}"
                           target="email">点击这里订阅</a>。<br />
                        如果您不想再收到此类邮件，请<a style="color: #525252;" href="{$PLUGINLINK=unsubscribe}"
                                         target="email">点击这里退订</a>。
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>