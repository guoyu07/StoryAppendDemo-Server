<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/6/2
 * Time: 下午3:07
 */

class BookController extends BaseController {

    public function actionGetBookList() {
        $books = Converter::convertModelToArray(HiBook::model()->with('customer')->findAll());
        EchoUtility::echoMsgTF(1, '获取书籍列表', $books);
    }

    public function actionGetBookDetail() {
        $book_id = $this->getParam('book_id');
        $book = Converter::convertModelToArray(HiBook::model()->with('customer')->findByPk($book_id));
        EchoUtility::echoMsgTF(1, '获取书籍', $book);
    }

    public function actionAddBook() {
        $customer_id = $this->getParam('customer_id');
        $title = $this->getParam('title');
        $content = $this->getParam('content');
        $book = new HiBook();
        $book['title'] = $title;
        $book['content'] = $content;
        $book['customer_id'] = $customer_id;
        $book['insert_time'] = date('Y-m-d H:i:s',time());
        $book->insert();
        EchoUtility::echoMsgTF(true, '添加段子', $book);
    }
}