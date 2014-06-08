<?php

class ParentController extends Zend_Controller_Action
{
    /**
     * Объект сообщений
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    public $fmessage = null;

	public function init()
	{
		parent::init();
        $this->view->headTitle()->setSeparator(' : ');
        $this->setTitle(Zend_Registry::get('config')->site->name);

        // Получаем объект сообщений
        $this->fmessage = $this->_helper->getHelper('FlashMessenger');

        Zend_Registry::set('View', $this->view);
        Zend_Registry::set('Front', $this);

        $this->view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'CORE_View_Helper');
        $this->view->addHelperPath(APPLICATION_PATH . '/views/helpers/tests', 'Tests_View_Helper');
        $this->view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Corvita_View_Helper');

        // Проверяем есть ли доступ у пользователя к запрашиваемому ресурсу
/*
        if (!CORE_Acl::isAllowed($this->getResourceKey())) {
            if (CORE_User::getRole() === CORE_User::ROLE_GUEST) {
                $this->redirect($this->_helper->url->url(array(), 'auth'));
            }
        }
*/
	}

    /**
     * Получение ключа запрашиваемого ресурса. Ключ формируется в виде строки
     * имя_контроллера::имя_действия. Например, auth::auth
     * @return string
     * @throws Exception
     */
    protected function getResourceKey()
    {
        $request = $this->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        if ($controllerName && $actionName) {
            $controllerName = mb_strtolower($controllerName, 'UTF-8');
            $actionName = mb_strtolower($actionName, 'UTF-8');

            return "{$controllerName}::{$actionName}";
        } else {
            throw new Exception('Undefined resource type');
        }
    }

	public function postDispatch()
	{
        parent::postDispatch();
        $this->view->headScript()->prependFile('/js/common.js', 'text/javascript');
		$this->view->headScript()->prependFile('/js/jquery-2.0.3.min.js', 'text/javascript', array('compress' => true));
		$this->view->headScript()->appendFile('/js/bootstrap/3.0.3/js/bootstrap.min.js', 'text/javascript', array('compress' => true));

		$this->view->headScript()->appendFile('/js/flatui-radio.js', 'text/javascript', array('compress' => true));
		$this->view->headScript()->appendFile('/js/flatui-checkbox.js', 'text/javascript', array('compress' => true));

        $this->view->headLink()->prependStylesheet('/css/bootstrap.min.css', 'screen', false, array('compress' => true));

		if ($this->fmessage->hasMessages()) {
            // устанавливаем сообщения, если они есть
            $this->view->fmessages = $this->fmessage->getMessages();
        } elseif ($this->fmessage->hasCurrentMessages()) {
            // устанавливаем сообщения, если они есть
            $this->view->fmessages = $this->fmessage->getCurrentMessages();
        }

        if (APPLICATION_ENV === 'production') {
            CORE_Cache_Compressor::compress($this->view->headLink());
            CORE_Cache_Compressor::compress($this->view->headScript());
        }
	}

    public function noLayout()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function emptyLayout()
    {
        $this->_helper->layout->setLayout('empty');
    }

    public function lightLayout()
    {
        $this->_helper->layout->setLayout('light');
    }

    public function printLayout()
    {
        $this->_helper->layout->setLayout('print');
    }

    public function setTitle($title)
    {
        $this->view->headTitle()->prepend($title);
    }

    public function useCKEditor()
    {
        $this->view->headScript()->appendFile('/js/ckeditor/ckeditor.js', 'text/javascript');
    }

    public function useDatepickerBootstrap()
    {
        $this->view->headScript()->appendFile('/js/bootstrap-datepicker.js', 'text/javascript', array('compress' => true));
        $this->view->headLink()->appendStylesheet('/css/datepicker.css', 'screen', false, array('compress' => true));
    }

    /**
     * Подключение к странице плагина Select2
     */
    public function useSelect2()
    {
        $this->view->headScript()->appendFile('/js/select2/select2.min.js', 'text/javascript', array('compress' => true));
        $this->view->headScript()->appendFile('/js/select2/select2_locale_ru.js', 'text/javascript', array('compress' => true));

        $this->view->headLink()->appendStylesheet('/js/select2/select2.css', 'screen', false, array('compress' => true));
        $this->view->headLink()->appendStylesheet('/js/select2/bootstrap.css', 'screen', false, array('compress' => true));
    }

    /**
     * Подключение к странице плагина Custom ScrollBar
     */
    public function useCustomScrollBar()
    {
        $this->view->headScript()->appendFile('/js/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js', 'text/javascript', array('compress' => true));
        $this->view->headLink()->appendStylesheet('/js/custom-scrollbar/jquery.mCustomScrollbar.css', 'screen', false, array('compress' => true));
    }

    /**
     * Подключение к странице плагина DataTables
     */
    public function useDataTables()
    {

		// Datatables 1.10-dev
		$this->view->headScript()->appendFile('/js/dataTables/1.10-dev/media/js/jquery.dataTables.js', 'text/javascript', array('compress' => true));

		$this->view->headScript()->appendFile('/js/2.0/dataTables.utils.js', 'text/javascript', array('compress' => true));

		/* Bootstrap 3 integration */
		$this->view->headScript()->appendFile('/js/dataTables/1.10-dev/examples/resources/bootstrap/3/dataTables.bootstrap.js', 'text/javascript', array('compress' => true));
		$this->view->headLink()->appendStylesheet('/js/dataTables/1.10-dev/examples/resources/bootstrap/3/dataTables.bootstrap.css', 'screen', false, array('compress' => true));

    }

    public function usePopovers()
    {
        $this->view->headScript()->appendFile('/js/jquery.jgrowl.min.js', 'text/javascript', array('compress' => true));
        $this->view->headLink()->appendStylesheet('/css/jquery.jgrowl.min.css', 'screen', false, array('compress' => true));
    }


    public function setMessage($message)
    {
        $this->fmessage->addMessage($message);
    }

    //////////////////////////////////////////////////////////////

    /**
     * @param $data
     * @param null $error
     * @param null $status
     * @return string
     */
    public function setJSON( $data, $error = null, $status = null )
    {
        if( is_null( $status ) ) {
           $status = is_null($error)?'OK':'ERROR';
        }

        $response = array(
            'status' => $status,
            'error' => is_null($error)?'':$error,
            'data' => $data
        );

        return json_encode( $response );
    }
}