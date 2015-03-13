<script type="text/ng-template" id="editProductImage.html">
  <div class="edit-section last clearfix product-image">
    <section class="col-xs-16 col-xs-offset-1 section-action">
      <div class="row edit-heading text-center">
        <h2><span class="glyphicon glyphicon-camera"></span>&nbsp;商品样图</h2>
        <p>请上传商品的实例图，以便用户参考</p>
      </div>
      <div class="image-container edit-body grid-bottom">
        <div class="one-image-container sample-image grid-bottom" data-ng-controller="imageUploadSampleCtrl">
          <input id="sample-upload" type="file" data-ng-file-select accept="image/png, image/jpeg" />

          <div data-ng-show="data.has_sample == true">
            <div class="image-holder">
              <img data-ng-src="{{data.sample_image.image_url}}"
                   data-ng-hide="sample_uploader.queue[0].isUploading == true" />
              <div class="progress overlay-progress" data-ng-show="sample_uploader.queue[0].isUploading == true">
                <div class="progress-bar" data-ng-style="{ 'width': sample_uploader.progress + '%' }">
                </div>
              </div>
              <div class="overlay" data-ng-click="triggerSampleUpload()">
                更改图片&nbsp;<span class="glyphicon glyphicon-share-alt"></span>
              </div>
            </div>
          </div>
          <div data-ng-show="data.has_sample == false">
            <span class="glyphicon glyphicon-plus" data-ng-click="triggerSampleUpload()"></span>
          </div>
        </div>
      </div>
      <div class="row edit-heading text-center">
        <h2><span class="glyphicon glyphicon-picture"></span>&nbsp;轮播图片</h2>
        <p>我们已经提供景点专辑内的图片<br />
           你也可以上传新的图片</p>
      </div>
      <div class="image-container edit-body grid-bottom">
        <div data-ng-controller="imageUploadCarouselCtrl" style="display: inline-block;">
          <div class="one-image-container carousel-image grid-bottom">
            <span class="glyphicon glyphicon-plus" data-ng-click="triggerCarouselUpload()"></span>
            <input id="carousel-upload" type="file" data-ng-file-select multiple accept="image/png, image/jpeg" />
          </div>
          <div class="one-image-container carousel-image grid-bottom"
               data-ng-if="carousel_uploader.isUploading == true">
            <div class="progress overlay-progress">
              <div class="progress-bar" data-ng-style="{ 'width': carousel_uploader.progress + '%' }">
              </div>
            </div>
          </div>
        </div>
        <div class="one-image-container carousel-image grid-bottom" data-ng-repeat="image in data.carousel_images"
             data-index="{{ $index }}" dnd-sortable item="image" callback="dndCallback(info, dstIndex)"
             options="dndOptions">
          <div class="image-holder">
            <img data-ng-src="{{image.image_url}}?imageView/5/w/220/h/145/" />
            <span class="image-order" data-ng-bind="$index + 1"></span>
						<span class="cover-triangle" data-ng-show="image.product_image_id == local.cover_img_id">
							<span class="glyphicon glyphicon-heart"></span>
						</span>
            <span class="triangle"></span>
            <div class="overlay">
              <div class="overlay-button glyphicon glyphicon-heart-empty"
                   data-ng-click="setAsCover( image.product_image_id )"></div>
              <div class="overlay-button glyphicon glyphicon-trash" data-ng-click="delImage( image )"></div>
            </div>
          </div>
          <div class="image-info" data-ng-show="image.editing == false">
            <h3 data-ng-bind="image.name"></h3>
            <p data-ng-bind="image.short_desc"></p>
          </div>
          <div class="image-info" data-ng-show="image.editing == true">
            <input class="image-title form-control" data-ng-model="image.name" />
            <textarea class="image-desc form-control" data-ng-model="image.short_desc"></textarea>
          </div>
					<span class="glyphicon toggle-edit" data-ng-show="image.image_usage == '1'"
                data-ng-class="{ 'glyphicon-edit' : image.editing == false, 'glyphicon-ok' : image.editing == true }"
                data-ng-click="toggleState( image )"></span>
        </div>
      </div>
      <div class="horizontal-line" data-ng-show="data.has_album">
        <p>
          景点关联图片删除后将会移到以下区域<br />
          自己上传的图片则彻底删除
        </p>
      </div>
      <div class="image-container edit-body grid-bottom" data-ng-show="data.has_album">
        <div class="one-image-container album-image grid-bottom" data-ng-repeat="image in data.album_images"
             data-ng-hide="local.selected_album_images.indexOf( image.landinfo_id ) > -1"
             data-ng-click="setAsSelected( image.landinfo_id )">
          <div class="image-holder">
            <img data-ng-src="{{image.image_url}}?imageView/5/w/220/h/145/" />
            <span class="triangle"></span>
          </div>
          <div class="image-info">
            <h3 data-ng-bind="image.name"></h3>
          </div>
        </div>
      </div>
    </section>
  </div>
</script>