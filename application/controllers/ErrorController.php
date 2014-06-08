<?php

require_once 'Common/ParentController.php';

class ErrorController extends ParentController
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $router = Zend_Controller_Front::getInstance()->getRouter();
                if ($router->hasRoute('home')) {
                    $this->redirect($router->assemble(array(), 'home'));
                } else {
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->_helper->viewRenderer('error/notfound', null, true);
                }
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;

    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    
	public function notfoundAction()
    {

    }

    public function servicesaboutAction()
    {

    }

    public function accessAction()
    {

    }

    public function runtimeAction()
    {

    }
}