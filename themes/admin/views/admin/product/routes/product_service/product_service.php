<script type="text/ng-template" id="ProductService.html">
    <div class="states-section">
        <hi-tab options="local.tab_options"></hi-tab>

        <?php
        $sub_path = __DIR__ . '/modules/';
        include_once( $sub_path . 'include/include.php' );
        include_once( $sub_path . 'pass_other/pass_other.php' );
        include_once( $sub_path . 'pass_classic/pass_classic.php' );
        include_once( $sub_path . '_tourplan/_tourplan.php' );
        include_once( $sub_path . 'multi_day_general/multi_day_general.php' );
        ?>
    </div>
</script>