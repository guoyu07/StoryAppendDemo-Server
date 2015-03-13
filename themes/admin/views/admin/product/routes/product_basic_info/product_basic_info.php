<script type="text/ng-template" id="ProductBasicInfo.html">
    <hi-tab options="local.tab_options"></hi-tab>

    <?php
    $sub_path = __DIR__ . '/modules/';
    include_once( $sub_path . 'name/name.php' );
    include_once( $sub_path . 'city/city.php' );
    include_once( $sub_path . 'tag/tag.php' );
    include_once( $sub_path . 'image/image.php' );
    include_once( $sub_path . 'location/location.php' );
    ?>
</script>