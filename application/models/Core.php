<?php

 class Model_Core extends Zend_Db_Table
 {
    /**
     * Список значений произвольного поля в таблице.
     * @param $field
     * @return array
     */
    public function getDistinctValues( $field, $translate = false )
    {
        $res = $this->select()->distinct()->from($this, array( $field ))
            ->query()->fetchAll();
        $values = array();
        foreach( $res as $row) {
            if ($translate) {
                $values[] = t($row[ $field ]);
            } else {
                $values[] = $row[ $field ];
            }
        }
        return $values;

    }
 }