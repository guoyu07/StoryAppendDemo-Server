<?php

class SupplierController extends AdminController
{
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '供应商管理';

        $request_urls = array(
            'editVendorUrl' => $this->createUrl('supplier/edit', array('supplier_id' => '')),
            'addVendor' => $this->createUrl('supplier/addSupplier')
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '供应商编辑';

        $supplier_id = $this->getSupplierID();

        $request_urls = array(
            'supplierInfo' => $this->createUrl('supplier/supplierInfo', array('supplier_id' => $supplier_id)),
            'supplierContact' => $this->createUrl('supplier/supplierContact', array('contact_id' => '')),
            'supplierLocalSupport' => $this->createUrl('supplier/supplierLocalSupport', array('support_id' => '')),
            'addVendorImage' => $this->createUrl('supplier/addSupplierImage', array(
                    'supplier_id' => $supplier_id
                ))
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('edit');
    }

    public function actionSupplierInfo()
    {
        $supplier_id = $this->getSupplierID();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
            $supplier = HtSupplier::model()->findByPk($supplier_id);

            $contacts = $this->getSupplierContacts();

            $local_supports = $this->getSupplierLocalSupport();

            EchoUtility::echoMsgTF(true, '获取供应商信息', array(
                'supplier' => $supplier,
                'supplier_contacts' => $contacts,
                'supplier_local_supports' => $local_supports
            ));
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();

            $supplier = HtSupplier::model()->findByPk($supplier_id);

            $result = ModelHelper::updateItem($supplier, $data, array());

            EchoUtility::echoMsgTF(!empty($result), '更新供应商信息');
        }
    }

    public function actionSupplierContact()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $contact_id = $data["contact_id"];

            if (!empty($contact_id)) {
                $supplier_contact = HtSupplierContacts::model()->findByPk($contact_id);
                if (!empty($supplier_contact)) {
                    $result = ModelHelper::updateItem($supplier_contact, $data, array());
                }

                EchoUtility::echoMsgTF(!empty($result), '供应商联系人保存', $data);
            } else {
                $new_contact = new HtSupplierContacts();

                foreach ($data as $key => $value) {
                    $new_contact[$key] = $value;
                }
                $result = $new_contact->insert();
                if ($result) {
                    $new_contact["contact_id"] = Yii::app()->db->getLastInsertID();
                }

                EchoUtility::echoMsgTF(!empty($result), '供应商联系人新增', Converter::convertModelToArray($new_contact));
            }
        } else if ($request_method == 'delete') {
            $contact_id = Yii::app()->request->getParam('contact_id');
            $supplier_contact = HtSupplierContacts::model()->findByPk($contact_id);

            if (!empty($contact_id)) {
                $result = $supplier_contact->delete();
                EchoUtility::echoMsgTF(!empty($result), '删除供应商联系人');
            } else {
                EchoUtility::echoCommonFailed('删除供应商联系人失败');
            }
        }
    }

    public function actionSupplierLocalSupport()
    {
        $data = $this->getPostJsonData();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'post') {
            $support_id = $data["support_id"];

            if (!empty($support_id)) {
                $local_support = HtSupplierLocalSupport::model()->findByPk($support_id);
                if (!empty($local_support)) {
                    $result = ModelHelper::updateItem($local_support, $data, array());
                }
                EchoUtility::echoMsgTF(!empty($result), '对应的客服信息', $data);
            } else {
                $new_support = new HtSupplierLocalSupport();

                foreach ($data as $key => $value) {
                    $new_support[$key] = $value;
                }

                $new_support["language_id"] = '2';

                $result = $new_support->save();

                if ($result) {
                    $newSupport["support_id"] = Yii::app()->db->getLastInsertID();
                }
                EchoUtility::echoMsgTF(!empty($result), '对应的客服信息', Converter::convertModelToArray($new_support));
            }
        } else if ($request_method == 'delete') {
            $support_id = Yii::app()->request->getParam('support_id');
            $local_support = HtSupplierLocalSupport::model()->findByPk($support_id);
            if (!empty($local_support)) {
                $result = $local_support->delete();
                EchoUtility::echoMsgTF(!empty($result), '删除供应商当地客服', $result);
            } else {
                EchoUtility::echoCommonFailed('删除供应商当地客服失败');
            }
        }
    }

    public function actionAddSupplier()
    {
        $data = $this->getPostJsonData();
        $supplier = new HtSupplier();
        $supplier['name'] = $data['en_name'];
        $supplier["sort_order"] = 0;

        if ($supplier->save()) {
            $id = Yii::app()->db->getLastInsertID();
        }

        EchoUtility::echoMsgTF(!empty($id), "创建供应商", array('supplier_id' => $id));
    }

    public function actionAddSupplierImage()
    {
        $supplier_id = $this->getSupplierID();
        $to_dir = 'image/upload/suppliers/' . $supplier_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            EchoUtility::echoMsgTF(true, "供应商图片", $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    private function getSupplierID()
    {
        return Yii::app()->request->getParam('supplier_id');
    }

    private function getSupplierContacts()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('supplier_id = ' . $this->getSupplierID());

        $supplierContacts = HtSupplierContacts::model()->findAll($criteria);

        return $supplierContacts;
    }

    private function getSupplierLocalSupport()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('supplier_id = ' . $this->getSupplierID());

        $supplierLocalSupports = HtSupplierLocalSupport::model()->findAll($criteria);

        foreach ($supplierLocalSupports as $support) {
            if (empty($support["phone"])) {
                $support["phone"] = $support["international"];
            }
        }

        return $supplierLocalSupports;
    }

    public function actionGetSuppliers()
    {
        $c = new CDbCriteria();
        $c->select = array(
            'supplier_id',
            'name'
        );
        $c->order = 'name ASC';
        $result = HtSupplier::model()->findAll($c);

        echo CJSON::encode(array(
                               'code' => 200,
                               'msg' => '',
                               'data' => $result
                           ));
    }
}