<?php
require_once APPLICATION_PATH . '/controllers/Common/UnitTestController.php';
class AuthControllerTest extends UnitTestController
{
    public function setUp()
    {
        parent::setUp();
    }

    public function appBootstrap()
    {
        //$this->frontController
        //    ->registerPlugin(new Bugapp_Plugin_Initialize('development'));
    }

    public function testIndexActionShouldContainAuthForm()
    {
        $this->dispatch('/auth');
        $this->assertAction('auth');
        $this->assertQueryCount('form#authForm', 1);
    }
}