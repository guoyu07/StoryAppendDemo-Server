<script type="text/ng-template" id="ProductRedeem.html">
    <div class="row grid-bottom">
        <label class="col-md-2 col-md-offset-11">新版数据</label>
        <div class="col-md-5">
            <hi-radio-switch options="local.radio_options.status" model="data"></hi-radio-switch>
        </div>
    </div>
    <div class="states-section">
        <hi-tab options="local.tab_options"></hi-tab>

        <?php
        $sub_path = __DIR__ . '/modules/';
        include_once( $sub_path . 'place/place.php' );
        include_once( $sub_path . 'usage/usage.php' );
        ?>
    </div>
</script>