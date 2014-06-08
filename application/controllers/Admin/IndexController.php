<?php
require_once APPLICATION_PATH . '/controllers/Common/ParentController.php';

class Admin_IndexController extends ParentController {

    public function init()
    {
        parent::init();

        if (CORE_User::getGroup() != CORE_User::GROUP_ADMIN) {
            $this->redirect($this->view->url(array(),'auth'));
        }

        //$this->_helper->layout->setLayout('layout');
    }

    public function indexAction()
    {
        $this->setTitle('Вы ' . CORE_User::getName());
        $this->view->pageTitle = 'Дэшборд';


    }


} 