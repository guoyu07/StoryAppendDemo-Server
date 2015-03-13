<?php
$sub_path = __DIR__ . '/modules/';
?>
<script type="text/ng-template" id="ProductPrice.html">
    <div class="states-section">
        <hi-tab options="local.tab_options"></hi-tab>

        <?php
        include_once( $sub_path . 'sale_attribute/sale_attribute.php' );
        include_once( $sub_path . 'special_code/special_code.php' );
        include_once( $sub_path . 'departure_point/departure_point.php' );
        include_once( $sub_path . 'price_plan/price_plan_list.php' );
        include_once( $sub_path . 'price_plan/edit_price_plan.php' );
        ?>
    </div>
</script>