<?php

class ProductController extends AdminController
{
    public $product_detail = [];

    public function actionIndex()
    {
        $this->pageTitle = '商品搜索';

        $request_urls = array(
            'getCities' => $this->createUrl('product/getCities'),
            'getProducts' => $this->createUrl('product/getProducts'),
            'getSuppliers' => $this->createUrl('product/getSuppliers'),
            'edit' => $this->createUrl('product/detail', array('product_id' => '')),
            'gtaImportUrl' => $this->createUrl('import/index'),
            'productCheck' => $this->createUrl('productCheck/index'),
        );

        $qs = Yii::app()->request->getParam('qs', '');

        $cs = Yii::app()->getClientScript();
        $base_url = Yii::app()->theme->baseUrl;

        $cs->registerCssFile($base_url . '/bower_components/chosen/public/chosen.min.css');
        $cs->registerCssFile($base_url . '/bower_components/angular-chosen-localytics/chosen-spinner.css');

        $cs->registerScriptFile($base_url . '/bower_components/chosen/public/chosen.jquery.min.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/bower_components/angular-chosen-localytics/chosen.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/search.js', CClientScript::POS_END);

        $this->render('search', array(
            'request_urls' => $request_urls, 'qs' => $qs
        ));
    }

    public function actionEdit()
    {
        $this->pageTitle = '商品编辑';

        if (!Yii::app()->user->checkAccess('HT_ProductEdit')) {
            $this->redirect($this->createUrl('/'));
        }

        $product_id = $this->getProductID();

        $qs = Yii::app()->request->getParam('qs');

        $this->layout = '//layouts/fullwidth';

        $request_urls = array(
            'back' => $this->createUrl('product/index', array('qs' => $qs)),
            'edit' => $this->createUrl('product/edit', array('product_id' => '')),
            'editNew' => $this->createUrl('product/detail', array('product_id' => $product_id)),
            'getCities' => $this->createUrl('product/getCities'),
            'getSuppliers' => $this->createUrl('product/getSuppliers'),
            'getProduct' => $this->createUrl('product/getProductBasicInfo', array('product_id' => $product_id)),
            'copyProduct' => $this->createUrl('product/copy', array('product_id' => $product_id)),
            'getProductInfo' => $this->createUrl('product/getProductInfo', array('product_id' => $product_id)),
            'otherCity' => $this->createUrl('product/otherCity', array('product_id' => $product_id, 'city_code' => '')),
            'updateProductInfo' => $this->createUrl('product/updateProductInfo', array('product_id' => $product_id)),
            'addProductCombo' => $this->createUrl('product/addProductCombo', array('product_id' => $product_id)),
            'deleteProductCombo' => $this->createUrl('product/deleteProductCombo',
                                                     array('product_id' => $product_id)),
            'getProductQA' => $this->createUrl('product/getProductQA', array('product_id' => $product_id)),
            'updateProductQA' => $this->createUrl('product/updateProductQA', array('product_id' => $product_id)),
            'getProductDescription' => $this->createUrl('productDescription/getProductDescription',
                                                        array('product_id' => $product_id)),
            'updateProductDescription' => $this->createUrl('productDescription/updateProductDescription',
                                                           array('product_id' => $product_id)),
            'getProductAlbum' => $this->createUrl('product/getProductAlbum', array('product_id' => $product_id)),
            'updateProductAlbum' => $this->createUrl('product/updateProductAlbum', array('product_id' => $product_id)),
            'getProductPickTicketAlbum' => $this->createUrl('product/getProductPickTicketAlbum',
                                                            array('product_id' => $product_id)),
            'updateProductPickTicketAlbum' => $this->createUrl('product/updateProductPickTicketAlbum',
                                                               array('product_id' => $product_id)),
            'saveAlbumInfoAll' => $this->createUrl('product/saveAlbumInfoAll', array('product_id' => $product_id)),
            'savePickTicketMap' => $this->createUrl('product/savePickTicketMap', array('product_id' => $product_id)),
            'saveAlbumMap' => $this->createUrl('product/saveAlbumMap', array('product_id' => $product_id)),

            'getProductImages' => $this->createUrl('product/getProductImages', array('product_id' => $product_id)),
            'addOrUpdateProductSampleImage' => $this->createUrl('product/addOrUpdateProductSampleImage',
                                                                array('product_id' => $product_id)),
            'addProductImage' => $this->createUrl('product/addProductImage', array('product_id' => $product_id)),
            'addProductImageOfLandinfo' => $this->createUrl('product/addProductImageOfLandinfo',
                                                            array('product_id' => $product_id)),
            'deleteProductImage' => $this->createUrl('product/deleteProductImage', array('product_id' => $product_id)),
            'updateProductImage' => $this->createUrl('product/updateProductImage', array('product_id' => $product_id)),
            'updateProductImageOrder' => $this->createUrl('product/updateProductImageOrder',
                                                          array('product_id' => $product_id)),
            'productImageSetCover' => $this->createUrl('product/productImageSetCover',
                                                       array('product_id' => $product_id)),
            'getProductRelated' => $this->createUrl('product/getProductRelated', array('product_id' => $product_id)),
            'addProductRelated' => $this->createUrl('product/addProductRelated', array('product_id' => $product_id)),
            'deleteProductRelated' => $this->createUrl('product/deleteProductRelated',
                                                       array('product_id' => $product_id)),
            'changeEditingState' => $this->createUrl('product/changeEditingState', array('product_id' => $product_id)),
            'getLandinfos' => $this->createUrl('album/getAlbumLandinfos', array('album_id' => '')),
            'getPassengerMetaData' => $this->createUrl('product/getPassengerMetaData'),
            'getProductPassengerRule' => $this->createUrl('product/getProductPassengerRule',
                                                          array('product_id' => $product_id)),
            'updatePackagePassengerRule' => $this->createUrl('product/updatePackagePassengerRule',
                                                             array('product_id' => $product_id)),
            'updateProductPassengerRule' => $this->createUrl('product/updateProductPassengerRule',
                                                             array('product_id' => $product_id)),
            'postEditProductRule' => $this->createUrl('product/saveProductRule', array('product_id' => $product_id)),
            'getProductRules' => $this->createUrl('product/getProductRules', array('product_id' => $product_id)),
            'getVoucherRules' => $this->createUrl('product/getVoucherRules', array('product_id' => $product_id)),
            'updateVoucherRule' => $this->createUrl('product/updateVoucherRule', array('product_id' => $product_id)),
            'uploadAttachedPdf' => $this->createUrl('product/uploadAttachedPdf', array('product_id' => $product_id)),
            'deleteAttachedPdf' => $this->createUrl('product/deleteAttachedPdf', array('product_id' => $product_id)),

            'getShippingConfigurations' => $this->createUrl('product/getShippingConfigurations',
                                                            array('product_id' => $product_id)),
            'updateShippingConfigurations' => $this->createUrl('product/updateShippingConfigurations',
                                                               array('product_id' => $product_id)),
            // APIs for departure plan
            'departurePlans' => $this->createUrl('productDeparture/departurePlans', array('product_id' => $product_id)),
            'departurePlan' => $this->createUrl('productDeparture/departurePlan',
                                                array('product_id' => $product_id, 'departure_plan_id' => '')),
            'departurePoint' => $this->createUrl('productDeparture/departurePoint',
                                                 array('product_id' => $product_id, 'departure_code' => '')),

            // APIs for product special code editing
            'getProductSpecialCodes' => $this->createUrl('productPrice/getProductSpecialCodes',
                                                         array('product_id' => $product_id)),
            'saveProductSpecialCodes' => $this->createUrl('productPrice/saveProductSpecialCodes',
                                                          array('product_id' => $product_id)),
            'deleteProductSpecialCodes' => $this->createUrl('productPrice/deleteProductSpecialCodes',
                                                            array('product_id' => $product_id)),
            'productSpecialCode' => $this->createUrl('productPrice/productSpecialCode',
                                                     array('product_id' => $product_id, 'special_code' => '')),
            // APIs for product ticket rule
            'ticketRules' => $this->createUrl('productPrice/ticketRules', array('product_id' => $product_id)),
            'ticketTypes' => $this->createUrl('productPrice/ticketTypes', array('product_id' => $product_id)),
            // APIs for product date rule
            'getDateRule' => $this->createUrl('productPrice/getDateRule', array('product_id' => $product_id)),
            'saveDateRule' => $this->createUrl('productPrice/saveDateRule', array('product_id' => $product_id)),
            'productTourOperation' => $this->createUrl('productPrice/productTourOperation',
                                                       array('product_id' => $product_id, 'operation_id' => '')),

            // APIs for product price plan
            'productPricePlanBasicInfo' => $this->createUrl('productPrice/productPricePlanBasicInfo',
                                                            array('product_id' => $product_id)),
            'productPricePlans' => $this->createUrl('productPrice/productPricePlans',
                                                    array('product_id' => $product_id)),
            'productPricePlan' => $this->createUrl('productPrice/productPricePlan',

                                                   array('product_id' => $product_id, 'price_plan_id' => '')),
            // APIs for product price plan special
            'productPricePlanSpecials' => $this->createUrl('productPrice/productPricePlanSpecials',
                                                           array('product_id' => $product_id)),
            'productPricePlanSpecial' => $this->createUrl('productPrice/productPricePlanSpecial',
                                                          array('product_id' => $product_id, 'price_plan_id' => '')),

            // APIs for product sale rule
            'getSaleRule' => $this->createUrl('productSaleRule/getSaleRule',
                                              array('product_id' => $product_id)),
            'saveSaleRule' => $this->createUrl('productSaleRule/saveSaleRule',
                                               array('product_id' => $product_id)),
            //APIs for product Image Description

            'getTourPlanDetail' => $this->createUrl('productTourPlan/getTourPlanDetail',
                                                    array('product_id' => $product_id)),
            'addTourPlan' => $this->createUrl('productTourPlan/addTourPlan', array('product_id' => $product_id)),
            'addTourPlanItem' => $this->createUrl('productTourPlan/addTourPlanItem',
                                                  array('product_id' => $product_id)),
            'uploadImage' => $this->createUrl('productTourPlan/uploadImage', array('product_id' => $product_id)),
            'updateTourPlanItem' => $this->createUrl('productTourPlan/updateTourPlanItem',
                                                     array('product_id' => $product_id)),
            'deleteItem' => $this->createUrl('productTourPlan/deleteItem', array('item_id' => '')),
            'deleteImage' => $this->createUrl('productTourPlan/deleteImage', array('item_id' => '')),
            'deleteGroup' => $this->createUrl('productTourPlan/deleteGroup', array('group_id' => '')),
            'addTourPlanGroup' => $this->createUrl('productTourPlan/addTourPlanGroup',
                                                   array('product_id' => $product_id)),
            'updateTourPlanGroup' => $this->createUrl('productTourPlan/updateTourPlanGroup',
                                                      array('product_id' => $product_id)),
            'updateItemsOrder' => $this->createUrl('productTourPlan/updateItemsOrder',
                                                   array('product_id' => $product_id)),
            'insertTourPlanGroup' => $this->createUrl('productTourPlan/insertTourPlanGroup',
                                                      array('product_id' => $product_id)),
            'productSeo' => $this->createUrl('product/productSeo', array('product_id' => $product_id)),
            'uploadDeparturePoints' => $this->createUrl('productDeparture/uploadDeparturePoints',
                                                        array('product_id' => $product_id)),
            'getProductSaleDateRule' => $this->createUrl('product/getProductSaleDateRule',
                                                         array('product_id' => $product_id)),

            // APIs for product comment
            'productComments' => $this->createUrl('productComment/comments', array('product_id' => $product_id)),
            'productAddComment' => $this->createUrl('productComment/addComment', array('product_id' => $product_id)),
            'productEditComment' => $this->createUrl('productComment/editComment'),
            'productDeleteComment' => $this->createUrl('productComment/deleteComment',
                                                       array('product_id' => $product_id, 'comment_id' => '')),
            'productCommentStatInfo' => $this->createUrl('productComment/getStatInfo',
                                                         array('product_id' => $product_id)),
            'getRandomCustomer' => $this->createUrl('productComment/getCustomer', array('product_id' => $product_id)),

            'gtaImportUrl' => $this->createUrl('import/index'),
            'gtaImportAdd' => $this->createUrl('import/addImportProduct'),
            'gtaImportUpdate' => $this->createUrl('import/update'),

            //APIs for product coupon
            'productGiftCoupon' => $this->createUrl('coupon/productGiftCoupon',
                                                    array('product_id' => $product_id, 'id' => '')),
            'productCouponRule' => $this->createUrl('coupon/productCouponRule', array('product_id' => $product_id)),
            'getProductCouponTemplateList' => $this->createUrl('coupon/getProductGiftCouponList',
                                                               array('product_id' => $product_id)),
            'editCouponTemplateUrl' => $this->createUrl('coupon/template', array('template_id' => '')),
            'newCouponTemplateUrl' => $this->createUrl('coupon/template',
                                                       array('product_id' => $product_id, 'product_name' => 'xxx', 'template_id' => '')),

            //APIs for tag
            'getTags' => $this->createUrl('tag/getTags'),
            // Product Bundle
            'getBundleList' => $this->createUrl('productBundle/getBundleList', array('product_id' => $product_id)),
            'saveBundle' => $this->createUrl('productBundle/saveBundle'),
            'deleteBundle' => $this->createUrl('productBundle/deleteBundle', array('bundle_id' => '')),
            'deleteBundleProduct' => $this->createUrl('productBundle/deleteBundleProduct',
                                                      array('product_id' => $product_id, 'bundle_id' => 'bundle_000', 'binding_product_id' => 'product_000')),
            'bundleItemChangeOrder' => $this->createUrl('productBundle/bundleItemChangeOrder'),
            'getBundleHotelSpecial' => $this->createUrl('productBundle/getBundleHotelSpecial',
                                                        array('product_id' => $product_id)),

            // Product Hotel
            'rateSources' => $this->createUrl('productHotel/rateSources'),
            'serviceItems' => $this->createUrl('productHotel/serviceItems'),
            'bankcardItems' => $this->createUrl('productHotel/bankcardItems'),
            'hotelInfo' => $this->createUrl('productHotel/hotelInfo', array('product_id' => $product_id)),
            'hotelRoomType' => $this->createUrl('productHotel/hotelRoomType',
                                                array('product_id' => $product_id, 'room_type_id' => '')),
            'updateRoomImage' => $this->createUrl('productHotel/updateRoomImage',
                                                  array('room_type_id' => '', 'image_id' => '')),
            'deleteRoomImage' => $this->createUrl('productHotel/deleteRoomImage',
                                                  array('room_type_id' => '', 'image_id' => '')),
            'updateRoomImageOrder' => $this->createUrl('productHotel/updateRoomImageOrder'),
            'bedPolicy' => $this->createUrl('productHotel/bedPolicy', array('policy_id' => '')),

            // Trip Plan
            'changeToOnline' => $this->createUrl('tripPlan/changeToOnline', ['product_id' => $product_id]),
            'getTripPlan' => $this->createUrl('tripPlan/getTripPlan',
                                              ['product_id' => $product_id]), // get whole trip plan
            'planInfo' => $this->createUrl('tripPlan/planInfo',
                                           ['product_id' => $product_id, 'plan_id' => '']), // get, post, delete one day plan info
            'changePlanOrder' => $this->createUrl('tripPlan/changePlanOrder', ['product_id' => $product_id]),
            'savePlanPoints' => $this->createUrl('tripPlan/savePlanPoints', ['product_id' => $product_id]),
            'savePlanTraffic' => $this->createUrl('tripPlan/savePlanTraffic',
                                                  ['product_id' => $product_id, 'plan_id' => '']),
            'changePlanPointOrder' => $this->createUrl('tripPlan/changePlanPointOrder', ['plan_id' => '']),
            'addPlanPoint' => $this->createUrl('tripPlan/addPlanPoint', ['plan_id' => '']),
            'updatePlanPoint' => $this->createUrl('tripPlan/updatePlanPoint', ['point_id' => '']),
            'deletePlanPoint' => $this->createUrl('tripPlan/deletePlanPoint', ['point_id' => '']),
            'planPointImages' => $this->createUrl('tripPlan/planPointImages',
                                                  ['point_id' => '']), // get/update point images
            'planPointImage' => $this->createUrl('tripPlan/planPointImage',
                                                 ['point_id' => '']), // add/update point image

            //Product Sightseeing
            'productSightseeing' => $this->createUrl('product/productSightseeing', ['product_id' => $product_id]),
            'sightseeingDisplayOrder' => $this->createUrl('product/sightseeingDisplayOrder', ['product_id' => $product_id]),

            // Preview the direction.
            'viewMap' => $this->createUrl('site/viewMap'),

            // Product Introduction
            'productDetailInfo' => $this->createUrl('productDetail/detailInfo', ['product_id' => $product_id]),
        );

        $cs = Yii::app()->getClientScript();
        $base_url = Yii::app()->theme->baseUrl;

        $cs->registerCssFile($base_url . '/bower_components/chosen/public/chosen.min.css');
        $cs->registerCssFile($base_url . '/bower_components/angular-chosen-localytics/chosen-spinner.css');

        $cs->registerScriptFile($base_url . '/bower_components/chosen/public/chosen.jquery.min.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/bower_components/angular-chosen-localytics/chosen.js',
                                CClientScript::POS_END);

        $cs->registerScriptFile($base_url . '/bower_components/angular-route/angular-route.min.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/bower_components/angular-file-upload/angular-file-upload.min.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/bower_components/showdown/compressed/showdown.js',
                                CClientScript::POS_END);

        $cs->registerScriptFile($base_url . '/javascripts/product/edit/main.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/factories.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/directives.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/ProductEdit.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productInfo.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productRule.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productPrice.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/passengerMeta.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productDesc.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productTourPlan.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productImage.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productAlbum.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productRelated.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productQna.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/voucherRule.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/shippingConfig.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/departure_point.js',
                                CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/attribute.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/option.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/plan.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productSeo.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productComment.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productCoupon.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productBundle.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productHotel.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productTripPlan.js', CClientScript::POS_END);

        $cs->registerScriptFile($base_url . '/javascripts/product/edit/productHotelRoom.js', CClientScript::POS_END);

//        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/special.js', CClientScript::POS_END);
        $cs->registerScriptFile($base_url . '/javascripts/product/edit/price/departure_point.js',
                                CClientScript::POS_END);
        $this->render('edit', array(
            'request_urls' => $request_urls
        ));
    }

    public function actionDetail()
    {
        $this->pageTitle = '商品编辑';

        if (!Yii::app()->user->checkAccess('HT_ProductEdit')) {
            $this->redirect($this->createUrl('/'));
        }

        $qs = Yii::app()->request->getParam('qs');
        $product_id = $this->getProductID();
        $product_detail = HtProduct::model()->getProductDetail($product_id);
        if(empty($product_detail)) {
            $this->redirect($this->createUrl('/'));
        } else {
            $this->product_detail = ['product_id' => $product_id,
                'name' => $product_detail['name'],
                'city_cn_name' => $product_detail['city_cn_name'],
                'status' => $product_detail['status'],
                'type' => $product_detail['type'],
                'supplier_id' => $product_detail['supplier_id']
            ];
        }

        $city_info = HtProduct::model()->getProductCity($product_id);

        $request_urls = array(
            //Common
            'getProductSummary' => $this->createUrl('product/getProductBasicInfo', array('product_id' => $product_id)),
            'updateProductStatus' => $this->createUrl('product/changeEditingState', array('product_id' => $product_id)),

            //Breadcrumb
            'back' => $this->createUrl('product/index', array('query' => $qs)),
            'oldEdit' => $this->createUrl('product/edit', array('product_id' => $product_id)),
            'edit' => $this->createUrl('product/detail', array('product_id' => '')),
            'viewCity' => Yii::app()->urlManager->createUrl('city/index', ['city_name' => $city_info['city_name'], 'country_name' => $city_info['country_name']]),
            'copyProduct' => $this->createUrl('product/copy', array('product_id' => $product_id)),

            //基本信息 - 名称
            'getBasicInfo' => $this->createUrl('product/getProductInfo', array('product_id' => $product_id)),
            'updateBasicInfo' => $this->createUrl('product/updateProductInfo', array('product_id' => $product_id)),

            'addProductCombo' => $this->createUrl('product/addProductCombo', array('product_id' => $product_id)),
            'deleteProductCombo' => $this->createUrl('product/deleteProductCombo',
                                                     array('product_id' => $product_id)),

            'gtaImportUrl' => $this->createUrl('import/index'),
            'addGtaImport' => $this->createUrl('import/addImportProduct'),
            'updateGtaImport' => $this->createUrl('import/update'),
            'getExpertList' => $this->createUrl('product/getExpertList'),
            //基本信息 - 城市
            'otherCity' => $this->createUrl('product/otherCity', array('product_id' => $product_id, 'city_code' => '')),

            //基本信息 - 图片
            'getProductImages' => $this->createUrl('product/getProductImages', array('product_id' => $product_id)),
            'addOrUpdateProductSampleImage' => $this->createUrl('product/addOrUpdateProductSampleImage',
                                                                array('product_id' => $product_id)),
            'addProductImage' => $this->createUrl('product/addProductImage', array('product_id' => $product_id)),
            'addProductImageOfLandinfo' => $this->createUrl('product/addProductImageOfLandinfo',
                                                            array('product_id' => $product_id)),
            'deleteProductImage' => $this->createUrl('product/deleteProductImage', array('product_id' => $product_id)),
            'updateProductImage' => $this->createUrl('product/updateProductImage', array('product_id' => $product_id)),
            'updateProductImageOrder' => $this->createUrl('product/updateProductImageOrder',
                                                          array('product_id' => $product_id)),
            'productImageSetCover' => $this->createUrl('product/productImageSetCover',
                                                       array('product_id' => $product_id)),

            'getProductDescription' => $this->createUrl('productDescription/getProductDescription',
                                                        array('product_id' => $product_id)),
            'updateProductDescription' => $this->createUrl('productDescription/updateProductDescription',
                                                           array('product_id' => $product_id)),
            'getProductAlbum' => $this->createUrl('product/getProductAlbum', array('product_id' => $product_id)),
            'updateProductAlbum' => $this->createUrl('product/updateProductAlbum', array('product_id' => $product_id)),
            'getProductPickTicketAlbum' => $this->createUrl('product/getProductPickTicketAlbum',
                                                            array('product_id' => $product_id)),
            'updateProductPickTicketAlbum' => $this->createUrl('product/updateProductPickTicketAlbum',
                                                               array('product_id' => $product_id)),
            'saveAlbumInfoAll' => $this->createUrl('product/saveAlbumInfoAll', array('product_id' => $product_id)),
            'savePickTicketAlbumInfoAll' => $this->createUrl('product/savePickTicketAlbumInfoAll', ['product_id' => $product_id]),
            'savePickTicketMap' => $this->createUrl('product/savePickTicketMap', array('product_id' => $product_id)),
            'saveAlbumMap' => $this->createUrl('product/saveAlbumMap', array('product_id' => $product_id)),

            'getLandinfos' => $this->createUrl('album/getAlbumLandinfos', array('album_id' => '')),
            'getPassengerMetaData' => $this->createUrl('product/getPassengerMetaData'),
            'getProductPassengerRule' => $this->createUrl('product/getProductPassengerRule',
                                                          array('product_id' => $product_id)),
            'updatePackagePassengerRule' => $this->createUrl('product/updatePackagePassengerRule',
                                                             array('product_id' => $product_id)),
            'updateProductPassengerRule' => $this->createUrl('product/updateProductPassengerRule',
                                                             array('product_id' => $product_id)),
            'postEditProductRule' => $this->createUrl('product/saveProductRule', array('product_id' => $product_id)),
            'getProductRules' => $this->createUrl('product/getProductRules', array('product_id' => $product_id)),
            'getVoucherRules' => $this->createUrl('product/getVoucherRules', array('product_id' => $product_id)),
            'updateVoucherRule' => $this->createUrl('product/updateVoucherRule', array('product_id' => $product_id)),
            'uploadAttachedPdf' => $this->createUrl('product/uploadAttachedPdf', array('product_id' => $product_id)),
            'deleteAttachedPdf' => $this->createUrl('product/deleteAttachedPdf', array('product_id' => $product_id)),

            'getShippingConfigurations' => $this->createUrl('product/getShippingConfigurations',
                                                            array('product_id' => $product_id)),
            'updateShippingConfigurations' => $this->createUrl('product/updateShippingConfigurations',
                                                               array('product_id' => $product_id)),
            'getProductSaleDateRule' => $this->createUrl('product/getProductSaleDateRule',
                                                         array('product_id' => $product_id)),
            'deleteRoomImage' => $this->createUrl('productHotel/deleteRoomImage',
                                                  array('room_type_id' => '', 'image_id' => '')),
            // APIs for departure plan
            'departurePlans' => $this->createUrl('productDeparture/departurePlans', array('product_id' => $product_id)),
            'departurePlan' => $this->createUrl('productDeparture/departurePlan',
                                                array('product_id' => $product_id, 'departure_plan_id' => '')),
            'departurePoint' => $this->createUrl('productDeparture/departurePoint',
                                                 array('product_id' => $product_id, 'departure_code' => '')),

            // APIs for product special code editing
            'getProductSpecialCodes' => $this->createUrl('productPrice/getProductSpecialCodes',
                                                         array('product_id' => $product_id)),
            'saveProductSpecialCodes' => $this->createUrl('productPrice/saveProductSpecialCodes',
                                                          array('product_id' => $product_id)),
            'deleteProductSpecialCodes' => $this->createUrl('productPrice/deleteProductSpecialCodes',
                                                            array('product_id' => $product_id)),
            'productSpecialCode' => $this->createUrl('productPrice/productSpecialCode',
                                                     array('product_id' => $product_id, 'special_code' => '')),
            'productSpecialGroup' => $this->createUrl('productPrice/specialGroup', ['product_id' => $product_id]),
            'productSpecialItem' => $this->createUrl('productPrice/specialItem', ['group_id' => '']),
            'productSpecialGroupOrder' => $this->createUrl('productPrice/specialGroupOrder', ['product_id' => $product_id]),
            'productSpecialItemOrder' => $this->createUrl('productPrice/specialItemOrder', ['product_id' => $product_id, 'group_id' => '']),
            'updateSpecialItemStatus' => $this->createUrl('productPrice/updateSpecialItemStatus', ['product_id' => $product_id]),
            // APIs for product ticket rule
            'ticketRules' => $this->createUrl('productPrice/ticketRules', array('product_id' => $product_id)),
            'ticketTypes' => $this->createUrl('productPrice/ticketTypes', array('product_id' => $product_id)),
            // APIs for product date rule
            'getDateRule' => $this->createUrl('productPrice/getDateRule', array('product_id' => $product_id)),
            'saveDateRule' => $this->createUrl('productPrice/saveDateRule', array('product_id' => $product_id)),
            'productTourOperation' => $this->createUrl('productPrice/productTourOperation',
                                                       array('product_id' => $product_id, 'operation_id' => '')),

            // APIs for product price plan
            'productPricePlanBasicInfo' => $this->createUrl('productPrice/productPricePlanBasicInfo',
                                                            array('product_id' => $product_id)),
            'productPricePlans' => $this->createUrl('productPrice/productPricePlans',
                                                    array('product_id' => $product_id)),
            'productPricePlan' => $this->createUrl('productPrice/productPricePlan',

                                                   array('product_id' => $product_id, 'price_plan_id' => '')),
            // APIs for product price plan special
            'productPricePlanSpecials' => $this->createUrl('productPrice/productPricePlanSpecials',
                                                           array('product_id' => $product_id)),
            'productPricePlanSpecial' => $this->createUrl('productPrice/productPricePlanSpecial',
                                                          array('product_id' => $product_id, 'price_plan_id' => '')),

            // APIs for product sale rule
            'getSaleRule' => $this->createUrl('productSaleRule/getSaleRule',
                                              array('product_id' => $product_id)),
            'saveSaleRule' => $this->createUrl('productSaleRule/saveSaleRule',
                                               array('product_id' => $product_id)),
            //APIs for product Image Description

            'getTourPlanDetail' => $this->createUrl('productTourPlan/getTourPlanDetail',
                                                    array('product_id' => $product_id)),
            'addTourPlan' => $this->createUrl('productTourPlan/addTourPlan', array('product_id' => $product_id)),
            'addTourPlanItem' => $this->createUrl('productTourPlan/addTourPlanItem',
                                                  array('product_id' => $product_id)),
            'uploadImage' => $this->createUrl('productTourPlan/uploadImage', array('product_id' => $product_id)),
            'updateTourPlanItem' => $this->createUrl('productTourPlan/updateTourPlanItem',
                                                     array('product_id' => $product_id)),
            'deleteItem' => $this->createUrl('productTourPlan/deleteItem', array('item_id' => '')),
            'deleteImage' => $this->createUrl('productTourPlan/deleteImage', array('item_id' => '')),
            'deleteGroup' => $this->createUrl('productTourPlan/deleteGroup', array('group_id' => '')),
            'addTourPlanGroup' => $this->createUrl('productTourPlan/addTourPlanGroup',
                                                   array('product_id' => $product_id)),
            'updateTourPlanGroup' => $this->createUrl('productTourPlan/updateTourPlanGroup',
                                                      array('product_id' => $product_id)),
            'updateItemsOrder' => $this->createUrl('productTourPlan/updateItemsOrder',
                                                   array('product_id' => $product_id)),
            'insertTourPlanGroup' => $this->createUrl('productTourPlan/insertTourPlanGroup',
                                                      array('product_id' => $product_id)),

            ////使用描述
            //商品QnA
            'getProductQna' => $this->createUrl('product/getProductQA', array('product_id' => $product_id)),
            'updateProductQna' => $this->createUrl('product/updateProductQA', array('product_id' => $product_id)),

            ////商品运营
            //商品SEO
            'productSeo' => $this->createUrl('product/productSeo', array('product_id' => $product_id)),

            //相关商品
            'getProductRelated' => $this->createUrl('product/getProductRelated', array('product_id' => $product_id)),
            'addProductRelated' => $this->createUrl('product/addProductRelated', array('product_id' => $product_id)),
            'deleteProductRelated' => $this->createUrl('product/deleteProductRelated',
                                                       array('product_id' => $product_id)),

            //优惠券挂接
            'getProductCouponTemplateList' => $this->createUrl('coupon/getProductGiftCouponList',
                                                               array('product_id' => $product_id)),
            'editCouponTemplateUrl' => $this->createUrl('coupon/template', array('template_id' => '')),
            'newCouponTemplateUrl' => $this->createUrl('coupon/template', array('product_id' => $product_id, 'product_name' => 'xxx', 'template_id' => '')),

            'uploadDeparturePoints' => $this->createUrl('productDeparture/uploadDeparturePoints',
                                                        array('product_id' => $product_id)),

            // APIs for product comment
            'productComments' => $this->createUrl('productComment/comments', array('product_id' => $product_id)),
            'productComment' => $this->createUrl('productComment/comment', ['product_id' => $product_id, 'comment_id' => '']),
            'productCommentStatInfo' => $this->createUrl('productComment/getStatInfo',
                                                         array('product_id' => $product_id)),
            'getRandomCustomer' => $this->createUrl('productComment/getCustomer', array('product_id' => $product_id)),


            // Product Bundle
            'getBundleList' => $this->createUrl('productBundle/getBundleList', array('product_id' => $product_id)),
            'saveBundle' => $this->createUrl('productBundle/saveBundle'),
            'deleteBundle' => $this->createUrl('productBundle/deleteBundle', array('bundle_id' => '')),
            'deleteBundleProduct' => $this->createUrl('productBundle/deleteBundleProduct',
                                                      array('product_id' => $product_id, 'bundle_id' => 'bundle_000', 'binding_product_id' => 'product_000')),
            'bundleItemChangeOrder' => $this->createUrl('productBundle/bundleItemChangeOrder'),
            'getBundleHotelSpecial' => $this->createUrl('productBundle/getBundleHotelSpecial',
                                                        array('product_id' => $product_id)),

            // Product Hotel
            'rateSources' => $this->createUrl('productHotel/rateSources'),
            'serviceItems' => $this->createUrl('productHotel/serviceItems'),
            'hotelInfo' => $this->createUrl('productHotel/hotelInfo', array('product_id' => $product_id)),
            'hotelRoomType' => $this->createUrl('productHotel/hotelRoomType',
                                                array('product_id' => $product_id, 'room_type_id' => '')),
            'updateRoomImage' => $this->createUrl('productHotel/updateRoomImage',
                                                  array('room_type_id' => '', 'image_id' => '')),
            'bedPolicy' => $this->createUrl('productHotel/bedPolicy', array('policy_id' => '')),

            // Trip Plan
            'changeToOnline' => $this->createUrl('tripPlan/changeToOnline', ['product_id' => $product_id]),
            'getTripPlan' => $this->createUrl('tripPlan/getTripPlan',
                                              ['product_id' => $product_id]), // get whole trip plan
            'planInfo' => $this->createUrl('tripPlan/planInfo',
                                           ['product_id' => $product_id, 'plan_id' => '']), // get, post, delete one day plan info
            'changePlanOrder' => $this->createUrl('tripPlan/changePlanOrder', ['product_id' => $product_id]),
            'savePlanPoints' => $this->createUrl('tripPlan/savePlanPoints', ['product_id' => $product_id]),
            'savePlanTraffic' => $this->createUrl('tripPlan/savePlanTraffic',
                                                  ['product_id' => $product_id, 'plan_id' => '']),
            'changePlanPointOrder' => $this->createUrl('tripPlan/changePlanPointOrder', ['plan_id' => '']),
            'addPlanPoint' => $this->createUrl('tripPlan/addPlanPoint', ['plan_id' => '']),
            'updatePlanPoint' => $this->createUrl('tripPlan/updatePlanPoint', ['point_id' => '']),
            'deletePlanPoint' => $this->createUrl('tripPlan/deletePlanPoint', ['point_id' => '']),
            'planPointImages' => $this->createUrl('tripPlan/planPointImages',
                                                  ['point_id' => '']), // get/update point images
            'planPointImage' => $this->createUrl('tripPlan/planPointImage',
                                                 ['point_id' => '']), // add/update point image

            'updateProductIntroduction' => $this->createUrl('productDescription/updateProductDescriptionNew',
                                                            ['product_id' => $product_id]),
            'productIntroduction' => $this->createUrl('productDescription/productIntroduction',
                                                      ['product_id' => $product_id]),

            //Product Sightseeing
            'productSightseeing' => $this->createUrl('product/productSightseeing', ['product_id' => $product_id]),
            'sightseeingDisplayOrder' => $this->createUrl('product/sightseeingDisplayOrder', ['product_id' => $product_id]),

            //APIs for tag
            'getTags' => $this->createUrl('tag/getTags'),
            // Preview the direction.
            'viewMap' => $this->createUrl('site/viewMap'),

            // APIs for Multiple day trip.
            'multiDayIntroduce'=>$this->createUrl('productMultiDay/multiDayIntroduce', ['product_id' =>  $product_id]),
            'multiDayHighLight'=>$this->createUrl('productMultiDay/multiDayHighLight', ['product_id' =>  $product_id]),
            'updateBriefImage'=>$this->createUrl('productMultiDay/updateBriefImage', ['product_id' =>  $product_id]),
            'updateBriefImageMobile'=>$this->createUrl('productMultiDay/updateBriefImageMobile', ['product_id' =>  $product_id]),
            'updateTripIntroImage'=>$this->createUrl('productMultiDay/updateTripIntroImage', ['product_id' =>  $product_id]),
            'updateAvatar'=>$this->createUrl('productMultiDay/updateAvatar', ['product_id' => $product_id]),
            'updateTripLineImage'=>$this->createUrl('productMultiDay/updateTripLineImage', ['product_id' => $product_id]),

            // Product Q&A
            'getProductFeedback'=>$this->createUrl('productAsk/get', ['product_id' => $product_id]),
            'saveProductFeedback'=>$this->createUrl('productAsk/save', ['product_id' => $product_id]),
        );

        $this->layout = '//layouts/common';
        $this->pageTitle = '商品编辑';
        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('detail');
    }

    public function actionCopy()
    {
        $product_id = $this->getProductID();
        list($result, $msg, $new_product_id) = HtProduct::model()->copyProduct($product_id);

        EchoUtility::echoCommonMsg($result ? 200 : 400, $msg, $new_product_id);
    }

    /*
     * 更改产品编辑状态：1：编辑中；2：待审核；3：已上架；4：禁用
     */
    public function actionChangeEditingState()
    {
        $product_id = (int)$this->getProductID();
        $data = $this->getPostJsonData();
        $status = (int)$data['status'];

        if (3 == $status) {
            $error = HtProduct::validateProduct($product_id);
            if (!empty($error)) {
                EchoUtility::echoCommonFailed("状态更新失败！\n" . $error);

                return;
            }
        }

        list($result, $msg) = HtProduct::changeStatus($product_id, $status);
        EchoUtility::echoCommonMsg($result ? 200 : 400, $msg);
    }

    public function actionSaveProductRule()
    {
        if (!Yii::app()->user->checkAccess('HT_ProductEdit')) {
            $this->redirect($this->createUrl('/'));
        }
        $msg = '';
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();

        $redeem_limit = $data['redeem_limit'];
        $return_limit = $data['return_limit'];
        $sale_date_limit = $data['sale_date_rule'];

        $redeem_item = HtProductRedeemRule::model()->findByPk($product_id);
        ModelHelper::fixDateValue($redeem_limit, 'expire_date');

        $result = ModelHelper::updateItem($redeem_item, $redeem_limit);
        $msg .= $this->getMsg('兑换规则信息', $result) . "\n";

        $return_item = HtProductReturnRule::model()->findByPk($product_id);
        $result = ModelHelper::updateItem($return_item, $return_limit);
        $msg .= $this->getMsg('退票限制', $result) . "\n";

        $sale_date_item = HtProductDateRule::model()->findByPk($product_id);
        $sale_date_limit['sale_range_type'] = HtProductDateRule::TYPE_TO_DATE;
        if (strpos($sale_date_limit['sale_range'], '0') !== 0) {
            $sale_date_limit['sale_range_type'] = HtProductDateRule::TYPE_RANGE;
        }
        $result = ModelHelper::updateItem($sale_date_item, $sale_date_limit,
                                          array('lead_time', 'buy_in_advance', 'sale_range', 'sale_range_type', 'day_type', 'shipping_day_type'));
        $msg .= $this->getMsg('购买日期限制', $result) . "\n";

        HtProduct::clearCachedRuleDesc($product_id);
        $rule_desc = HtProduct::model()->getRuleDesc($product_id);

        EchoUtility::echoCommonMsg(200, $msg, $rule_desc);
    }

    private function getMsg($base, $res)
    {
        switch ($res) {
            case -1:
                return '未找到产品的' . $base . '！';
            case 0:
                return $base . '保存失败！';
            case 1:
                return $base . '保存成功！';
        }
    }

    public function actionGetProductBasicInfo()
    {
        $product_detail = HtProduct::model()->getProductDetail($this->getProductID());

        EchoUtility::echoCommonMsg(200, '', $product_detail);
    }

    public function actionGetExpertList()
    {
        $result = Converter::convertModelToArray(HtTravelExpert::model()->findAll());
        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionGetProductInfo($product_id)
    {
        $product = HtProduct::model()->findByPk($product_id);

        $result = array('product_id' => $product_id);
        ModelHelper::fillItem($result, $product,
                              array('city_code', 'supplier_id', 'type', 'is_combo', 'supplier_product_id', 'source_url'));
        $city = HtCity::model()->getByCode($product['city_code']);
        $result['city_name'] = $city['cn_name'];

        $combo_products = HtProductCombo::model()->findAll('product_id = ' . $this->getProductID());
        $combo_result = array();
        foreach ($combo_products as $r) {
            $combo_result[] = HtProduct::model()->getProductBasic($r['sub_product_id']);
        }
        $result['combo'] = $combo_result;

        $expert = HtProductExpertRef::model()->find('product_id = ' . $this->getProductID());
        if(!empty($expert)) {
            $result['expert_id'] = $expert['expert_id'];
        }

        $other_cities = array();
        $product_cities = HtProductCity::model()->with('city')->findAllByAttributes(array('product_id' => $product_id));
        foreach ($product_cities as $city) {
            $other_cities[] = array(
                'city_code' => $city['city_code'],
                'city_name' => $city['city']['cn_name']
            );
        }

        $result['other_cities'] = $other_cities;

        $pd = HtProductDescription::model()->getFieldValues($product_id, ['name', 'origin_name']);

        ModelHelper::fillItem($result, $pd, ['en_name', 'cn_name', 'en_origin_name', 'cn_origin_name']);

        $product_manager = HtProductManager::model()->find('product_id = ' . $product_id);
        $result['manager_name'] = $product_manager['manager_name'];

        if ($result['supplier_id'] == 11) {
            $result['import'] = GtaAutoImport::model()->getProductImport($product_id);
        }

        $result['tags'] = HtProductTag::getTagsOfProduct($product_id);

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionGetProductSaleDateRule()
    {
        $result = HtProductDateRule::model()->findByPk($this->getProductID());

        EchoUtility::echoByResult($result, 'Ok', '产品的购买日期规则信息不存在。');
    }

    public function actionUpdateProductInfo()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();


        $product = HtProduct::model()->findByPk($product_id);
        if(isset($data['type'])) {
            if(($product['type'] == HtProduct::T_HOTEL) && ($data['type'] != HtProduct::T_HOTEL)){
                EchoUtility::echoCommonMsg(400, '酒店商品不能更改类型');
                return;
            }else if(($product['type'] != HtProduct::T_HOTEL) && ($data['type'] == HtProduct::T_HOTEL)){
                EchoUtility::echoCommonMsg(400, '商品类型不能改为酒店');
                return;
            }else if($data['type'] == HtProduct::T_CHARTER_BUS){
                HtProductTicketRule::model()->deleteAll('product_id = '.$product_id);
                $ticket_rule = new HtProductTicketRule();
                $ticket_rule['product_id'] = $product_id;
                $ticket_rule['ticket_id'] = 1;
                $ticket_rule->insert();
                HtProductSaleRule::model()->deleteAll('product_id = '.$product_id);
                $sale_rule = new HtProductSaleRule();
                $sale_rule['product_id'] = $product_id;
                $sale_rule->insert();
                HtProductPricePlan::model()->removePricePlan($product_id,0);
                HtProductPricePlan::model()->removePricePlan($product_id,1);
                $special_info = HtProductSpecialCombo::getAllComboSpecialDetail($product_id);
                if(!empty($special_info) && !empty($special_info['special_codes'])){//包车商品清空供应商原始名称
                    foreach($special_info['special_codes'] as $special){
                        $item = HtProductSpecialItem::model()->findByPk(array('group_id' => $special['group_id'], 'special_code' => $special['special_code']));
                        $item['product_origin_name'] = '';
                        $item->update();
                    }
                }
            }

        }

        $result = ModelHelper::updateItem($product, $data,
                                          array('city_code', 'supplier_id', 'type', 'is_combo', 'source_url', 'supplier_product_id'));

        if(isset($data['cn_name'])) {
            $result = HtProductDescription::model()->updateFieldValues($product_id, ['name', 'origin_name'], $data);
        }

        if(isset($data['is_combo'])) {
            if ($data['is_combo'] == 0) {
                HtProductCombo::model()->deleteAll('product_id=' . $product_id);
            }
        }

        if(isset($data['manager_name'])) {
            $result = HtProductManager::model()->addOrUpdate($product_id, $data['manager_name']);
        }

        if(isset($data['expert_id'])) {
            $expertRef = HtProductExpertRef::model()->find('product_id = ' . $product_id);
            if(empty($expertRef)) {
                $new_expert = new HtProductExpertRef();
                $new_expert['product_id'] = $product_id;
                $new_expert['expert_id'] = $data['expert_id'];
                $result = $new_expert -> insert();
            } else {
                $expertRef['expert_id'] = $data['expert_id'];
                $result = $expertRef -> update();
            }
        }

        //tags处理
        if(isset($data['tags'])) {
            HtProductTag::updateTags($product_id, $data['tags']);
        }

        EchoUtility::echoCommonMsg(200, $this->getMsg('产品基本信息', $result), $data);
    }

    public function actionOtherCity()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();
        if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $city_code = $data['city_code'];

            $result = HtProductCity::addNew($product_id, $city_code);

            HtCity::updateCityHasOnlineProduct($city_code);

            EchoUtility::echoMsgTF($result, '添加');
        } else if ($request_method == 'delete') {
            $city_code = Yii::app()->request->getParam('city_code');
            $result = HtProductCity::model()->deleteByPk(array('product_id' => $product_id, 'city_code' => $city_code)) > 0;

            HtCity::updateCityHasOnlineProduct($city_code);

            EchoUtility::echoMsgTF($result, '删除');
        }
    }

