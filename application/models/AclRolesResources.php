<?php
class Model_AclRolesResources extends Zend_Db_Table
{
    protected $_name = 'acl_roles_resources';

    public function getResources( $id_role, $id_group = null )
    {
        if( is_null($id_group) ) {
            $mGroupRoles = new Model_AclGroupsRoles();
            $id_group = $mGroupRoles->getGroupIdByRoleId( $id_role );
        }

        $mResources = new Model_AclResource();
        $result = $mResources->getAdapter()->select()
                    ->from(array('r' => 'acl_resources'), array('resource','description'))
                    ->joinLeft(array( 'rr' => $this->_name ), 'rr.resource = r.resource AND rr.id_role = ' . $id_role,array(new Zend_Db_Expr('IF(rr.id_role IS NULL,0,1) status')))
                    ->where('id_group = ?', $id_group)
                    ->query()
                    ->fetchAll();
        return $result;
    }

    public function hasResource($idRole, $resource)
    {
        $resources = $this->getResourcesFromKey($resource);

        $select = $this->getDefaultAdapter()->select()
            ->from(
                array('rr' => $this->_name)
            )
            ->join(
                array('r' => 'acl_resources'),
                'rr.resource = r.id_resource',
                array()
            )
            ->where('r.resource IN (?)', $resources)
            ->where('rr.id_role = ?', $idRole);

        $result = $this->getDefaultAdapter()->fetchRow($select);

        if ($result === false) {
            return false;
        } elseif (is_array($result)) {
            return true;
        }
    }

    private function getResourcesFromKey($resourceKey)
    {
        if (preg_match('/^([a-zA-Z0-9\_\-]+)\:\:([a-zA-Z0-9\_\-]+)$/ui', $resourceKey, $matches)) {
            return array($matches[1], $resourceKey);
        } else {
            return array($resourceKey);
        }
    }
}