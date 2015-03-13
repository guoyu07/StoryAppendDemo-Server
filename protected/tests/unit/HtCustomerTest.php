<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 9/18/14
 * Time: 11:55 AM
 */
class HtCustomerTest extends CDbTestCase
{

    public function testAddCustomer()
    {
        $customer = HtCustomer::model()->addCustomer('xml1123@163.com', '123456', 'abcdef');
        $this->assertNotEmpty($customer->getErrors(), 'Should be failed!!!');

        $customer = HtCustomer::model()->addCustomer('xml11234567890@163.com', 'abcd123456', 'abcd123456');
        $this->assertEmpty($customer->getErrors(), 'This time should be ok.');

        $result = $customer->delete();

        $this->assertTrue($result, 'Delete should works ok.');

    }

    public function testGetCustomer() {
        $result = HtCustomer::model()->getCustomer('xml1123@163.com');
        $this->assertNotEmpty($result, 'Account should be found.');

        $result = HtCustomer::model()->getCustomer('13988888888');

        $this->assertEmpty($result,'User should not be exists.');
    }


} 