    public function actionGetProductQA($product_id)
    {
        $product_quiz = HtProductQa::model()->findByPk($product_id);

        $product_quiz['qa'] = html_entity_decode($product_quiz['qa']);
        EchoUtility::echoCommonMsg(200, '', $product_quiz);
    }

    public function actionUpdateProductQA()
    {
        $data = $this->getPostJsonData();

        $product_quiz = HtProductQa::model()->findByPk($this->getProductID());
        $result = ModelHelper::updateItem($product_quiz, $data);

        EchoUtility::echoCommonMsg(200, $this->getMsg('产品QA', $result), $data);
    }

    public function actionGetProductRules()
    {
        $product_id = $this->getProductID();

        $redeem_item = Converter::convertModelToArray(HtProductRedeemRule::model()->findByPk($product_id));
        $return_item = Converter::convertModelToArray(HtProductReturnRule::model()->findByPk($product_id));
        $sale_date_rule = Converter::convertModelToArray(HtProductDateRule::model()->findByPk($product_id));

        $rule_desc = HtProduct::model()->getRuleDesc($product_id);

        EchoUtility::echoCommonMsg(200, '', array(
            'redeem_limit' => $redeem_item,
            'return_limit' => $return_item,
            'sale_date_rule' => $sale_date_rule,
            'rule_desc' => $rule_desc
        ));
    }

