<div class="tab-content pr">
    <table style="margin-top:145px" class="table coupon-table table-order table-striped">
        <thead>
        <tr>
            <th style="width: 160px;">折扣方式</th>
            <th style="width:250px;">使用规则</th>
            <th style="width: 150px;">优惠码</th>
            <th style="width: 160px;">使用状态</th>
            <th style="width: 150px;">有效期</th>
        </tr>
        </thead>
        <tbody>
        <tr ms-repeat-coupon="couponList">
            <td class="text-center bold-td">{{ coupon.discount }}</td>
          <td class="text-center rule-td" ms-click="toggleCouponRuleList(coupon)">{{ coupon.rule|html }}</td>
            <td class="text-center numeric-text">{{ coupon.code }}</td>
            <td class="text-center" ms-class-1="green-label:coupon.used_times==0"
                ms-class-2="orange-label:coupon.used_times!=0">{{coupon.used_status}}</td>
            <td class="text-center" ms-class-1="green-label:coupon.expired==1"
                ms-class-2="orange-label:coupon.expired==0">{{ coupon.date_end }}</td>
        </tr>
        </tbody>

    </table>
  <div class="coupon-rule-list" ms-visible="showCouponRuleList">
    <div class="inner-border">
      <ul>
        <li ms-repeat-rule="couponRuleList"><a target="_blank" ms-href="rule.url">{{rule.name}}</a></li>
      </ul>
    </div>
    <div class="arrow"></div>
  </div>
</div>