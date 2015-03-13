<?php

return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
        'components' => array(
            'fixture' => array(
                'class' => 'system.test.CDbFixtureManager',
            ),

            'db' => array(
                'connectionString' => 'mysql:host=dev.hitour.cc;dbname=hicart',
                'emulatePrepare' => true,
                'username' => 'hitour',
                'password' => 'cqzs01@hitour',
                'charset' => 'utf8',
            ),
        ),
    )
);