    public function actionGetProductPassengerRule()
    {
        $product_id = $this->getProductID();
        $passenger_rule = HtProductPassengerRule::model()->findByPk($product_id);
        $passenger_rule = Converter::convertModelToArray($passenger_rule);
        $passenger_item = HtProductPassengerRuleItem::model()->with('ticket_type')->findAll('product_id = ' . $product_id);
        $passenger_item = Converter::convertModelToArray($passenger_item);
        $passenger_rule['rule_item'] = $passenger_item;

        EchoUtility::echoByResult($passenger_rule, 'Ok', '产品的出行人信息不存在。');
    }

    public function actionUpdateProductPassengerRule()
    {
        $product_id = $this->getProductID();
        $item = HtProductPassengerRule::model()->findByPk($product_id);

        $data = $this->getPostJsonData();
        $result = ModelHelper::updateItem($item, $data,
                                          array('lead_fields', 'need_passenger_num', 'need_lead', 'lead_hidden_fields'));
        $voucher_rule = HtProductVoucherRule::model()->findByPk($product_id);
        $voucher_rule["lead_fields"] = $this->updateVoucherPassengerRule($voucher_rule["lead_fields"],
                                                                         $data["lead_fields"]);
        $result = $voucher_rule->update() ? 1 : 0;
        if ($result == 1) {
            foreach ($data['rule_item'] as $item_data) {
                $item_data['fields'] = trim($item_data['fields'], ","); // fix bug that data has leading ','
                $item = HtProductPassengerRuleItem::model()->findByAttributes(array('product_id' => $product_id, 'ticket_id' => $item_data['ticket_id']));
                $result = ModelHelper::updateItem($item, $item_data, array('fields', 'hidden_fields'));
                $voucher_item = HtProductVoucherRuleItem::model()->findByAttributes(array('product_id' => $product_id, 'ticket_id' => $item_data['ticket_id']));
                if (!empty($voucher_item)) {
                    $voucher_item["fields"] = $this->updateVoucherPassengerRule($voucher_item["fields"],
                                                                                $item_data["fields"]);
                    $result = $voucher_item->update() ? 1 : 0;
                }
                if ($result != 1) break;
            }
        }

        EchoUtility::echoMsg($result, '出行人规则信息', '', array());
    }

