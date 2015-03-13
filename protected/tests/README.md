# PHPUnit test for yii

## First of all:

1. Install phpunit by brew:

    brew install phpunit

## Pre-requirement for add/run tests that do not use selenium

1. Edit config/test.php to add db config;
1. Add your test to extends from CDbTestCase;


## Pre-requirement for add/run tests that use selenium

1. Add path of PHPUnit to php include path;
1. Get phpunit-selenium from Github -- https://github.com/giorgiosironi/phpunit-selenium;
1. Copy selenium extenstions to PHPUnit/extenstions;
1. Edit the following line in **HiWebTestCase.php** according to your local configuration:

    define('TEST_BASE_URL', 'http://hitour.local/');

1. Download jar file of selenium-server-standalone;
1. Remember to run selenium-server-standalone first when you want to run the unit tests;

## To run the test:

Open a terminal window and change dir to protected/tests, then type the following command:

    phpunit -v functional

Or

    phpunit -v functional/SiteTest.php
