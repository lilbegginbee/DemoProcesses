<?php
require_once APPLICATION_PATH . '/controllers/Common/UnitTestController.php';
class Admin_IndexControllerTest extends UnitTestController
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCallWithoutActionShouldPullFromIndexAction()
    {
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testShouldContainLoginForm()
    {
        $this->dispatch('/admin');
        $this->assertAction('index');
        $this->assertRedirectTo('/auth');

        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET')
            ->setPost(array());

        $this->dispatch('/auth');
        $this->assertQueryCount('form#authForm', 1);
    }

    public function testValidLoginShouldGoToDashboard()
    {
        $this->request->setMethod('POST')
            ->setPost(array(
                'login' => 'admin',
                'password' => 'password'
            ));
        $this->dispatch('/auth');
        $this->assertRedirectTo('/');

    }
}