    public function actionUpdatePackagePassengerRule()
    {
        $product_id = $this->getProductID();
        $result = HtProductPassengerRule::model()->updateHotelPassengerRule($product_id);
        EchoUtility::echoMsgTF($result, '更新酒店套餐');
    }

    private function updateVoucherPassengerRule($voucher_pax_rule, $new_pax_rule)
    {
        $voucher_arr = explode(",", $voucher_pax_rule);
        $result_arr = explode(",", $voucher_pax_rule);

        foreach ($voucher_arr as $item) {
            if (strlen($item) > 0) {
                $index = strpos($new_pax_rule, $item);
                if ($index === false) {
                    $key = array_search($item, $result_arr);
                    if ($key !== false) {
                        array_splice($result_arr, $key, 1);
                    }
                }
            }
        }

        return trim(",", implode(",", $result_arr));
    }


    public function actionGetPassengerMetaData()
    {
        $data = $this->getPostJsonData();
        $c = new CDbCriteria();

        $c->order = !empty($data['order']) ? $data['order'] . ' ASC' : 'group_order, display_order ASC';

        $result = HtPassengerMetaData::model()->findAll($c);

        EchoUtility::echoByResult($result, 'Ok', '获取出行人信息MetaData失败。');
    }

    public function actionGetProducts()
    {
        $req_data = $this->getPostJsonData();

        $data = HtProduct::model()->getProducts($req_data);

        echo CJSON::encode(array('code' => 200, 'msg' => '', 'data' => $data['data'], 'total' => $data['total']));
    }

