<?php
require_once APPLICATION_PATH . '/controllers/Common/ParentController.php';

class User_IndexController extends ParentController {

    public function init()
    {
        parent::init();

        if (CORE_User::getGroup() == CORE_User::GROUP_GUEST) {
            $this->redirect($this->view->url(array(),'auth'));
        }

        $this->view->headScript()->prependFile('/js/processes.js', 'text/javascript');
        $this->usePopovers();
        //$this->_helper->layout->setLayout('layout');
    }

    public function indexAction()
    {
        $this->setTitle('Вы ' . CORE_User::getLogin());
        $this->view->pageTitle = 'Процессы';

    }

    public function addAction()
    {
        $this->setTitle('Новый процесс');
        $this->view->pageTitle = 'Новый процесс';
        $form = new Application_Form_ProcessAdd();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $formValues = $form->getValues();
                /**
                 * Проверка API
                 */
                CORE_API_Request::execute(Zend_Registry::get('config')->site->url . $this->view->url(array(),'api_add'),
                                            array(
                                               'title' => $formValues['title'],
                                               'duration' => $formValues['duration']
                                            )
                                        );

                $this->fmessage->addMessage('Процесс добавлен');
                $this->redirect( $this->view->url(array(),'processes'));
            }
        }

        $this->view->form = $form;
    }

}