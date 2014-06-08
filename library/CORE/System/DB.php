<?php
/**
 * User: Timur
 * Date: 21.03.12
 * Time: 15:51
 *
 */

class CORE_System_DB extends Zend_Db_Adapter_Pdo_Mysql{

    public function getLastQuery(){
        return  $this->getProfiler()->getLastQueryProfile()->getQuery();
    }

}