    public function actionGetSuppliers()
    {
        $c = new CDbCriteria();
        $c->select = array('supplier_id', 'name');
        $c->order = 'name ASC';
        $result = HtSupplier::model()->findAll($c);

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionGetCities()
    {
        $cityIDs = HtCity::model()->getCityIDsHaveProduct();
        $data = HtCity::model()->getCountryCityInfo($cityIDs);

        EchoUtility::echoCommonMsg(200, '', $data);
    }

    public function actionGetProductAlbum()
    {
        $data = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        if (empty($data)) {
            $data = HtProductAlbum::addDefaultProductAlbum($this->getProductID());
        }

        $album_id = $data['album_id'];

        $album_points = [];
        if ($album_id > 0) {
            $landinfos = Landinfo::model()->getLandinfos($album_id);
            $album_points = ModelHelper::getList($landinfos, 'location');
        }

        $result = [];
        ModelHelper::fillItem($result, $data, ['need_album', 'album_name', 'album_map', 'landinfo_md_title']);

        $result['album_id'] = $album_id;
        $result['album_points'] = $album_points;
        $result['album_info'] = $this->getAlbumInfo($album_id);
        $result['landinfo_md'] = html_entity_decode($data['landinfo_md']);

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionUpdateProductAlbum()
    {
        $data = $this->getPostJsonData();

        $result = $this->updateProductAlbum($this->getProductID(), $data);

        $album_info = $this->getAlbumInfo($data['album_id']);

        EchoUtility::echoMsg($result, '', '未找到该专辑。', $album_info);
    }

    public function actionGetProductPickTicketAlbum()
    {
        $data = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        if (empty($data)) {
            $data = HtProductAlbum::addDefaultProductAlbum($this->getProductID());
        }
        $album_id = $data['pick_ticket_album_id'];

        $result = array(
            'pick_ticket_album_id' => $album_id,
            'need_pick_ticket_album' => $data['need_pick_ticket_album'],
            'pt_group_info' => html_entity_decode($data['pt_group_info']),
            'specification_md' => html_entity_decode($data['specification_md']),
            'pick_ticket_map' => $data['pick_ticket_map'],
            'album_map' => $data['album_map'],
            'pick_ticket_album_info' => $this->getAlbumInfo($album_id),
            'landinfos' => Landinfo::model()->getLandinfos($album_id)
        );

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionUpdateProductPickTicketAlbum()
    {
        $data = $this->getPostJsonData();
        $pick_ticket_album_id = $data['pick_ticket_album_id'];
        $album_info = $this->getAlbumInfo($pick_ticket_album_id);

        $result = $this->updateProductPickTicketAlbum($this->getProductID(), $data);

        $product_album_info = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        EchoUtility::echoMsg($result, '', '未找到该专辑。',
                             array('album_info' => $album_info,
                                 'pt_group_info' => html_entity_decode($product_album_info['pt_group_info']),
                                 'landinfos' => Landinfo::model()->getLandinfos($pick_ticket_album_id)));
    }

    public function actionSaveAlbumInfoAllOld()
    {
        $data = $this->getPostJsonData();

        $album_info = $this->getAlbumInfo($data['album_id']);

        $result = $this->updateProductAlbum($this->getProductID(), $data);
        $msg = $this->getMsg('景点专辑信息', $result) . "\n";


        $pick_ticket_album_id = $data['pick_ticket_album_id'];
        $pick_ticket_album_info = $this->getAlbumInfo($pick_ticket_album_id);

        $result = $this->updateProductPickTicketAlbum($this->getProductID(), $data);
        $msg .= $this->getMsg('接送地点专辑信息', $result) . "\n";

        $product_album_info = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        $result = ModelHelper::updateItem($product_album_info, $data, array('album_name', 'landinfo_md'));
        $msg .= $this->getMsg('文字景点信息', $result) . "\n";

        EchoUtility::echoCommonMsg(200, $msg, array(
            'need_album' => $product_album_info['need_album'],
            'need_pick_ticket_album' => $product_album_info['need_pick_ticket_album'],
            'album_info' => $album_info,
            'pick_ticket_album_info' => $pick_ticket_album_info,
            'pt_group_info' => html_entity_decode($product_album_info['pt_group_info']),
            'landinfo_md' => html_entity_decode($product_album_info['landinfo_md']),
            'landinfos' => Landinfo::model()->getLandinfos($pick_ticket_album_id)
        ));
    }

    // TODO: replace above with the following one if using product/detail ...
    public function actionSaveAlbumInfoAll()
    {
        $data = $this->getPostJsonData();

        $album_info = $this->getAlbumInfo($data['album_id']);

        $result = $this->updateProductAlbum($this->getProductID(), $data);
        $msg = $this->getMsg('景点专辑信息', $result) . "\n";

        $product_album_info = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        $result = ModelHelper::updateItem($product_album_info, $data, array('album_name', 'landinfo_md'));
        $msg .= $this->getMsg('文字景点信息', $result) . "\n";

        EchoUtility::echoCommonMsg(200, $msg, array(
            'need_album' => $product_album_info['need_album'],
            'album_info' => $album_info,
            'landinfo_md' => html_entity_decode($product_album_info['landinfo_md']),
        ));
    }

    public function actionSavePickTicketAlbumInfoAll()
    {
        $data = $this->getPostJsonData();

        $pick_ticket_album_id = $data['pick_ticket_album_id'];
        $pick_ticket_album_info = $this->getAlbumInfo($pick_ticket_album_id);

        $result = $this->updateProductPickTicketAlbum($this->getProductID(), $data);
        $msg = $this->getMsg('接送地点专辑信息', $result) . "\n";

        $product_album_info = HtProductAlbum::model()->find('product_id=' . $this->getProductID());
        $msg .= $this->getMsg('文字景点信息', $result) . "\n";

        EchoUtility::echoCommonMsg(200, $msg, array(
            'need_pick_ticket_album' => $product_album_info['need_pick_ticket_album'],
            'pick_ticket_album_info' => $pick_ticket_album_info,
            'pt_group_info' => html_entity_decode($product_album_info['pt_group_info']),
            'landinfos' => Landinfo::model()->getLandinfos($pick_ticket_album_id)
        ));
    }


    public function actionSavePickTicketMap()
    {
        //  get pick ticket map by center, zoom, points
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $image = $this->getMapImage($data);
        if (!empty($image)) {
            $pick_ticket_map = FileUtility::uploadToQiniu($image, true);

            HtProductAlbum::model()->updateByPk($product_id, array('pick_ticket_map' => $pick_ticket_map));

            EchoUtility::echoMsgTF(true, '保存', array('pick_ticket_map' => $pick_ticket_map));
        } else {
            EchoUtility::echoCommonFailed('获取地图失败。');
        }
    }

    public function actionSaveAlbumMap()
    {
        // save album_map
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $image = $this->getMapImage($data);
        if (!empty($image)) {
            $album_map = FileUtility::uploadToQiniu($image, true);

            HtProductAlbum::model()->updateByPk($product_id, array('album_map' => $album_map));

            EchoUtility::echoMsgTF(true, '保存', array('album_map' => $album_map));
        } else {
            EchoUtility::echoCommonFailed('获取地图失败。');
        }
    }

    private function updateProductAlbum($product_id, $data)
    {
        $product_album_info = HtProductAlbum::model()->find('product_id=' . $product_id);

        $album_id = $data['album_id'];
        $album_info = $this->getAlbumInfo($album_id);
        if ($data['need_album'] == 1 && empty($album_info) && $album_id > 0) {
            $result = -1;
        } else {
            $update_fields = array('album_id', 'need_album', 'landinfo_md_title');
            if ($data['need_album'] == 0) {
                $data['album_id'] = 0;
                $data['album_map'] = '';
                array_push($update_fields, 'album_map');
            }

            $result = ModelHelper::updateItem($product_album_info, $data, $update_fields);

            if ($result == 1) {
                $c = new CDbCriteria();
                $c->addCondition('image_usage=2');
                $c->addCondition('product_id=' . $product_id);
                HtProductImage::model()->deleteAll($c);

                $landinfos = Landinfo::model()->getLandinfos($album_id);
                foreach ($landinfos as $landinfo) {
                    HtProductImage::addProductImage(array(
                                               'product_id' => $product_id,
                                               'changed' => 0,
                                               'image' => '',
                                               'image_url' => '',
                                               'image_usage' => 2,
                                               'landinfo_id' => $landinfo['landinfo_id'],
                                           ));
                }
            }
        }

        return $result;
    }

    private function updateProductPickTicketAlbum($product_id, $data)
    {
        $product_album_info = HtProductAlbum::model()->find('product_id=' . $product_id);
        $pick_ticket_album_id = $data['pick_ticket_album_id'];
        $album_info = $this->getAlbumInfo($pick_ticket_album_id);
        if ($data['need_pick_ticket_album'] == 1 && empty($album_info) && $pick_ticket_album_id > 0) {
            $result = -1;
        } else {
            $update_fields = array('pick_ticket_album_id', 'pt_group_info', 'need_pick_ticket_album', 'specification_md');
            if ($data['need_pick_ticket_album'] == 0) {
                $data['pick_ticket_album_id'] = 0;
                $data['pt_group_info'] = '';
                $data['pick_ticket_map'] = '';
                $data['specification_md'] = '';
                array_push($update_fields, 'pick_ticket_map');
            }
            $result = ModelHelper::updateItem($product_album_info, $data, $update_fields);
        }

        return $result;
    }

    public function actionProductSightseeing() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();

        if($request_method == 'get') {
            $data = HtProductSightseeing::model()->findAllByAttributes(array('product_id'=>$product_id));
            EchoUtility::echoMsgTF(true, '获取城市聚合', Converter::convertModelToArray($data));
        } else if($request_method == 'post') {
            $data = $this->getPostJsonData();
            $item = HtProductSightseeing::model()->findByPk($data['id']);
            if(!empty($item)) {
                $result = ModelHelper::updateItem($item, $data, array('zh_name', 'en_name', 'en_address', 'zh_address', 'latlng'));
            } else {
                $item = new HtProductSightseeing();
                $item['product_id'] = $product_id;
                ModelHelper::fillItem($item, $data, array('zh_name', 'en_name', 'en_address', 'zh_address', 'latlng', 'display_order'));
                $result = $item->insert();
            }
            EchoUtility::echoMsgTF($result,'保存景点',Converter::convertModelToArray($item));
        } else if($request_method == 'delete') {
            $id = (int)Yii::app()->request->getParam('id');
            $result = HtProductSightseeing::model()->deleteByPk($id);
            EchoUtility::echoMsgTF($result > 0, '删除');
        }
    }

    //更改体验分组文章顺序
    public function actionSightseeingDisplayOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $sight) {
            $item = HtProductSightseeing::model()->findByPk($sight['id']);
            $part_result = ModelHelper::updateItem($item, $sight, ['display_order']);
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }
        EchoUtility::echoMsgTF($result, '更改景点顺序');
    }

