<?php
class Model_AclGroupsRoles extends Zend_Db_Table
{
    protected $_name = 'acl_groups_roles';

    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    public function getAllByGroup()
    {
        $res = $this->getAdapter()->select()
                            ->from( array('g' => $this->_name), array('id_group'))
                            ->join( array('r' => 'acl_roles'), 'r.id_role = g.id_role', array('id_role', 'role' => 'title') )
                            ->query()
                            ->fetchAll();
        $groups = array();
        foreach( $res as $row ) {
            if( !isset($groups[ $row['id_group'] ]) ) {
                $groups[ $row['id_group'] ] = new ArrayObject();
            }
            $groups[ $row['id_group'] ][] = $row;
        }

        return $groups;
    }

    public function getAllByGroupName( $group_name )
    {
        $res = $this->getAdapter()->select()
            ->from( array('g' => $this->_name), array('id_group'))
            ->join( array('r' => 'acl_roles'), 'r.id_role = g.id_role', array('id_role', 'role' => 'title') )
            ->where('g.id_group = ?',$group_name)
            ->where('r.status = ?', self::STATUS_ACTIVE )
            ->query()
            ->fetchAll();

        return $res;
    }

    public function getGroupIdByRoleId( $id_role )
    {
        $res = $this->select()->where('id_role = ?',$id_role)->query()->fetch();
        if( $res ) {
            return $res['id_group'];
        }

        return null;
    }

}