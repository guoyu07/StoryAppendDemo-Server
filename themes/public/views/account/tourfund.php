<div class="main-wrap">

  <div class="top-ctn">

  </div>
  <div class="rule-ctn">
    <div class="rule-desc">
      分享玩途50元优惠礼券给你的好友，当你的好友通过玩途旅行时，您将获得50元旅行基金。
    </div>
    <div class="rule-item-ctn">
      <div class="rule-item icon-gift-circle-bg">
        <h3>赠礼</h3>

        <p>当您在玩途下单后，<br>
          即可获得50元优惠礼券</p>
      </div>
      <div class="rule-item icon-share-circle-bg">
        <h3>分享</h3>

        <p>给好友发送<br>
          50元优惠礼券</p>
      </div>
      <div class="rule-item icon-rmb-circle-bg">
        <h3>赚取</h3>

        <p>您的好友下单后，您将<br>
          会得到50元旅行基金</p>
      </div>
    </div>
  </div>
  <div class="fund-content" ms-controller="fund">
    <div class="fund-status">
      <div class="left-wrap status-wrap">
        <p>您的旅行基金</p>

        <p><em>{{fund_total}}</em><i>元</i><a href="javascript:;">使用方法</a></p>
      </div>
      <div class="right-wrap status-wrap">
        <p>剩余{{remain}}个优惠礼券可分享，还可赚取</p>

        <p><em>{{available}}</em><i>元</i></p>
      </div>
    </div>
    <div class="fund-coupon-list">
      <table>
        <thead>
        <tr>
          <th width="255px">优惠礼券</th>
          <th class="m-th">领取状态</th>
          <th>赚取的旅游基金</th>
        </tr>
        </thead>
        <tbody>
        <tr ms-repeat-dl="dandelions">
          <td class="coupon-td">
            <div class="coupon-ticket">&yen;{{dl.discount}}</div>
          </td>
          <td ms-if="dl.shared>0"><i class="icon-smile-circle-bg"></i>您的{{dl.discount}}元优惠礼券已经被<strong>{{dl.shared}}</strong>个人领取 <a class="detail" href="">查看详情</a></td>
          <td ms-if="dl.shared==0"><i class="icon-cry-circle-bg"></i>小伙伴们还不知道呢 <a class="share-btn" href="javascript:;">分享</a></td>
          <td class="fund-td"><em>{{dl.fund_num}}</em>元</td>
        </tr>

        </tbody>
      </table>
    </div>
  </div>
</div>
<script>


</script>
