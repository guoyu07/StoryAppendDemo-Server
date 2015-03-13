<script type="text/ng-template" id="editProductRelated.html">
  <div class="edit-section last clearfix">
    <sidebar name='editProductRelated'></sidebar>
    <section class="col-xs-13 col-xs-offset-1 section-action">
      <div class="row edit-heading">
        <h2>请输入相关的商品ID</h2>
      </div>
      <div class="row edit-body">
        <div class="link-album clearfix">
          <div class="form-group col-xs-4">
            <input type="number" data-ng-model="local.input_product" min="1" placeholder="商品ID" class="form-control">
          </div>
          <button class="btn btn-inverse btn-sharp" data-ng-click="addProductRelated()">关联商品 <span
              class="glyphicon glyphicon-refresh refresh-animate"
              data-ng-show="local.check_product_progress == true"></span></button>
        </div>
        <div class="one-location-group-selection one-passenger-edit-box input-section">
          <button class="btn one-criteria one-allcriteria criteria-with-x" data-ng-repeat="product in data" data-ng-click="delProductRelated(product.product_id)">
            {{product.name}}
          </button>
        </div>
      </div>
    </section>
  </div>
</script>