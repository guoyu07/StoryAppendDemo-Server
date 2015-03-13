<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 11/21/14
 * Time: 11:55 AM
 */
class ColumnController extends Controller
{

    public function actionIndex()
    {
        $column_id = (int)$this->getParam('column_id', 0);
        $debug = $this->getParam('debug', false);
        if (empty($column_id)) {
            $this->render('demo');
            return;
        }
        $data = $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'bindingProduct' => $this->createUrl('product/bindingProduct')
            )
        );
        $articles = $this->getArticle($column_id);
        $data['article'] = $articles['data'];
        $data['product_link'] = Yii::app()->urlManager->createUrl('product/index', array('product_id' => '000'));
        if ($debug) {
            EchoUtility::echoJson($data['article']);
        } else {
            $seo_setting = HtSeoSetting::model()->findByArticleId($column_id);
            $this->initDataBySEOSetting($seo_setting);
            $this->render('main', $data);
        }
    }

    public function actionArticleDetail()
    {
        $result = $this->getArticle($this->getArticleId());
        if ($result['error']) {
            EchoUtility::echoCommonFailed($result['msg']);
        } else {
            EchoUtility::echoJson($result['data']);
        }
    }

    private function getArticle($article_id)
    {
        if (empty($article_id)) {
            return array('error' => true, 'msg' => 'Invalid article_id.');
        }

        $article = HtArticle::model()->getArticle($article_id);
        if (empty($article)) {
            return array('error' => true, 'msg' => 'Failed to get article data');
        }

        $city = Converter::convertModelToArray(HtCity::model()->findByPk($article['city_code']));
        $article['city'] = array(
            'cn_name' => $city['cn_name'],
            'link_url' => $city['link_url'],
            'city_code' => $city['city_code'],
            'country_cn_name' => $city['country_cn_name'],
        );
        $article['other_articles'] = HtArticle::model()->getCityArticles($article['city_code'], false, $article_id);
        if(!empty($article['other_articles']['data'])) {
            foreach ($article['other_articles']['data'] as &$a) {
                $a['link_url'] = $this->createUrl('column/index', array('column_id' => $a['article_id']));
            }
        }

        $product_all = [];
        foreach ($article['sections'] as &$s) {
            $items_to_be_deleted = [];
            foreach ($s['items'] as $key => &$i) {
                if ($i['type'] == 3) { //商品
                    $product = HtArticle::model()->getProductInfo($i['product_id']);
                    if($product) {
                        $product_all[] = $product;
                        $i['product_detail'] = $product;
                    } else { //If product is not available
                        $items_to_be_deleted[] = $key;
                    }
                }
            }
            if (!empty($items_to_be_deleted)) {
                foreach ($items_to_be_deleted as $key) {
                    unset($s['items'][$key]);
                }
            }
        }
        $product_all_mobile = [];
        foreach($product_all as $key => $value){
            $product_all_mobile[$key] = $value;
            if($key >= 1){
                break;
            }
        }
        $product_all_count = sizeof($product_all);
        $article['product_all_count'] = $product_all_count;
        $article['product_all_mobile'] = $product_all_mobile;
        $article['product_all'] = $product_all;

        return array('error' => false, 'data' => $article);
    }

    private function getArticleId()
    {
        return (int)$this->getParam('article_id', 0);
    }
}