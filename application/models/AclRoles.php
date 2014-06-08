<?php
class Model_AclRoles extends Model_Core
{
    protected $_name = 'acl_roles';

    public function getById( $id_role )
    {
        return $this->fetchRow('id_role = ' . $id_role);
    }

}