<?php
class Model_AclGroupsRolesExtra extends Zend_Db_Table
{
    protected $_name = 'acl_groups_roles_extra';

    public function isRoleInGroup( $id_role, $group )
    {
        $res = $this->select()
                    ->where('id_role = ?', $id_role)
                    ->where('id_group = ?', $group)
                    ->query()
                    ->fetch();
        if( $res ) {
            return true;
        }
        return false;
    }
}