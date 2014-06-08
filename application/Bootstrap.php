<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAppAutoload()
    {
        Zend_Registry::get('db')->query('SET NAMES utf8');

        new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'   => APPLICATION_PATH
        ));

        Zend_Session::setOptions(array(
            'cookie_domain' => Zend_Registry::get('config')->site->cookie_host
        ));

        Zend_Session::start();

        $this->auth();

        $this->setRoutes();

    }

    private function auth()
    {
        $user = CORE_User::getInstance();
        Zend_Registry::set('user', $user);
    }

    private function setRoutes()
    {
        $frontController = Zend_Controller_Front::getInstance();

        /**
         * Common area
         */
        $index = new Zend_Controller_Router_Route(
            '/',
            array(
                'controller'    =>  'index',
                'action'        =>  'index'
            )
        );

        $auth = new Zend_Controller_Router_Route(
            '/auth',
            array(
                'controller' => 'auth',
                'action'     => 'auth'
            )
        );

        $activation = new Zend_Controller_Router_Route_Regex(
            '^activate\/([a-zA-Z0-9]{32})$',
            array(
                'controller' => 'auth',
                'action'     => 'activation'
            ),
            array(
                1 => 'token'
            ),
            'activate/%s'
        );


        $logout = new Zend_Controller_Router_Route(
            '/logout',
            array(
                'controller' => 'auth',
                'action'     => 'logout'
            )
        );

        $registration = new Zend_Controller_Router_Route(
            '/registration',
            array(
                'controller' => 'auth',
                'action'     => 'registration'
            )
        );

        /**
         * Users area
         */

        $processes = new Zend_Controller_Router_Route(
            '/processes',
            array(
                'controller'    =>  'User_Index',
                'action'        =>  'index'
            )
        );

        $processesAdd = new Zend_Controller_Router_Route(
            '/processes/add',
            array(
                'controller'    =>  'User_Index',
                'action'        =>  'add'
            )
        );

        /**
         * Admin area
         */
        $adminDashboard = new Zend_Controller_Router_Route(
            '/admin',
            array(
                'controller'    =>  'Admin_Index',
                'action'        =>  'index'
            )
        );

        $frontController->getRouter()
            ->addRoute('index', $index)
            ->addRoute('auth', $auth)
            ->addRoute('activation', $activation)
            ->addRoute('logout', $logout)
            ->addRoute('registration', $registration)

            ->addRoute('processes',$processes)
            ->addRoute('processesAdd', $processesAdd)

            ->addRoute('adminDashboard', $adminDashboard);

        $this->setAPI(Zend_Registry::get('config')->api->version);
    }

    private function setAPI($version)
    {
        $frontController = Zend_Controller_Front::getInstance();

        $add = new Zend_Controller_Router_Route(
            '/api/'.$version.'/add',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'add'
            )
        );

        $list = new Zend_Controller_Router_Route(
            '/api/'.$version.'/list',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'list'
            )
        );

        $start = new Zend_Controller_Router_Route(
            '/api/'.$version.'/start',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'start'
            )
        );

        $stop = new Zend_Controller_Router_Route(
            '/api/'.$version.'/stop',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'stop'
            )
        );

        $reset = new Zend_Controller_Router_Route(
            '/api/'.$version.'/reset',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'reset'
            )
        );

        $remove = new Zend_Controller_Router_Route(
            '/api/'.$version.'/remove',
            array(
                'controller'    =>  'Api_Index',
                'action'        =>  'remove'
            )
        );

        $frontController->getRouter()
            ->addRoute('api_add', $add)
            ->addROute('api_list', $list)
            ->addRoute('api_start', $start)
            ->addRoute('api_stop', $stop)
            ->addRoute('api_reset', $reset)
            ->addRoute('api_remove', $remove);
    }

    private function wwwRule()
    {
        if (!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == Zend_Registry::get('config')->site->host ) {
            $url = Zend_Registry::get('config')->site->protocol->url . 'www.'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header ('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $url);
        }
    }

}