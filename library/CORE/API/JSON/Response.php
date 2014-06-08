<?php
class CORE_API_JSON_Response {

    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';

    protected $_status = null;
    protected $_data = array();

    public function __set($name, $value)
    {
        switch ($name) {
            case 'status':
                $this->_status = $value;
                break;
            default:
                $this->_data[$name] = $value;
        }

    }

    public function throwResponse()
    {
        $response = array(
            'status' => $this->_status,
            'data' => $this->_data
        );

        echo json_encode($response);
        exit;
    }
}