<script type="text/ng-template" id="editProductRules.html">
  <form name="product_rules_form" novalidate>
    <div data-ng-include="'editProductSaleRange.html'"></div>
    <div data-ng-include="'editProductRedeem.html'"></div>
    <div data-ng-include="'editProductReturn.html'"></div>
  </form>
  <button type="submit" class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form"
          data-ng-click="submitChanges()" data-ng-disabled="product_rules_form.$invalid">
    保存
  </button>
</script>


<script type="text/ng-template" id="editProductSaleRange.html">
  <div class="edit-section">
    <sidebar name='editProductSaleRange'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action">
      <div class="row edit-heading with-dot">
        <h2>客户是否需要提前购买？</h2>
      </div>
      <div class="row edit-body">
        <radio-switch options="radio_options.buy_in_advance" model="local_date_limit"></radio-switch>
        <label class="inline-input" data-ng-if="local_date_limit.buy_in_advance == 1">
          客户需要提前
            <input type="number"
                       min="1"
                       name="buy_in_advance_str"
                       data-ng-model="local_date_limit.buy_in_advance_str"
                       class="form-control input-sm" />个
          <radio-switch options="radio_options.day_type" model="local_date_limit"></radio-switch>
          购买</label>
      </div>
      <div class="row edit-heading with-dot">
        <h2>是否立即发货？</h2>
      </div>
      <div class="row edit-body">
        <radio-switch options="radio_options.lead_time" model="local_date_limit"></radio-switch>
        <label class="inline-input" data-ng-if="local_date_limit.lead_time == 1">
          将会在<input type="number" min="1"
                    name="lead_time_str"
                    data-ng-model="local_date_limit.lead_time_str"
                    class="form-control input-sm" />个
          <radio-switch options="radio_options.shipping_day_type" model="local_date_limit"></radio-switch>
          发货</label>
      </div>
        <div class="row edit-heading with-dot">
            <h2>最远购买时间</h2>
        </div>
        <div class="row edit-body">
            <radio-switch options="radio_options.limit_sale_range" model="local_date_limit"></radio-switch>
            <label class="inline-input" data-ng-if="local_date_limit.limit_sale_range == 1">
                可购买<btn-select options="select_options.sale_range" model="local_date_limit.sale_range"></btn-select>
                内的本产品</label>
        </div>


    </section>
    <div class="clearfix"></div>
  </div>
</script>

<script type="text/ng-template" id="editProductRedeem.html">
  <div class="edit-section">
    <sidebar name='editProductRedeem'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action padding-top">
      <div class="input-section">
        <h4>用户付款后，在什么时候兑换有效</h4>

        <div class="edit-body input-group button-select">
          <radio-switch options="radio_options.redeem_type" model="redeem_limit"></radio-switch>
          <div data-ng-if="redeem_limit.redeem_type == 3" class="outline-input">
            用户需要在
            <input type="text" class="form-control" datepicker-popup="yyyy-MM-dd"
                   data-ng-model="redeem_limit.expire_date" is-open="redeem_opened" data-ng-click="redeem_opened = true"
                   data-ng-required="true" close-text="关闭" show-weeks="false" show-button-bar="false" />
            之前兑换有效
          </div>
          <div data-ng-if="redeem_limit.redeem_type == 2" class="outline-input">
            从购买日起，
            <btn-select options="select_options.redeem_duration" model="redeem_duration_type2"></btn-select>
            之前兑换有效
          </div>
          <div data-ng-if="redeem_limit.redeem_type == 4" class="outline-input">
            在用户选择的使用日期后
            <btn-select options="select_options.redeem_duration" model="redeem_duration_type4"></btn-select>
            兑换有效
          </div>
        </div>
      </div>
      <div class="input-section">
        <h4>使用有效期</h4>

        <div class="edit-body outline-input">
          用户兑换后，<input name="usage_limit" type="text" data-ng-model="redeem_limit.usage_limit"
                       class="form-control input-sm col-xs-9" placeholder="24小时"> 内使用有效
        </div>
      </div>
    </section>
    <div class="clearfix"></div>
  </div>
</script>
<script type="text/ng-template" id="editProductReturn.html">
  <div class="edit-section last">
    <sidebar name='editProductReturn'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action padding-top">
      <div class="row edit-heading with-dot no-top-space">
        是否可以退款？
      </div>
      <div class="row edit-body">
        <radio-switch options="radio_options.return_type" model="return_limit"></radio-switch>
        <div data-ng-if="return_limit.return_type != 0" class="outline-input">
          兑换截止日期前
          <btn-select options="select_options.return_offset" model="return_duration"></btn-select>
          可退换
        </div>
      </div>
    </section>
    <div class="clearfix"></div>
  </div>
</script>