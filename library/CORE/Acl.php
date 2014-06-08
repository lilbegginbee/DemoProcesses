<?php

class CORE_Acl
{
    /**
     * Проверка на доступ пользователя к определенному ресурсу.
     * Если ресурса не существует, то доступ заблокирован.
     * @param mixed $resource наименование ресурса
     * @param string $privilege если нужно, то и ID привилегии
     * @param null $user если нужно то и пользователь, если не указан - текущий.
     * @param bool $forward делать форвард, либо не делать форвард
     * @return bool
     */
    static public function isAllowed($resource, $privilege = null, $user = null, $forward = false)
    {
        // @todo Добавить использования привилегий
        if (is_null($resource)) {
            return false;
        }

        // можно вызывать с параметром resource как строка, так и как id_resource
        if (is_numeric($resource)) {
            $mResources = new Model_AclResource();
            $resource = $mResources->getById($resource);
            if ($resource) {
                $resource = $resource['resource'];
            }
            else {
                return false;
            }
        }

        $mResources = new Model_AclRolesResources();
        $allow = $mResources->hasResource(CORE_User::getRole(), $resource);

        if ($allow === false && $forward === true) {
            Zend_Registry::get('Front')->redirect('/error/access');
        }

        return $allow;
    }
}