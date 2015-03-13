<script type="text/ng-template" id="editProductPrice.html">
  <div id="product-price-container">
    <div class="nav-row text-center grid-top">
      <div class="btn-group">
        <button class="btn btn-primary" data-ng-click="changePricePage( key )" data-ng-class="{ 'selected': current_menu == key }" data-ng-repeat="(key, item) in menus">{{key}}. {{item.label}}<span class="fui-star" data-ng-show="item.alert == true"></span></button>
      </div>
    </div>
    <div data-ng-include="'editPriceAttribute.html'" data-ng-show="current_menu == '1'"></div>
    <div data-ng-include="'editPriceOption.html'" data-ng-show="current_menu == '2'"></div>
    <div data-ng-include="'editPricePlan.html'" data-ng-show="current_menu == '3' || current_menu == '5'"></div>
    <div data-ng-include="'editDeparturePoint.html'" data-ng-show="current_menu == '4'"></div>
    <!--<div data-ng-include="'editPricePlan.html'" data-ng-show="current_menu == '5'"></div>-->
  </div>
</script>

<?php include_once __DIR__ . "/price/attribute.php" ?>
<?php include_once __DIR__ . "/price/option.php" ?>
<?php include_once __DIR__ . "/price/plan.php" ?>
<?php include_once __DIR__ . "/price/departure_point.php" ?>
<?php //include_once __DIR__ . "/price/special.php" ?>