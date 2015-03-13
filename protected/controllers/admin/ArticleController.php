<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 11/18/14
 * Time: 5:06 PM
 */
class ArticleController extends AdminController
{
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '文章管理';

        $request_urls = array(
            'fetchArticles' => $this->createUrl('article/articles'),
            'article' => $this->createUrl('article/article', array('article_id' => '')),
            'editArticle' => $this->createUrl('article/edit', array('article_id' => '')),
            'updateArticleStatus' => $this->createUrl('article/updateArticleStatus', array('article_id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '文章编辑';

        $article_id = $this->getArticleId();

        $request_urls = array(
            'viewArticleLink' => $this->createUrl('column/index', array('column_id' => $article_id), '', false),
            'article' => $this->createUrl('article/article', array('article_id' => $article_id)),
            'articleSection' => $this->createUrl('article/articleSection', array('article_id' => $article_id, 'section_id' => '')),
            'articleSectionItem' => $this->createUrl('article/articleSectionItem', array('section_id' => '000', 'item_id' => '')),
            'updateArticleHeadImage' => $this->createUrl('article/updateArticleHeadImage'),
            'updateArticleSectionImage' => $this->createUrl('article/updateArticleSectionImage'),
            'updateArticleStatus' => $this->createUrl('article/updateArticleStatus', array('article_id' => $article_id)),
            'getArticleProductInfo' => $this->createUrl('article/getArticleProductInfo', array('product_id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    public function actionArticles()
    {
        $data = $this->getPostJsonData();

        $c = new CDbCriteria();
        $total_count = new CDbCriteria();

        if (isset($data['query_filter'])) {
            if(!empty($data['query_filter']['search_text'])) {
                $c->addCondition('article_id = "' . $data['query_filter']['search_text'] .'"');
                $c->addCondition('title like "%' . $data['query_filter']['search_text'] . '%"', 'OR');
                $total_count->addCondition('article_id = "' . $data['query_filter']['search_text'].'"');
                $total_count->addCondition('title like "%' . $data['query_filter']['search_text'] . '%"', 'OR');
            }
            if(!empty($data['query_filter']['city_code'])) {
                $c->addCondition('city_code = "' . $data['query_filter']['city_code'] .'"');
                $total_count->addCondition('city_code = "' . $data['query_filter']['city_code'].'"');
            }
        }

        if($data['sort']){
            $order = '';
            foreach ($data['sort'] as $order_field => $order_dir) {
                $order .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
            }
            $c->order = substr($order, 2);
        }else{
            $c->order = 'date_added DESC';
        }

        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];

        $result = Converter::convertModelToArray(HtArticle::model()->findAll($c));
        foreach($result as &$a) {
            $city = Converter::convertModelToArray(HtCity::model()->getByCode($a['city_code']));
            $a['city_name'] = $city['cn_name'];
        }

        EchoUtility::echoMsgTF(true, '获取文章列表', array(
            'total_count' => HtArticle::model()->count($total_count),
            'data' => $result
        ));
    }

    //文章操作
    public function actionArticle()
    {
        // TODO get/add/update/delete an article
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        $article_id = $this->getArticleId();

        if ('get' == $request_method) {
            $article = HtArticle::model()->with('seo','sections.items.product_detail.description','sections.items.product_detail.cover_image')->findByPk($article_id);
            $article = Converter::convertModelToArray($article);
            foreach ($article['sections'] as &$sec) {
                foreach($sec['items'] as &$item){
                    if($item['type'] == 3){
                        $comment_stat = HtProductComment::model()->getStatInfo($item['product_id']);
                        $show_prices = HtProductPricePlan::model()->getShowPrices($item['product_id']);
                        $item['product_detail']['comment_stat'] = $comment_stat;
                        $item['product_detail']['show_prices'] = $show_prices;
                    }
                }
            }

            EchoUtility::echoByResult($article, '', '未找到ID为“' . $article_id . '”的Article.');
        } else if ('post' == $request_method) {
            $data = $this->getPostJsonData();

            if (empty($article_id)) {
                $article = new HtArticle();
                $result = $article->insert();
                if ($result) {//文章SEO
                    $seo = new HtSeoSetting();
                    $seo['type'] = HtSeoSetting::TYPE_ARTICLE;
                    $seo['id'] = $article->getPrimaryKey();
                    $seo->insert();
                }
                EchoUtility::echoMsgTF($result, '添加',array('article_id'=>$article->getPrimaryKey()));
            } else {
                $article = HtArticle::model()->findByPk($article_id);
                $result = ModelHelper::updateItem($article, $data, ['city_code', 'category', 'head_image_url', 'title', 'brief', 'link_to']);
                $seo = HtSeoSetting::model()->findByAttributes(array('type' => HtSeoSetting::TYPE_ARTICLE, 'id' => $article_id));
                if ($seo){
                    ModelHelper::updateItem($seo, $data['seo'], array('title', 'description', 'keywords'));
                }else{
                    $seo = new HtSeoSetting();
                    $seo['type'] = HtSeoSetting::TYPE_ARTICLE;
                    $seo['id'] = $article_id;
                    ModelHelper::fillItem($seo,$data['seo'],array('title', 'description', 'keywords'));
                    $seo->insert();
                }

                EchoUtility::echoMsgTF(1==$result, '更新',Converter::convertModelToArray($article));
            }
        } else if ('delete' == $request_method) {
            HtArticleSection::model()->deleteAllByAttributes(['article_id' => $article_id]);
            HtArticle::model()->deleteByPk($article_id);

            EchoUtility::echoMsgTF(true, '删除');
        }
    }

    //文章内段落
    public function actionArticleSection()
    {
        $section_id = $this->getSectionId();
        $article_id = $this->getArticleId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ('post' == $request_method) {
            $data = $this->getPostJsonData();
            if (empty($section_id)) {
                //调整顺序
                $sections = HtArticleSection::model()->findAll('article_id = '.$article_id);
                $change_order = 0;
                foreach($sections as $sec){
                    if($sec['display_order'] == $data['display_order']){
                        $change_order = 1;
                    }
                }
                if($change_order){
                    foreach($sections as $sec){
                        if($sec['display_order'] >= $data['display_order']){
                            $display_order = $sec['display_order'] + 1;
                            $section = HtArticleSection::model()->findByPk($sec['section_id']);
                            $section['display_order'] = $display_order;
                            $section->update();
                        }
                    }
                }

                $section = new HtArticleSection();
                $data['article_id'] = $article_id;
                ModelHelper::fillItem($section, $data, ['article_id','display_order']);
                $result = $section->insert();

                EchoUtility::echoMsgTF($result, '添加',array(
                    'section_id'=>$section->getPrimaryKey(),
                    'article_id' => $this->getArticleId()
                ));
            }else{
                $section = HtArticleSection::model()->findByPk($section_id);
                $result = ModelHelper::updateItem($section, $data, ['section_title']);
                EchoUtility::echoMsgTF(1==$result, '更新',Converter::convertModelToArray($section));
            }
        }else if ('delete' == $request_method) {
            $section = HtArticleSection::model()->findByPk($section_id);
            $sec_display_order = $section['display_order'];

            HtArticleSection::model()->deleteByPk($section_id);
            HtArticleSectionItem::model()->deleteAll('section_id = '.$section_id);

            $sections = HtArticleSection::model()->findAll('article_id = '.$article_id);
            foreach($sections as $sec){
                if($sec['display_order'] >= $sec_display_order){
                    $display_order = $sec['display_order'] - 1;
                    $section = HtArticleSection::model()->findByPk($sec['section_id']);
                    $section['display_order'] = $display_order;
                    $section->update();
                }
            }
            EchoUtility::echoMsgTF(true, '删除');
        }
    }

    //文章段落内项目
    public function actionArticleSectionItem()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $item_id = $this->getItemId();
        $section_id = $this->getSectionId();
        if ('post' == $request_method) {
            $data = $this->getPostJsonData();
            $data['section_id'] = $section_id;
            if (!$item_id) {
                //调整顺序
                $items = HtArticleSectionItem::model()->findAll('section_id = '.$section_id);
                $change_order = 0;
                foreach($items as $item){
                    if($item['display_order'] == $data['display_order']){
                        $change_order = 1;
                    }
                }
                if($change_order){
                    foreach($items as $it){
                        if($it['display_order'] >= $data['display_order']){
                            $display_order = $it['display_order'] + 1;
                            $item = HtArticleSectionItem::model()->findByPk($it['item_id']);
                            $item['display_order'] = $display_order;
                            $item->update();
                        }
                    }
                }

                $item = new HtArticleSectionItem();
                switch($data['type']){
                    case 1://文本
                        ModelHelper::fillItem($item, $data, ['section_id', 'type', 'text_content', 'display_order']);
                        break;
                    case 2://图片
                        ModelHelper::fillItem($item, $data, ['section_id', 'type', 'image_url', 'image_title', 'image_description', 'display_order']);
                        break;
                    case 3://商品
                        ModelHelper::fillItem($item, $data, ['section_id', 'type', 'product_id', 'product_title', 'product_description', 'display_order']);
                        break;
                    default:
                        ModelHelper::fillItem($item, $data, ['section_id', 'type', 'text_content', 'display_order']);
                        break;
                }
                $result = $item->insert();
                EchoUtility::echoMsgTF($result, '添加',array('item_id'=>$item->getPrimaryKey()));
            }else{
                $item = HtArticleSectionItem::model()->findByPk($item_id);
                switch($data['type']){
                    case 1://文本
                        $result = ModelHelper::updateItem($item, $data, ['text_content']);
                        break;
                    case 2://图片
                        $result = ModelHelper::updateItem($item, $data, ['image_url', 'image_title', 'image_description']);
                        break;
                    case 3://商品
                        $result = ModelHelper::updateItem($item, $data, ['product_id', 'product_title', 'product_description']);
                        break;
                    default:
                        $result = ModelHelper::updateItem($item, $data, ['text_content']);
                        break;
                }

                EchoUtility::echoMsgTF(1==$result, '更新',Converter::convertModelToArray($item));
            }
        }else if ('delete' == $request_method) {
            $item = HtArticleSectionItem::model()->findByPk($item_id);
            $item_display_order = $item['display_order'];

            HtArticleSectionItem::model()->deleteByPk($item_id);

            $items = HtArticleSectionItem::model()->findAll('section_id = '.$section_id);
            foreach($items as $it){
                if($it['display_order'] >= $item_display_order){
                    $display_order = $it['display_order'] - 1;
                    $item = HtArticleSectionItem::model()->findByPk($it['item_id']);
                    $item['display_order'] = $display_order;
                    $item->update();
                }
            }
            EchoUtility::echoMsgTF(true, '删除');
        }
    }

    //更新文章头图
    public function actionUpdateArticleHeadImage()
    {
        $article_id = $_POST['article_id'];
        $article = HtArticle::model()->findByPk($article_id);
        if (empty($article)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $article_id . '的文章。');
            return;
        }

        $to_dir = 'image/upload/article/' . $article_id . '/head_img/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $article['head_image_url'] = $image_url;
            $result = $article->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //更新文章内图片
    public function actionUpdateArticleSectionImage()
    {
        $article_id = $_POST['article_id'];
//        $item_id = $_POST['item_id'];
//        $item = HtArticleSectionItem::model()->findByPk($item_id);
//        if (empty($item)) {
//            EchoUtility::echoCommonFailed('未找到ID为' . $item_id . '的项目。');
//            return;
//        }

        $to_dir = 'image/upload/article/' . $article_id . '/article_img/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

//            $item['image_url'] = $image_url;
//            $result = $item->update();

            EchoUtility::echoMsgTF(true, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //更改文章状态
    public function actionUpdateArticleStatus()
    {
        $article_id = $this->getArticleId();
        $article = HtArticle::model()->findByPk($article_id);
        if (empty($article)) {
            EchoUtility::echoCommonFailed('找不到ID为' . $article . '的文章。');

            return;
        }
        $data = $this->getPostJsonData();
        $result = ModelHelper::updateItem($article, $data,['status']);
        EchoUtility::echoMsg($result, '更改文章状态', '', Converter::convertModelToArray($article));
    }

    private function getArticleId()
    {
        return (int)Yii::app()->request->getParam('article_id');
    }

    private function getSectionId()
    {
        return (int)Yii::app()->request->getParam('section_id');
    }

    private function getItemId()
    {
        return (int)Yii::app()->request->getParam('item_id');
    }

    private function getProductId()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    public function actionGetArticleProductInfo()
    {
        $product_id = $this->getProductId();
        $product_info = HtProduct::model()->with('description','cover_image')->findByPk($product_id);
        if(!$product_info){
            EchoUtility::echoCommonFailed('商品不存在');
            return;
        }
        $product_info = Converter::convertModelToArray($product_info);
        $comment_stat = HtProductComment::model()->getStatInfo($product_id);
        $show_prices = HtProductPricePlan::model()->getShowPrices($product_id);
        $product_info['comment_stat'] = $comment_stat;
        $product_info['show_prices'] = $show_prices;
        EchoUtility::echoMsgTF(true, '获取文章列表',$product_info);
    }
}