<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/6/9
 * Time: 下午5:28
 */

class QuestionController extends BaseController{

    public function actionGetCurrentQuestion() {
        $customer_id = $this->getParam('customer_id');
        $flag = $this->getParam('flag');
        $current_id = array();
        if($flag == 'today') {
            $current_id = 2;
        } else {
            $current_id = 1;
        }
        $question = Converter::convertModelToArray(HiQuestion::model()->findByPk($current_id));
        $answers = Converter::convertModelToArray(HiQuestionAnswer::model()->with('customer')->findAllByAttributes(array('customer_id' => $customer_id, 'question_id' => $current_id)));
        if($flag == 'today') {
            $question['has_answered'] = 0;
            $year = date('Y');
            foreach($answers as $k => $v) {
                if(strpos('date' . $v['insert_date'], $year)) {
                    $question['has_answered'] = 1;
                }
            }
        } else {
            $question['has_answered'] = 1;
        }
        $question['answers'] = $answers;
        EchoUtility::echoMsgTF($question, '获取问题', $question);
    }

    public function actionAddNewAnswer() {
        $newAnswer = $this->getParam("new_answer");
        $customer_id = $this->getParam("customer_id");
        $question_id = $this->getParam("question_id");
        $answer = new HiQuestionAnswer();
        $answer['content'] = $newAnswer;
        $answer['customer_id'] = $customer_id;
        $answer['question_id'] = $question_id;
        $answer['insert_date'] = date('Y-m-d');
        $result = $answer->insert();
        EchoUtility::echoMsgTF($result, '回答问题', $answer);
    }

}