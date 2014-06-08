<?php
class Model_AclResource extends Zend_Db_Table
{
    protected $_name = 'acl_resources';

    public function getByGroup( $id_group )
    {
        $resources = $this->select()->where('id_group = ?',  $id_group)->query()->fetchAll();
        return $resources;
    }

    public function getById( $id_resource)
    {
        $select = $this->select()->where('id_resource = ?', $id_resource);
        $resource = $select->query()->fetch();
        return $resource;
    }

    public function getByName( $resource, $id_group = null )
    {
        $select = $this->select()->where('resource = ?', $resource);
        if( !is_null($id_group) ) {
            $select->where('id_group = ?', $id_group);
        }
        $resource = $select->query()->fetch();
        return $resource;
    }

}