    public function actionGetProductImages()
    {
        $c = new CDbCriteria();
        $c->addCondition('product_id=' . $this->getProductID());
        $c->addInCondition('image_usage', array(0));
        $sample_image = HtProductImage::model()->find($c);

        $c = new CDbCriteria();
        $c->addCondition('product_id=' . $this->getProductID());
        $c->addInCondition('image_usage', array(1, 2));
        $loop_images = HtProductImage::model()->findAll($c);

        $product_album = HtProductAlbum::model()->findByPk($this->getProductID());
        $landinfos = array();
        if (!empty($product_album) && ($product_album['need_album'] == 1)) {
            $landinfos = Landinfo::model()->getLandinfos($product_album['album_id']);
        }
        $landinfo_array = array();
        foreach ($landinfos as $landinfo) {
            $landinfo_array[$landinfo['landinfo_id']] = $landinfo;
        }

        $loop_images_array = array();
        foreach ($loop_images as $image) {
            if ($image['image_usage'] == 1) {
                array_push($loop_images_array, $image);
            } else {
                if (isset($landinfo_array[$image['landinfo_id']])) {
                    $landinfo = $landinfo_array[$image['landinfo_id']];
                    $image['image_url'] = $landinfo['image_url'];
                    $image['name'] = $landinfo['name'];
                    $image['short_desc'] = $landinfo['reason'];
                    array_push($loop_images_array, $image);
                } else {
                    HtProductImage::model()->deleteByPk($image['product_image_id']);
                }
            }
        }

        EchoUtility::echoCommonMsg(200, '', array(
            'sample_image' => $sample_image,
            'loop_images' => $loop_images_array,
            'landinfos' => $landinfos
        ));
    }

