<?php
require_once 'Common/ParentController.php';

class AuthController extends ParentController
{
    public function init()
    {
		//$this->view->headLink()->appendStylesheet('/css/fonts/news_gothic/stylesheet.css', 'screen', false, array('compress' => true));
		//$this->view->headLink()->appendStylesheet('/theme/css/font-awesome.min.css', 'screen', false, array('compress' => true));
		parent::init();
    }

    public function authAction()
    {
        $this->setTitle('Авторизация');
        $this->view->pageTitle = 'Авторизация';
        $request = $this->getRequest();
        $form = new Application_Form_Auth();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $formValues = $form->getValues();

                $mUsers = new Model_Users();

                if ($user = $mUsers->auth($formValues['login'], $formValues['password'])) {
                    $user = CORE_User::setUser($user);
                    $mUsers->update(array('last_login' => date('Y-m-d H:i:s')), "id_user = {$user->id_user}");
                } else {
                    $form->setErrorMessages(array('Неверное сочетание логин/пароль'));
                    $form->markAsError();
                }

                if (CORE_User::getRole() != CORE_User::ROLE_GUEST) {
                    // авторизация прошла
                    Zend_Session::rememberMe(86400);
                    $this->redirect(CORE_User::getHomePage());
                }
            }
        }

        $this->view->form = $form;
    }

    public function registrationAction()
    {
        $this->setTitle('Регистрация');
        $this->view->pageTitle = 'Регистрация';
        $request = $this->getRequest();
        $form = new Application_Form_Registration();

        $this->view->done = false;
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $formValues = $form->getValues();
                $mUsers = new Model_Users();

                $login = $formValues['login'];
                $pass = $formValues['password'];
                $email = $formValues['email'];

                $customValid = true;
                if ($mUsers->loginOrEmailExists($login, $email)) {
                    $form->addError('Такой email и(или) логин уже используется.');
                    $customValid = false;
                }

                if ($customValid) {
                    // Токен для активации
                    $token = md5($login.$pass.$email.Zend_Registry::get('config')->session->salt);

                    // Новый пользователь
                    $user = $mUsers->createRow();
                    $user->login = $login;
                    $user->email = $email;
                    $user->password = CORE_User::hashPassword($pass, true);
                    $user->id_role = CORE_User::ROLE_PERSON;
                    $user->group = CORE_User::GROUP_PERSON;
                    $user->status = CORE_User::STATUS_INACTIVE;
                    $user->token = $token;
                    $user->save();
                    $this->view->done = true;

                    // Письмо для активации
                    $this->view->site = Zend_Registry::get('config')->site->name;

                    $this->view->url = Zend_Registry::get('config')->site->url
                                    . Zend_Controller_Front::getInstance()->getRouter()->assemble( array('token'=>$token), 'activation');
                    $emailTemplate =$this->view->render('auth/mailTemplate.phtml');

                    mail(
                            $email,
                            '=?UTF-8?B?'.base64_encode('Активация аккаунта на ' . Zend_Registry::get('config')->site->name).'?=',
                            $emailTemplate,
                            'MIME-Version: 1.0' . "\r\n" .
                            "Content-Type: text/html; charset=UTF-8\r\n"
                    );
                }
            }
        }

        $this->view->form = $form;
    }

    public function activationAction()
    {
        $token = $this->getRequest()->getParam('token');
        $mUsers = new Model_Users();
        $this->view->status = $mUsers->activateToken($token);

    }

    public function setsessionlifetimeAction()
    {
        $this->noLayout();
        if( $this->getRequest()->isPost()) {
            $value = (int)$this->getRequest()->getParam('value',1);
            if( $value > 90 ) {
                $value = 90;
            }

            $session = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);
            if( $session->user ) {
                Zend_Session::rememberMe( $value * 86400 );
                echo $this->setJSON(array());
            }
            else {
                echo $this->setJSON(array(),'forbidden');
            }
        }
    }

    public function logoutAction()
    {

        $auth_session = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);
        // Может быть нужно вернуть пользователя в прошлую жизнь

/*
        if( isset( $auth_session->restore ) ) {
            $previuos_user = $auth_session->restore->user;
            $redirect = $auth_session->restore->link;
            unset($auth_session->restore);
            $auth_session->user = $previuos_user;
            $this->_redirect( $redirect );
            exit;
        }
*/
        Zend_Session::expireSessionCookie();


        $user = Model_Users::getGuest();
        $auth_session->user = $user;
        Zend_Registry::set('user', $user);
        $this->redirect('/');
    }
}