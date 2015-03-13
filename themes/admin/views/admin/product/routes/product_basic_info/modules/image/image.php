<div class="states-section info-image" ng-if="local.tab_options.current_tab.path == 'image'">
    <div ng-controller="InfoImageCtrl">
        <div class="images-container text-center">
            <div class="row">
                <h2><span class="i i-camera"></span>&nbsp;商品样图</h2>
                <p class="small-desc">请上传商品的实例图，以便用户参考</p>
            </div>
            <div class="one-image-container sample-image grid-bottom">
                <hi-uploader options="local.uploader_options.sample"></hi-uploader>
            </div>
        </div>
        <div class="carousel images-container clearfix text-center">
            <div class="row">
                <h2><span class="i i-picture"></span>&nbsp;轮播图片</h2>
                <p class="small-desc">
                    我们已经提供景点专辑内的图片<br />
                    你也可以上传新的图片
                </p>
            </div>
            <hi-multiple-uploader options="local.uploader_options.carousel"></hi-multiple-uploader>
            <div class="carousel">
                <div class="one-image-container add-image grid-bottom">
                    <span class="i i-plus" ng-click="triggerCarouselUpload()"></span>
                </div>
                <div class="one-image-container add-image grid-bottom" ng-show="local.uploader_options.carousel.in_progress">
                    <div class="progress overlay-progress">
                        <div class="progress-bar" data-ng-style="{ 'width': local.uploader_options.carousel.uploader.progress + '%' }"></div>
                    </div>
                </div>
                <div class="one-image-container carousel-image grid-bottom" ng-repeat="image in data.carousel_images track by $index" hi-dnd item="image" callback="local.dnd.callback( info, dst_index )" options="local.dnd.options" data-index="{{ $index }}">
                    <div class="image-holder">
                        <img data-ng-src="{{ image.image_url }}?imageView/5/w/218/h/145/" />
                        <span class="image-order" ng-bind="$index + 1"></span>
                    <span class="cover-triangle" ng-show="image.as_cover == 1">
                        <span class="i i-heart-filled"></span>
                    </span>
                        <span class="triangle"></span>
                        <div class="image-overlay">
                            <div class="overlay-button i i-heart-empty" ng-click="selectCoverImage($index)"></div>
                            <div class="overlay-button i i-trash" ng-click="deleteCarouselImage($index)"></div>
                        </div>
                    </div>
                    <div class="image-info" ng-hide="image.edit">
                        <h3 ng-bind="image.name"></h3>
                        <p class="small-desc" ng-bind="image.short_desc"></p>
                    </div>
                    <div class="image-info" ng-show="image.edit">
                        <input class="image-title form-control" ng-model="image.name" />
                        <textarea class="image-desc form-control" ng-model="image.short_desc"></textarea>
                    </div>
                    <span class="i toggle-edit" ng-show="image.image_usage == 1" ng-class="{ 'i-edit' : image.edit == false, 'i-save' : image.edit == true }" ng-click="toggleState($index)"></span>
                </div>
            </div>
        </div>
        <div class="images-container" ng-show="local.has_album">
            <div class="horizontal-line">
                <p class="small-desc">
                    景点关联图片删除后将会移到以下区域<br />
                    自己上传的图片则彻底删除
                </p>
            </div>
            <div class="one-image-container album-image grid-bottom" ng-repeat="image in data.album_images" ng-hide="local.selected_album_images.indexOf( image.landinfo_id ) > -1" ng-click="selectAlbumImage( $index )">
                <div class="image-holder">
                    <img ng-src="{{ image.image_url }}?imageView/5/w/218/h/145/" />
                    <span class="triangle"></span>
                </div>
                <div class="image-info">
                    <h3 data-ng-bind="image.name"></h3>
                </div>
            </div>
        </div>
    </div>
</div>