    public function actionAddOrUpdateProductSampleImage()
    {
        $product_id = $this->getProductID();
        $to_dir = 'image/upload/' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);

            $c = new CDbCriteria();
            $c->addCondition('product_id=' . $product_id);
            $c->addCondition('image_usage=0');
            $pi = HtProductImage::model()->find($c);
            if ($pi) {
                if (strlen($image_url) > 0) {
                    $pi['image_url'] = $image_url;
                    $pi['changed'] = 0;
                } else {
                    $pi['changed'] = 1;
                }
                $pi['image'] = $to_dir . $file;

                $result = $pi->update();

                echo CJSON::encode(array('code' => 200, 'msg' => $this->getMsg('产品样张',
                                                                               $result ? 1 : 0), 'sample_image' => $pi));
            } else {
                $result = HtProductImage::addProductImage(array(
                                                     'product_id' => $product_id,
                                                     'image' => $to_dir . $file,
                                                     'image_url' => $image_url,
                                                     'changed' => 1,
                                                     'image_usage' => 0,
                                                     'landinfo_id' => 0,
                                                 ));

                echo CJSON::encode(array('code' => 200, 'msg' => $this->getMsg('产品样张',
                                                                               empty($result) ? 0 : 1), 'sample_image' => $result));
            }
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionAddProductImage()
    {
        $product_id = $this->getProductID();
        $to_dir = 'image/upload/' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);

            $result = HtProductImage::addProductImage(array(
                                                 'product_id' => $product_id,
                                                 'image' => $to_dir . $file,
                                                 'image_url' => $image_url,
                                                 'changed' => strlen($image_url) > 0 ? 0 : 1,
                                                 'image_usage' => 1,
                                                 'landinfo_id' => 0,
                                             ));

            echo CJSON::encode(array('code' => 200, 'msg' => $this->getMsg('产品图片',
                                                                           empty($result) ? 0 : 1), 'loop_image' => $result));
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionAddProductImageOfLandinfo()
    {
        $data = $this->getPostJsonData();

        $result = HtProductImage::addProductImage(array(
                                             'product_id' => $this->getProductID(),
                                             'changed' => 0,
                                             'image' => '',
                                             'image_url' => '',
                                             'image_usage' => 2,
                                             'landinfo_id' => $data['landinfo_id'],
                                         ));

        if ($result) {
            $landinfo = Landinfo::model()->getLandinfo($result['landinfo_id']);
            $result['image_url'] = $landinfo['image_url'];
            $result['name'] = $landinfo['name'];
            $result['short_desc'] = $landinfo['reason'];
            if(isset($data['sort_order'])) {
                $result['sort_order'] = $data['sort_order'];
            }
            $result->update();
        }

        echo CJSON::encode(array('code' => 200, 'msg' => $this->getMsg('产品图片',
                                                                       empty($result) ? 0 : 1), 'loop_image' => $result));
    }

    public function actionDeleteProductImage()
    {
        $data = $this->getPostJsonData();
        $product_image_id = $data['product_image_id'];
        $image_usage = $data['image_usage'];
        if ($image_usage == 1) {
            $pi = HtProductImage::model()->findByPk($product_image_id);
            if ($pi && !empty($pi['image'])) {
                $file = Yii::app()->params['DIR_UPLOAD_ROOT'] . $pi['image'];
                FileUtility::deleteFile($file);
            }
        }

        $result = HtProductImage::model()->deleteByPk($product_image_id);

        EchoUtility::echoMsgTF($result, '删除');
    }

    /*
     * 设置指定图片为产品Cover
     */
    public function actionProductImageSetCover()
    {
        $c = new CDbCriteria();
        $c->addCondition('product_id=' . $this->getProductID());
        $c->addCondition('as_cover=1');
        $pi = HtProductImage::model()->find($c);
        if (!empty($pi)) {
            $pi['as_cover'] = 0;
            $pi->update();
        }

        $data = $this->getPostJsonData();

        $pi = HtProductImage::model()->findByPk($data['product_image_id']);
        $pi['as_cover'] = 1;
        $result = $pi->update();

        EchoUtility::echoMsgTF($result, '产品封面图设置', $pi);
    }

    /*
     * Update product image meta data -- name, short_desc
     */
    public function actionUpdateProductImage()
    {
        $data = $this->getPostJsonData();

        $pi = HtProductImage::model()->findByPk($data['product_image_id']);
        $result = ModelHelper::updateItem($pi, $data, array('name', 'short_desc'));

        EchoUtility::echoMsg($result, '产品图片', '', $pi);
    }

    /*
     * Update order of product images
     */
    public function actionUpdateProductImageOrder()
    {
        $data = $this->getPostJsonData();
        $pi_order = $data['order_info'];
        foreach ($pi_order as $order) {
            HtProductImage::model()->updateByPk($order['product_image_id'], ['sort_order' => $order['sort_order']]);
        }

        EchoUtility::echoCommonMsg(200, '更新完毕！');
    }

    public function actionGetProductRelated()
    {
        $data = HtProductRelated::model()->findAll('product_id = ' . $this->getProductID());
        $result = array();
        foreach ($data as $r) {
            $result[] = HtProduct::model()->getProductBasic($r['related_id']);
        }

        EchoUtility::echoCommonMsg(200, '获取成功！', $result);
    }

    public function actionAddProductRelated()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $related_id = (int)$data['related_id'];

        $data = HtProduct::model()->getProductBasic($related_id);
        if (empty($data)) {
            EchoUtility::echoCommonFailed('商品ID为' . $related_id . '的商品不存在！');

            return;
        }

        $result = HtProductRelated::addNew($product_id, $related_id);

