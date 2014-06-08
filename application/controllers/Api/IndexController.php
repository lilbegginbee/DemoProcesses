<?php
/**
 * Простое АПИ.
 */

class Api_IndexController extends Zend_Controller_Action
{
    protected $_map = array(
        'id_process' => null,
        'title' => null,
        'duration' => null,
        'id_owner' => null,
        'start_time' => null,
        'end_time' => null
    );

    const RESPONSE_STATUS_OK = 'OK';
    const RESPONSE_STATUS_FAIL = 'FAIL';

    public function init()
    {
        // авторизация
        if (CORE_User::getGroup() == CORE_User::GROUP_GUEST) {
            $this->throwError('Требуется авторизация');
        }

        $this->_helper->layout->setLayout('empty');

        // @todo валидация всех параметров
        foreach ($this->_map as $paramKey => $paramValue) {
            if ($this->getRequest()->getParam($paramKey,null)) {
                $this->_map[$paramKey] = $this->getRequest()->getParam($paramKey);
            }
        }
    }

    public function errorAction()
    {

    }

    /**
     * Полный список всех процессов,
     * для админа - совсем полный,
     * для пользователя - только его процессы.
     */
    public function listAction()
    {
        $idOwner = null;
        if (CORE_User::getGroup() != CORE_User::GROUP_ADMIN) {
            $idOwner = CORE_User::getId();
        }

        $mProcess = new Model_Processes();
        $items = $mProcess->getAll($idOwner);

        $this->throwJSON(
            self::RESPONSE_STATUS_OK,
            array(
                'items' => $items
            )
        );
    }

    public function addAction()
    {
        $title = $this->_map['title'];
        $duration = $this->_map['duration'];

        if (empty($title)) {
            $this->throwError('Название процесса не может быть пустым');
        }

        $mProcess = new Model_Processes();
        $idProcess = $mProcess->add(CORE_User::getId(), $title, $duration);

        if ($idProcess) {
            $this->throwJSON(
                self::RESPONSE_STATUS_OK,
                array(
                    'message' => 'Процесс добавлен',
                    'id_process' => $idProcess
                )
            );
        } else {
            $this->throwJSON(
                self::RESPONSE_STATUS_FAIL,
                array(
                    'message' => 'Процесс не добавлен'
                )
            );
        }

    }

    public function removeAction()
    {
        $idProcess = $this->_map['id_process'];
        $idOwner = CORE_User::getId();

        $mProcess = new Model_Processes();
        if ($mProcess->remove($idProcess, $idOwner)) {
            $this->throwJSON(
                self::RESPONSE_STATUS_OK,
                array(
                    'message' => 'Процесс #' . $idProcess . ' удалён.'
                )
            );
        } else {
            $this->throwJSON(
                self::RESPONSE_STATUS_FAIL,
                array(
                    'message' => 'Процесс #' . $idProcess . ' не удалён.'
                )
            );
        }


    }

    /**
     *
     */
    public function startAction()
    {
        $idProcess = $this->_map['id_process'];
        $idOwner = CORE_User::getId();

        $mProcess = new Model_Processes();
        if ($mProcess->start($idProcess, $idOwner)) {
            $this->throwJSON(
                self::RESPONSE_STATUS_OK,
                array(
                    'message' => 'Процесс стартовал.'
                )
            );
        } else {
            $this->throwJSON(
                self::RESPONSE_STATUS_FAIL,
                array(
                    'message' => 'Процесс не запустился.'
                )
            );
        }
    }

    public function stopAction()
    {
        $idProcess = $this->_map['id_process'];
        $idOwner = CORE_User::getId();

        $mProcess = new Model_Processes();
        if ($mProcess->stop($idProcess, $idOwner)) {
            $this->throwJSON(
                self::RESPONSE_STATUS_OK,
                array(
                    'message' => 'Процесс остановлен.'
                )
            );
        } else {
            $this->throwJSON(
                self::RESPONSE_STATUS_FAIL,
                array(
                    'message' => 'Процесс не остановится.'
                )
            );
        }
    }

    public function resetAction()
    {
        $idProcess = $this->_map['id_process'];
        $idOwner = CORE_User::getId();

        $mProcess = new Model_Processes();
        if ($mProcess->reset($idProcess, $idOwner)) {
            $this->throwJSON(
                self::RESPONSE_STATUS_OK,
                array(
                    'message' => 'Процесс сброшен.'
                )
            );
        } else {
            $this->throwJSON(
                self::RESPONSE_STATUS_FAIL,
                array(
                    'message' => 'Процесс не может быть сброшен.'
                )
            );
        }
    }

    protected function throwJSON($status, $data)
    {
        $Response = new CORE_API_JSON_Response();
        $Response->status = $status;
        foreach ($data as $name => $value) {
            $Response->$name = $value;
        }
        $Response->throwResponse();
    }

    protected function throwError($message)
    {
        $Response = new CORE_API_JSON_Response();
        $Response->status = CORE_API_JSON_Response::STATUS_FAIL;
        $Response->message = $message;
        $Response->throwResponse();
    }

}
