<?php

class TestController extends Controller
{
    public function actionShipping()
    {
        $order_id = $this->getParam('order_id');
        $preview = $this->getParam('preview', 1);
        $data = Yii::app()->shipping->shippingOrder($order_id, $preview);
        if (!$preview) {
            Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_SHIPPED);
        }
        if (is_string($data))
            echo $data;
        else
            echo json_encode($data);
    }

    public function actionClearCache()
    {
        Yii::app()->cache->flush();
        echo '<h2>' . date('Y-m-d H:i:s') . ' -- Cache Cleared!</h2>';
    }

    public function actionGetPassengers($order_id = 0)
    {
        if (!empty($order_id)) {
            $data = HtOrderPassenger::model()->findAllByOrder($order_id);

            EchoUtility::echoByResult($data);

        } else {
            echo 'No order_id supplied.';
        }

    }

    public function actionGetHowItWorksTitles()
    {
        $titles_all = [];
        $titles_all_with_product_id = [];

        $criteria = new CDbCriteria();
        $criteria->addCondition('p.status=3');
        $criteria->addCondition('p.product_id < 800');
//        $criteria->addInCondition('p.product_id', [737, 1087, 1119, 1271]);

        $products_all = HtProduct::model()->with('description')->findAll($criteria);
        foreach ($products_all as $product) {
            $product_id = $product['product_id'];
            $how_it_works = $product['description']['how_it_works'];

            $decoded_how_it_works = rawurldecode(html_entity_decode($how_it_works));
            $json = json_decode($decoded_how_it_works, true);

//            var_dump($json);
            $md_text = $json['md_text'];

//            $md_html = $json['md_html'];
            $titles = [];
//            if (preg_match_all("/<h2>([^<]*)<\/h2>/i", $md_html, $titles)) {
            if (preg_match_all("/##([^\n]*)/i", $md_text, $titles)) {
//                var_dump($titles);
                foreach ($titles[1] as $title) {
                    if (!in_array($title, $titles_all)) {
                        $titles_all_with_product_id[$title] = [$product_id];
                        array_push($titles_all, $title);
                    } else {
                        $titles_all_with_product_id[$title][] = $product_id;
                    }
                }
            }
        }

        echo "<h2>Titles:</h2>";
//        var_dump($titles_all_with_product_id);
        foreach ($titles_all_with_product_id as $title => $product_ids) {
            echo "<h3>" . $title . " " . implode(",", $product_ids) . "</h3>";
        }
    }

    public function actionRegulateHowItWorksTitles()
    {
        require_once 'Michelf/Markdown.inc.php';


        $criteria = new CDbCriteria();
        $criteria->addCondition('p.status=3');
        $criteria->addCondition('p.product_id < 800');

        $products_all = HtProduct::model()->with('description')->findAll($criteria);
        foreach ($products_all as $product) {
            $product_id = $product['product_id'];
            $how_it_works = $product['description']['how_it_works'];

            $decoded_how_it_works = rawurldecode(html_entity_decode($how_it_works));
            $json = json_decode($decoded_how_it_works, true);

            //            var_dump($json);
            $md_text = $json['md_text'];

            //            $md_html = $json['md_html'];
            $titles = [];
            //            if (preg_match_all("/<h2>([^<]*)<\/h2>/i", $md_html, $titles)) {
            if (preg_match_all("/##([^\n]*)/i", $md_text, $titles)) {
                //                var_dump($titles);
                foreach ($titles[1] as $title) {
                    $regulated_title = $this->regulatedTitle($title);
                    if ($regulated_title != $title) {
                        $regulate_info[$regulated_title][] = $product_id . ' ' . $title;

                        $md_text = str_replace('##' . $title, '##' . $regulated_title, $md_text);
                        $md_html = \Michelf\Markdown::defaultTransform($md_text);
                        $json = array('md_text' => $md_text, 'md_html' => $md_html);
                        $new_how_it_works = rawurlencode(json_encode($json));

                        $description = $product['description'];
                        $description['how_it_works'] = $new_how_it_works;
                        $result = $description->update();
                    }
                }
            }
        }

        echo "<h2>Titles changed:</h2>";
        //        var_dump($titles_all_with_product_id);
        foreach ($regulate_info as $title => $product_id_title) {
            echo "<h3>" . $title . "</h3><h3>    " . implode(", ", $product_id_title) . "</h3>";
        }

    }

    private function regulatedTitle($title)
    {
        $title_regular_rule = [
            '购买须知' => ['购买须知(请仔细阅读）', '预定须知', '购买规则', '购买信息', '购买和退改规则', '退改规则', '购买须知：', '预订须知'],
            '使用方法' => ['如何使用', '兑换方法', '使用须知', '兑换须知', '兑换说明', '使用说明', '使用须知：', '如何兑换', '使用方法（请仔细阅读）',
                '兑换及入口', '出行须知', '使用说明（请您仔细阅读）', '如何使用（请仔细阅读）', '购买限制'],
            '注意事项' => ['玩途提醒您', '玩途提醒你', '玩途提醒', '特别提醒', '注意事项：', '注意事项（请您仔细阅读）', '退改规则', '拍摄注意事项（NOTICE）：',
                '玩途提醒您（请仔细阅读）', '退款规则', '退订规则', '玩途提醒你（请仔细阅读）', '玩提醒你', '玩途提醒您：', '玩提醒您', '活动说明', '玩途声明', '出行准备建议']
        ];

        foreach ($title_regular_rule as $key => $value) {
            if (in_array($title, $value)) {
                return $key;
            }
        }

        return $title;
    }
}