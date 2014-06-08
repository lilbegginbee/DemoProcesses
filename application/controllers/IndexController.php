<?php

require_once 'Common/ParentController.php';

class IndexController extends ParentController
{

    public function init()
	{
		parent::init();
        //$this->_helper->layout->setLayout('layout');

        switch(CORE_User::getRole()) {
            case CORE_User::ROLE_ADMIN:
                $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble( array(), 'adminDashboard'));
                break;
            case CORE_User::ROLE_PERSON:
                $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble( array(), 'processes'));
                break;
        }
	}

    public function indexAction()
    {
        $this->setTitle('Вы гость');
        $this->view->pageTitle = 'Задание';
    }
}