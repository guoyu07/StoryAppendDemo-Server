<?php

class EdmController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = 'EDM列表';

        $request_urls = array(
            'editEdm'             => $this->createUrl('edm/edit', array('edm_id' => '')),
            'previewEdm'          => $this->createUrl('edm/preview', array('edm_id' => '')),
            'getEdmList'          => $this->createUrl('edm/getEDMList'),
            'addEDM'              => $this->createUrl('edm/addEDM'),
            'downloadEDMTemplate' => $this->createUrl('edm/downloadEDMTemplate', array('edm_id' => ''))
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = 'EDM编辑';

        $edm_id = Yii::app()->request->getParam('edm_id');

        $request_urls = array(
            'getEdmDetail'            => $this->createUrl('edm/getEDMDetail', array('edm_id' => $edm_id)),
            'previewEdm'              => $this->createUrl('edm/preview', array('edm_id' => $edm_id)),
            'updateCoverImage'        => $this->createUrl('edm/updateEDMImg', array('edm_id' => $edm_id)),
            'updateBaseInfo'          => $this->createUrl('edm/updateEDM', array('edm_id' => $edm_id)),
            'addGroup'                => $this->createUrl('edm/addEDMGroup', array('edm_id' => $edm_id)),
            'updateGroup'             => $this->createUrl('edm/updateGroup'),
            'updateGroupOrder'        => $this->createUrl('edm/updateGroupOrder', array('edm_id' => $edm_id)),
            'deleteGroup'             => $this->createUrl('edm/removeGroup',
                                                          array('edm_id' => $edm_id, 'group_id' => '')),
            'addGroupProduct'         => $this->createUrl('edm/addEDMGroupProduct'),
            'deleteGroupProduct'      => $this->createUrl('edm/removeGroupProduct'),
            'updateGroupProductInfo'  => $this->createUrl('edm/updateGroupProduct'),
            'updateGroupProductImage' => $this->createUrl('edm/updateProductImg', array('edm_id' => $edm_id)),
            'updateGroupProductOrder' => $this->createUrl('edm/updateGroupProductOrder', array('group_id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    public function actionPreview()
    {
        $this->pageTitle = 'EDM预览';

        $edm_id = $this->getParam('edm_id');

        $request_urls = array(
            'getEdmDetail' => $this->createUrl('edm/getEDMDetail', array('edm_id' => $edm_id))
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('preview');
    }

    public function actionGetEDMList()
    {
        //返回EDM列表
        $data = $this->getPostJsonData();

        $c = new CDbCriteria();
        $total_count = new CDbCriteria();

        $c->order = 'date_update DESC';
        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];

        EchoUtility::echoMsgTF(true, '获取EDM列表', array(
            'total_count' => HtEdm::model()->count($total_count),
            'data'        => Converter::convertModelToArray(HtEdm::model()->findAll($c))
        ));
    }

    public function actionGetEDMDetail()
    {
        //传入edm_id ， 返回edm信息、edm group信息和group product信息
        $edm_id = $this->getEDMId();
        $result = $this->fetchEDMDetails($edm_id);
        EchoUtility::echoMsgTF(true, '获取EDM列表', $result);
    }

    public function actionAddEDM()
    {
        //传入name，返回新建的edm信息
        $name = $this->getParam('name');
        $newEdm = new HtEdm();
        $newEdm['name'] = $name;
        $newEdm['title'] = '';
        $newEdm['description'] = '';
        $newEdm['banner_image'] = Yii::app()->params['EDM_IMAGE_DIR'] . 'top.png';
        $result = $newEdm->insert();
        if ($result) {
            EchoUtility::echoMsgTF(true, '增加edm', Yii::app()->db->getLastInsertID());
        } else {
            EchoUtility::echoMsgTF(false, '增加edm');
        }
    }

    public function actionAddEDMGroup()
    {
        //传入edm_id, 返回新建的EDM group
        $edm_id = $this->getEDMId();
        $request_data = $this->getPostJsonData();
        $newGroup = new HtEdmGroup();
        $newGroup['edm_id'] = $edm_id;
        $newGroup['title'] = '新的分组';
        $newGroup['title_link'] = '';
        $newGroup['display_order'] = $request_data['display_order'];
        $result = $newGroup->insert();
        if ($result) {
            $newGroup['group_id'] = Yii::app()->db->getLastInsertID();
            $newGroup['group_products'] = array();
            EchoUtility::echoMsgTF(true, '增加edm group', $newGroup);
        } else {
            EchoUtility::echoMsgTF(false, '增加edm group');
        }
    }

    public function actionAddEDMGroupProduct()
    {
        //传入product_id, 返回新建的EDM Group Product
        $request_data = $this->getPostJsonData();
        $group_id = $request_data['group_id'];
        $product_id = $request_data['product_id'];

        $existing_result = HtEdmGroupProduct::model()->findByPk(array('group_id' => $group_id, 'product_id' => $product_id));

        $product = Converter::convertModelToArray(HtProduct::model()->with('description',
                                                                           'cover_image')->findByPk($product_id));
        $product['prices'] = HtProductPricePlan::model()->getShowPrices($product_id);

        if (!empty($existing_result)) {
            EchoUtility::echoCommonFailed('已经有此商品');

            return;
        }
        if (empty($product['description']) || empty($product['prices'])) {
            EchoUtility::echoCommonFailed('商品信息缺少，请补全后再添加。');

            return;
        }

        $newGroupProduct = new HtEdmGroupProduct();
        //Yii::app()->params['urlHome']

        $data = array(
            'price'               => $product['prices']['price'],
            'group_id'            => $group_id,
            'product_id'          => $product_id,
            'orig_price'          => $product['prices']['orig_price'],
            'product_name'        => $product['description']['name'],
            'product_link'        => Yii::app()->params['urlHome'] . $product['link_url'],
            'product_image'       => $product['cover_image']['image_url'],
            'display_order'       => $request_data['display_order'],
            'product_description' => $product['description']['summary']
        );
        foreach ($data as $key => $val) {
            $newGroupProduct[$key] = $val;
        }

        $result = $newGroupProduct->insert();

        EchoUtility::echoMsgTF($result, '增加edm group product', $data);
    }

    public function actionUpdateEDMImg()
    {
        //传入edm_id和img_url，返回成功失败
        $edm_id = $this->getEDMId();
        $edm = HtEdm::model()->findByPk($edm_id);
        if (empty($edm)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $edm_id . '的EDM。');

            return;
        }

        $to_dir = 'image/upload/edm/' . $edm_id . '/head_img/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $edm['banner_image'] = $image_url;
            $result = $edm->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionUpdateProductImg()
    {
        //传入group_product_id和img_url，返回成功失败
        $edm_id = $this->getEDMId();
        $group_id = $this->getGroupId();
        $product_id = $this->getProductId();

        $edm_group_product = HtEdmGroupProduct::model()->findByPk(array('group_id' => $group_id, 'product_id' => $product_id));
        if (empty($edm_group_product)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '_' . $product_id . '的EDM。');

            return;
        }

        $to_dir = 'image/upload/edm/' . $edm_id . '/' . $group_id . '_' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $edm_group_product['product_image'] = $image_url;
            $result = $edm_group_product->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionUpdateEDM()
    {
        //传入edm对象，返回新的edm对象
        $data = $this->getPostJsonData();
        $data['date_update'] = date('Y-m-d H:i:s');
        $edm_id = $this->getEDMId();
        $edm = HtEdm::model()->findByPk($edm_id);
        $result = ModelHelper::updateItem($edm, $data,
                                          array('name', 'description', 'title', 'small_title', 'title_link', 'date_update'));
        EchoUtility::echoMsgTF($result, '更新EDM基本信息', $edm);
    }

    public function actionUpdateGroup()
    {
        //传入group对象，返回新的group对象
        $data = $this->getPostJsonData();
        $edm_group = HtEdmGroup::model()->findByPk($data['group_id']);
        $result = ModelHelper::updateItem($edm_group, $data,
                                          array('title', 'title_link', 'display_order'));
        EchoUtility::echoMsgTF($result, '更新EDM基本信息', $edm_group);
    }

    public function actionUpdateGroupProduct()
    {
        //传入group_product对象，返回新的group_product对象
        $data = $this->getPostJsonData();
        $edm_group_product = HtEdmGroupProduct::model()->findByPk(array('group_id' => $data['group_id'], 'product_id' => $data['product_id']));
        $result = ModelHelper::updateItem($edm_group_product, $data,
                                          array('product_name', 'product_description', 'product_link'));
        EchoUtility::echoMsgTF($result, '更新EDM基本信息', $edm_group_product);
    }

    public function actionUpdateGroupOrder()
    {
        //传入group list，返回新的group lists
        $data = $this->getPostJsonData();
        $edm_id = $this->getEDMId();
        foreach ($data as $group) {
            $edm_group = HtEdmGroup::model()->findByPk($group['group_id']);
            $result = ModelHelper::updateItem($edm_group, $group,
                                              array('display_order'));
            if (!$result) {
                EchoUtility::echoMsgTF(false, '更新EDM信息');

                return;
            }
        }
        EchoUtility::echoMsgTF(true, '更新EDM排序');
    }

    public function actionUpdateGroupProductOrder()
    {
        //传入group_product list，返回新的group_product lists
        $data = $this->getPostJsonData();
        $group_id = $this->getGroupId();
        foreach ($data as $gp) {
            $edm_gp = HtEdmGroupProduct::model()->findByPk(array('group_id' => $group_id, 'product_id' => $gp['product_id']));

            $result = ModelHelper::updateItem($edm_gp, $gp,
                                              array('display_order'));
            if (!$result) {
                EchoUtility::echoMsgTF(false, '更新商品排序');

                return;
            }
        }

        EchoUtility::echoMsgTF(true, '更新商品排序');
    }

    public function actionRemoveGroup()
    {
        //传入group_id，返回成功失败
        $group_id = $this->getGroupId();
        $result = HtEdmGroup::model()->deleteByPk($group_id);
        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    public function actionRemoveGroupProduct()
    {
        //传入group_product_id，返回成功失败
        $group_id = $this->getGroupId();
        $product_id = $this->getProductId();
        $result = HtEdmGroupProduct::model()->deleteByPk(array('group_id' => $group_id, 'product_id' => $product_id));
        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    public function actionDownloadEDMTemplate()
    {
        $edm_id = $this->getEDMId();
        $detail_info = $this->fetchEDMDetails($edm_id);
        if (!empty($detail_info)) {
            $root_dir = $this->getTmpDirectory($edm_id);
            if (!file_exists($root_dir . "img/")) {
                mkdir($root_dir . "img/", 0755, true);
            }

            // prepare images.
            foreach ($detail_info['groups'] as &$group) {
                $group_id = $group['group_id'];
                foreach ($group['group_products'] as &$product) {
                    $product_id = $product['product_id'];
                    $product['product_image'] = $this->downloadImage($product['product_image'], false, $group_id,
                                                                     $product_id, $root_dir);
                }
            }
            $img_dir = Yii::app()->basePath . '/..' . Yii::app()->params['EDM_IMAGE_DIR'];
            if (is_dir($img_dir)) {
                if ($handle = opendir($img_dir)) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            copy($img_dir . $entry, $root_dir . "img/" . $entry);
                        }
                    }
                    closedir($handle);
                }
            }
            if (!empty($detail_info['banner_image']) && strpos($detail_info['banner_image'], "/") !== 0) {
                $this->downloadImage($detail_info['banner_image'], true, 0, 0, $root_dir);
            }

            // render edm template with data.
            $template_dir = Yii::app()->basePath . '/..' . Yii::app()->params['BOOTSTRAP_BASE_URL'] . "/views/admin/edm/modules/templates/";
            $template_path = $template_dir . "template.php";
            $edm_content = Mail::templateRender($template_path, $detail_info);
            if ($edm_content == '') {
                exit("Failed to render template " . $template_path);
            }

            $generate_file_path = $root_dir . $edm_id . ".html";
            $fp = fopen($generate_file_path, "w+");
            if (is_writable($generate_file_path)) {
                fwrite($fp, $edm_content);
            }
            fclose($fp);

            // package template generated folder in zip.
            $zip_dir = substr($root_dir, 0, strlen($root_dir) - 9);
            $tmp_str = substr($root_dir, strlen($root_dir) - 9, 8);
            $zip_file = $zip_dir . $edm_id . "_" . $tmp_str . ".zip";
            $result = FileUtility::zipFiles($root_dir, $zip_file);
            if ($result == false) {
                EchoUtility::echoCommonFailed('下载EDM失败');

                return;
            }

            // Stream the file to the client
            header("Content-Type: application/zip");
            header("Content-Length: " . filesize($zip_file));
            header("Content-Disposition: attachment; filename=" . basename($zip_file));
            readfile($zip_file);
            unlink($zip_file);
        }
    }

    private function getEDMId()
    {
        return $this->getParam('edm_id');
    }

    private function getGroupId()
    {
        return $this->getParam('group_id');
    }

    private function getProductId()
    {
        return $this->getParam('product_id');
    }

    private function fetchEDMDetails($edm_id)
    {
        $result['data'] = HtEdm::model()->with('groups.group_products.product.description',
                                               'groups.group_products.product.cover_image')->findAllByAttributes(array('edm_id' => $edm_id));
        $result['data'] = Converter::convertModelToArray($result['data']);
        foreach ($result['data'] as &$edm) {
            foreach ($edm['groups'] as &$group) {
                foreach ($group['group_products'] as &$group_product) {
                    foreach ($group_product['product'] as &$product) {
                        $prices = HtProductPricePlan::model()->getShowPrices($product['product_id']);
                        $product['prices'] = $prices;
                    }
                }
            }
        }

        if (empty($result['data'])) {
            return [];
        } else {
            return $result['data'][0];
        }
    }

    private function downloadImage($url, $top_img, $group_id, $product_id, $generated_root_dir)
    {
        if ($top_img) {
            $result = HTTPRequest::request($url . "?imageView2/5/w/640/h/160");
        } else {
            $result = HTTPRequest::request($url . "?imageView2/5/w/580/h/250");
        }
        $path = "";
        if ($result['Status'] == 'OK') {
            $img_dir = $generated_root_dir . "img/";
            if (!file_exists($img_dir)) {
                mkdir($img_dir, 0755, true);
            }
            if ($top_img) {
                $path = $img_dir . "top.png";
                file_put_contents($path, $result['content']);
            } else {
                $file_name = "g" . $group_id . "p" . $product_id . ".jpg";
                $path = $img_dir . $file_name;
                file_put_contents($path, $result['content']);
            }
        }

        return $path;
    }

    private function getTmpDirectory($edm_id)
    {
        $tmp_dir = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < 8; $i++) {
            $tmp_dir .= $strPol[rand(0, $max)];
        }
        $generated_root_dir = Yii::app()->basePath . '/..' . Yii::app()->params['EDM_FILE_ROOT'] . $edm_id . '/' . $tmp_dir . '/';
        if (!file_exists($generated_root_dir)) {
            mkdir($generated_root_dir, 0755, true);
        }

        return $generated_root_dir;
    }
}