        EchoUtility::echoMsgTF($result > 0, '添加', $data);
    }

    public function actionDeleteProductRelated()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $related_id = $data['related_id'];

        $result = HtProductRelated::model()->deleteAll('product_id=' . (int)$product_id . ' AND related_id=' . (int)$related_id);

        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    public function actionGetVoucherRules()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('product_id = ' . $this->getProductID());
        $voucherConfigurations = HtProductVoucherRule::model()->findAll($criteria);
        $voucherConfigurations = Converter::convertModelToArray($voucherConfigurations);

        if (count($voucherConfigurations) < 1) {
            $result = HtProductVoucherRule::addNew($this->getProductID());
            if ($result) {
                $voucherConfigurations = HtProductVoucherRule::model()->findAll($criteria);
            }
        }

        if (empty($voucherConfigurations[0]["pay_cert"])) {
            $supplierId = HtProduct::model()->findByPk($this->getProductID())["supplier_id"];
            $voucherConfigurations[0]["pay_cert"] = HtSupplier::model()->findByPk($supplierId)["payable_by"];
        }

        //pdf
        $pdf_list = array();
        if ($voucherConfigurations[0]["attached_pdf"]) {
            $attached_pdf = json_decode($voucherConfigurations[0]['attached_pdf']);
            if (count($attached_pdf) > 0) {
                foreach ($attached_pdf as $k => $v) {
                    if (!empty($v)) {
                        $pdf_list[$k]['pdf_name'] = $v;
                        $pdf_list[$k]['pdf_path'] = Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['ATTACHED_VOUCHER_PATH'] . $this->getProductID() . '/' . $v;
                    }
                }
            }
        }
        $voucherConfigurations[0]["attached_pdf"] = $pdf_list;

        $passengerRules = $this->analysizePassengerRules();

        $wholeConfigurations = array(
            "configurations" => $voucherConfigurations,
            "wholeRules" => $passengerRules);
        EchoUtility::echoByResult($wholeConfigurations, 'Ok', '商品没有voucher的配置信息');
    }

    //上传辅助信息PDF
    public function actionUploadAttachedPdf()
    {
        $product_id = $this->getProductID();
        $attached_voucher_path = dirname(Yii::app()->BasePath) . Yii::app()->params['ATTACHED_VOUCHER_PATH'] . $product_id . '/';

        $result = FileUtility::uploadFile($attached_voucher_path, array('pdf'), '请选择pdf文件。', true, true);
        if ($result['code'] == 200) {
            $file = $result['file'];
            EchoUtility::echoCommonMsg(200, '上传成功！',
                                       array('pdf_name' => $file, 'pdf_path' => Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['ATTACHED_VOUCHER_PATH'] . $product_id . '/' . $file));
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //删除辅助信息PDF
    public function actionDeleteAttachedPdf()
    {
        $result = array('code' => 200, 'msg' => '删除成功!');
        $data = $this->getPostJsonData();
        $attached_voucher_path = dirname(Yii::app()->BasePath) . Yii::app()->params['ATTACHED_VOUCHER_PATH'] . $data['product_id'] . '/';
        $pdf_real_path = $attached_voucher_path . $data['pdf_name'];
        $isok = @unlink($pdf_real_path);
        if ($isok || !file_exists($pdf_real_path)) {
            $voucher = HtProductVoucherRule::model()->findByPk($data['product_id']);
            if (!empty($voucher)) {
                $new_pdfs = array();
                $pdfs = CJSON::decode($voucher['attached_pdf']);
                if (!empty($pdfs)) {
                    foreach ($pdfs as $pdf) {
                        if (trim($pdf) != trim($data['pdf_name'])) {
                            array_push($new_pdfs, $pdf);
                        }
                    }
                }
                $voucher['attached_pdf'] = CJSON::encode($new_pdfs);
                $isok = $voucher->update();
                if ($isok === false) {
                    Yii::log('Delete attached_pdf[' . $data['pdf_name'] . '] ok, but update DB failed.');
                    $result = array('code' => 500, 'msg' => '删除成功，更新数据失败!');
                }
            } else {
                $result = array('code' => 402, 'msg' => '未找到对应的voucher信息!');
            }
        } else {
            $result = array('code' => 400, 'msg' => '删除失败!');
        }
        echo CJSON::encode($result);
    }

    public function actionUpdateVoucherRule()
    {
        $data = $this->getPostJsonData();
        $basic = $data["voucherConfig"];
        //pdf转换
        if (is_array($data["voucherConfig"]['attached_pdf']) && count($data["voucherConfig"]['attached_pdf']) > 0) {
            $pdf_list = array();
            foreach ($data["voucherConfig"]['attached_pdf'] as $pdf) {
                $pdf_list[] = trim($pdf['pdf_name']);
            }
            $data["voucherConfig"]['attached_pdf'] = CJSON::encode($pdf_list);
        } else {
            $data["voucherConfig"]['attached_pdf'] = '';
        }
        $voucherConfigurations = HtProductVoucherRule::model()->findByPk($basic["product_id"]);
        $result = ModelHelper::updateItem($voucherConfigurations, $data["voucherConfig"]);

        $rule_arr = $data["passenger_rule_item"];
        foreach ($rule_arr as $rule) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('product_id = ' . $basic["product_id"]);
            $criteria->addCondition('ticket_id = ' . $rule["ticket_id"]);
            $passenger_item = HtProductVoucherRuleItem::model()->findAll($criteria);
            if (count($passenger_item) > 0) {
                $result = ModelHelper::updateItem($passenger_item[0], $rule);
                if (!$result)
                    break;
            } else {
                $result = HtProductVoucherRuleItem::addNew($basic['product_id'], $rule['ticket_id'], $rule['fields']);

                $result = $result ? 1 : 0;
                if (!$result)
                    break;
            }
        }


        EchoUtility::echoMsg($result, 'voucher配置', '', $data);
    }

    private function analysizePassengerRules()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('product_id = ' . $this->getProductID());
        $passengerRules = HtProductPassengerRule::model()->findByPk($this->getProductID());
        $passengerRules = Converter::convertModelToArray($passengerRules);
        $passengerItem = HtProductPassengerRuleItem::model()->with('ticket_type')->findAll($criteria);
        $passengerItem = Converter::convertModelToArray($passengerItem);

        if (!empty($passengerRules["lead_fields"])) {
            if (strlen($passengerRules["lead_fields"]) > 0 && substr($passengerRules["lead_fields"], 0, 1) == ",") {
                $passengerRules["lead_fields"] = substr($passengerRules["lead_fields"], 1,
                                                        strlen($passengerRules["lead_fields"]) - 1);
            }
            $leader_codes = explode(",", $passengerRules["lead_fields"]);
            $leader_arr = array();
            foreach ($leader_codes as $leader_item) {
                $label = HtPassengerMetaData::model()->findByPk($leader_item);
                array_push($leader_arr, array(
                    'ticket_id' => $leader_item,
                    'label' => $label["label"]
                ));
            }
            $passengerRules["lead_fields"] = $leader_arr;
        }

        if (!empty($passengerItem)) {
            for ($i = 0; $i < sizeof($passengerItem); $i++) {
                $c = new CDbCriteria();
                $c->addCondition('product_id = ' . $this->getProductID());
                $c->addCondition('ticket_id = ' . $passengerItem[$i]["ticket_id"]);
                $voucherItems = HtProductVoucherRuleItem::model()->findAll($c);

                if (strlen($passengerItem[$i]["fields"]) > 0 && substr($passengerItem[$i]["fields"], 0, 1) == ",") {
                    $passengerItem[$i]["fields"] = substr($passengerItem[$i]["fields"], 1,
                                                          strlen($passengerItem[$i]["fields"]) - 1);
                }
                $other_codes = explode(",", $passengerItem[$i]["fields"]);
                $other_arr = array();
                foreach ($other_codes as $other_item) {
                    $label = HtPassengerMetaData::model()->findByPk($other_item);
                    array_push($other_arr, array(
                        'ticket_id' => $other_item,
                        'label' => $label["label"]
                    ));
                }
                $passengerItem[$i]["fields"] = $other_arr;
                if (!empty($voucherItems)) {
                    $passengerItem[$i]["voucher_field"] = $voucherItems[0]["fields"];
                } else {
                    $passengerItem[$i]["voucher_field"] = "";
                }

            }
        }


        $voucherRules = HtProductVoucherRule::model()->findByPk($this->getProductID());
        if (!empty($voucherRules)) {
            $passengerRules["voucher_leader_field"] = $voucherRules["lead_fields"];
        } else {
            $passengerRules["voucher_leader_field"] = "";
        }

        $passengerRules['passenger_rule_item'] = $passengerItem;

        return $passengerRules;
    }

    private function getAlbumInfo($album_id)
    {
        $album_info = array();
        if ($album_id > 0) {
            $album = Album::model()->findByPk($album_id);

            if (!empty($album)) {
                $album_info = array(
                    'album_id' => $album_id,
                    'title' => $album['title'],
                    'link' => Yii::app()->params['urlViewAlbum'] . $album_id
                );
            }
        }

        return $album_info;
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function updateItemWithEcho($item, $data, $failed_get_item = '')
    {
        $result = ModelHelper::updateItem($item, $data);
        EchoUtility::echoMsg($result, '', $failed_get_item);
    }

    public function actionGetShippingConfigurations()
    {
        $shippingConfig = HtProductShippingRule::model()->findByPk($this->getProductID());
        if (empty($shippingConfig)) {
            $shippingConfig = HtProductShippingRule::initShippingRule($this->getProductID());
        }
        EchoUtility::echoByResult($shippingConfig, 'Ok', '商品没有发货的配置信息');
    }

    public function actionUpdateShippingConfigurations()
    {
        $data = $this->getPostJsonData();
        $shippingConfig = HtProductShippingRule::model()->findByPk($this->getProductID());

        // On/Off the switch item that can not modify but necessary.
        HtProductShippingRule::regulateShippingRule($data);

        $result = ModelHelper::updateItem($shippingConfig, $data);

        EchoUtility::echoMsg($result, '发货配置', '', $data);
    }

    public function actionGetProductCombo()
    {
        $data = HtProductCombo::model()->findAll('product_id = ' . $this->getProductID());
        $result = array();
        foreach ($data as $r) {
            $result[] = HtProduct::model()->getProductBasic($r['sub_product_id']);
        }

        EchoUtility::echoCommonMsg(200, '获取成功！', $result);
    }

    public function actionAddProductCombo()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $sub_product_id = (int)$data['sub_product_id'];

        $data = HtProduct::model()->getProductBasic($sub_product_id);
        if (empty($data)) {
            EchoUtility::echoCommonFailed('商品ID为' . $sub_product_id . '的商品不存在！');

            return;
        }

        $result = HtProductCombo::addNew($product_id, $sub_product_id);

        EchoUtility::echoMsgTF($result > 0, '添加', $data);
    }

    public function actionDeleteProductCombo()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $sub_product_id = $data['sub_product_id'];

        $result = HtProductCombo::model()->deleteAll('product_id=' . (int)$product_id . ' AND sub_product_id=' . (int)$sub_product_id);

        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    public function actionProductSeo()
    {
        $product_id = $this->getProductID();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
            $product_seo = HtSeoSetting::model()->findByProductId($product_id);

            EchoUtility::echoMsgTF(true, '获取商品SEO', $product_seo);
        } else if ($request_method == 'post') {
            $new_data = $this->getPostJsonData();

            $result = HtSeoSetting::addOrUpdateProductSeo($product_id, $new_data);

            EchoUtility::echoMsgTF($result, '更新城市SEO');
        }
    }

    public function actionFetchProducts()
    {
        $data = $this->getPostJsonData();
        $query = $this->getQueryParts($data);

        $data_sql = 'SELECT p.product_id product_id, pd.name product_name, c.cn_name city_name, s.name supplier_name, p.status status ' . $query['from'] . $query['where'] . $query["order"] . $query["limit"];
        $count_sql = 'SELECT COUNT(p.product_id) total' . $query['from'] . $query['where'];

        $connection = Yii::app()->db;
        $command = $connection->createCommand($data_sql);
        $result['data'] = $command->queryAll();
        $command = $connection->createCommand($count_sql);
        $result['total_count'] = $command->queryColumn();
        $result['total_count'] = $result['total_count'][0];

        foreach ($result['data'] as $key => $record) {
            $prices = HtProductPricePlan::model()->getShowPrices($result['data'][$key]['product_id']);
            $result['data'][$key]['price'] = $prices['price'];
            $result['data'][$key]['orig_price'] = $prices['orig_price'];
        }

        EchoUtility::echoMsgTF(true, '获取商品统计', $result);
    }

    private function getQueryParts($data)
    {
        $query = array();

        $query['from'] = '
            FROM `ht_product` AS p
            JOIN `ht_product_description` AS pd
            ON p.product_id = pd.product_id
            AND pd.language_id = 2
            JOIN `ht_city` AS c
            ON p.city_code = c.city_code
            JOIN `ht_supplier` AS s
            ON p.supplier_id = s.supplier_id
        ';

        $query['where'] = '';
        if (isset($data['query_filter'])) {
            /* city_code, supplier_id, product_term */
            if (!empty($data['query_filter']['city_code']) && $data['query_filter']['city_code'] != 'all') {
                $query['where'] .= ' AND c.city_code = "' . $data['query_filter']['city_code'] . '"';
            }
            if (!empty($data['query_filter']['supplier_id']) && $data['query_filter']['supplier_id'] != 'all') {
                $query['where'] .= ' AND s.supplier_id = ' . $data['query_filter']['supplier_id'];
            }
            if (!empty($data['query_filter']['product_term'])) {
                $query['where'] .= ' AND ( pd.name LIKE "%' . $data['query_filter']['product_term'] . '%" OR p.product_id = ' . $data['query_filter']['product_term'] . ' )';
            }
            if (!empty($data['query_filter']['product_status'])) {
                $query['where'] .= ' AND p.status = ' . $data['query_filter']['product_status'];
            }
            if (!empty($query['where'])) {
                $query['where'] = ' WHERE ' . substr($query['where'], 4);
            }
        }

        //PAGING
        if (isset($data['paging'])) {
            $query['limit'] = " LIMIT " . $data['paging']['start'] . ', ' . $data['paging']['limit'];
        }

        //SORTING
        if (isset($data['sort'])) {
            $query['order'] = '';
            foreach ($data['sort'] as $order_field => $order_dir) {
                $query['order'] .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
            }
            $query['order'] = " ORDER BY " . substr($query['order'], 2);
        }

        return $query;
    }

    /**
     * @param $data
     * @return string
     */
    private function getMapImage($data)
    {
        $center = $data['center'];
        $zoom = $data['zoom'];
        $points = $data['points'];

        //  construct the url

        $url = 'http://api.tiles.mapbox.com/v3/natecui.ig5adgfm/';
        $markers = '';
        $index = 0;
        foreach ($points as $point) {
            $loc = $point[1] . ',' . $point[0];
            if (!empty($markers)) {
                $markers .= ',';
            }

            $markers .= 'pin-s-' . chr(97 + $index) . '+f00(' . $loc . ')';

            $index++;
        }

        $url .= $markers . '/' . $center['lng'] . ',' . $center['lat'] . ',' . $zoom . '/1280x310.png';

        //  get image by url
        $image = FileUtility::downloadToFile($url);

        return $image;
    }

}
