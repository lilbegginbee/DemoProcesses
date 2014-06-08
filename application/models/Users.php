<?php

class Model_Users extends Model_Core
{
    const ACCESS_LEVEL_PRIVATE = 'private';
    const ACCESS_LEVEL_PUBLIC = 'public';

    const USER_STATUS_ACTIVE = 'active';
    const USER_STATUS_BLOCK = 'block';
    const USER_STATUS_DELETE = 'delete';

    protected $_name = 'users';

    public function activateToken($token)
    {
        $row = $this->select()
            ->where('status = ?', CORE_User::STATUS_INACTIVE)
            ->where('token = ?', $token)
            ->query()
            ->fetch();
        if($row) {
            $this->update(array('status' => CORE_User::STATUS_ACTIVE), array('id_user' => $row['id_user']));
            return true;
        } else {
            return false;
        }

    }

    public function remove( $id_user )
    {
        //@todo Как-то правильно проверить права на удаление пользователя $id_user

        if( CORE_User::getId() == (int)$id_user ) {
            return false;
        }

        $user = $this->getByID( $id_user );
        if( Core_User::ROLE_ADMIN != Core_User::getRole()
                && in_array( $user->id_role, array( Core_User::ROLE_ADMIN, CORE_User::ROLE_SUB_ADMIN) ) ) {
            return false;
        }

        Model_UsersLog::addActivity( CORE_User::getId(), Model_UsersLog::TYPE_DELETE, 'Удалил пользователя #' . $id_user );

        $this->delete( 'id_user = ' . (int)$id_user );
    }

    public function search( $page = 1, $limit = 10, $paramsLike = null, $sortparam = null, $sortdir = null, $searchparams = null )
    {
        $_translation = array(
            'organisation' => 'o.title_short',
            'role' => 'r.title'
        );

        $select = $this->getAdapter()->select();

        $select->where( 'u.id_role != ?', CORE_User::ROLE_PERSON );
        $select->where( 'u.status != ?', CORE_User::STATUS_DELETE );
        $select->from( array( 'u' => $this->_name ),
                        array(
                                'id_user',
                                'name',
                                'login',
                                'id_role' => 'id_role',
                                'id_organisation',
                                 'last_login' => new Zend_Db_Expr('CASE last_login WHEN "0000-00-00 00:00:00" THEN "" ELSE last_login END')
                            )
                      );
        $select->join( array( 'r' => 'acl_roles' ), 'r.id_role = u.id_role', array('role' => 'title') );
        $select->joinLeft( array( 'o' => 'organisations'),'o.id_organisation = u.id_organisation', array( 'organisation' => 'title_short') );
        $select->limit( $limit, ($page-1) * $limit );

        if( !is_null( $paramsLike ) && is_array( $paramsLike ) && count( $paramsLike ) ) {
            foreach( $paramsLike as $param) {
                $select->where( $param['name'] . ' LIKE ?', '%' . $param['like'] .'%' );
            }
        }

        $valid_params = array('id_user','name','login','id_role','id_organisation','organisation','last_login');

        if( is_null($sortparam) ) {
            $select->order('id_user ASC');
        }
        elseif( in_array( $sortparam, $valid_params) ) {
            $select->order( $sortparam . ' ' . $sortdir );
        }

        // search
        if( !is_null($searchparams) ) {
            foreach( $searchparams as $name => $value ) {
                // @todo param value check
                if( isset($_translation[$name]) ) {
                    $name = $_translation[$name];
                }
                $select->where(  $name . ' LIKE ?', '%' . $value . '%');
            }
        }

        try {
            $res = $select->query()->fetchAll();
            $select->columns(new Zend_Db_Expr('Count(*)') . ' as qty');
            $select->reset( Zend_Db_Select::LIMIT_COUNT );
            $select->reset( Zend_Db_Select::LIMIT_OFFSET );
            $res2 = $select->query()->fetchObject();
            $total = $res2->qty;
            $select->reset();
        }
        catch( Exception $e ) {
            $res = array();
            $total = 0;
        }

        return array( 'total' => $total, 'data' => $res );
    }

    /**
     * @return Zend_Db_Select
     */
    public function getUsers()
    {
        $_translation = array(
            'organisation' => 'o.title_short',
            'role' => 'r.title'
        );

        $select = $this->getAdapter()->select();

        $select->where( 'u.id_role != ?', CORE_User::ROLE_PERSON );
        $select->where( 'u.status != ?', CORE_User::STATUS_DELETE );
        $select->from( array( 'u' => $this->_name ),
            array(
                'id_user',
                'name',
                'login',
                'r.caption' => 'r.caption',
                'id_organisation',
                'last_login' => new Zend_Db_Expr('CASE last_login WHEN "0000-00-00 00:00:00" THEN "" ELSE last_login END')
            )
        );
        $select->join( array( 'r' => 'acl_roles' ), 'r.id_role = u.id_role', array('role' => 'title') );
        $select->joinLeft( array( 'o' => 'organisations'),'o.id_organisation = u.id_organisation', array( 'organisation' => 'title_short') );

        return $select;
    }


    /**
     * @param $id_user
     * @return object
     */
    public function getByID( $id_user )
    {
        $res = $this->getDefaultAdapter()
                ->select()
                ->from( array('u' => $this->_name),
                        array(
                            'id_user',
                            'name',
                            'login',
                            'password',
                            'password_changed_date',
                            'id_role',
                            'post',
                            'group',
                            'id_organisation',
                            'last_login',
                            'status',
                            'created',
                            'email',
                            'phone'
                        )
                    )
                ->join( array('ar' => 'acl_roles'),
                        'ar.id_role = u.id_role',
                        array(
                            'role'=>'caption'
                        )
                    )
                ->where( 'u.id_user = ?', $id_user )
                  ->query()
                    ->fetchObject();
        return $res;
    }

    /**
     * @param $login
     * @return mixed
     */
    public function getByLogin( $login )
    {
        $res = $this->getDefaultAdapter()
            ->select()
            ->from( array('u' => $this->_name),
                array(
                    'id_user',
                    'name',
                    'login',
                    'password',
                    'password_changed_date',
                    'id_role',
                    'post',
                    'group',
                    'id_organisation',
                    'last_login',
                    'status',
                    'created',
                    'email',
                    'phone'
                )
            )
            ->join( array('ar' => 'acl_roles'),
                'ar.id_role = u.id_role',
                array(
                    'role'=>'caption'
                )
            )
            ->where( 'login = ?', $login )
            ->query()
            ->fetchObject();
        return $res;
    }

    public function getByIDs( $id_users )
    {
        $id_users  = join(',', $id_users);
        $res = $this->select()->where( 'id_user IN (?)', $id_users )
            ->query()
            ->fetchAll();
        return $res;
    }


    public function checkPass( $id_user, $pass )
    {
        $res = $this->select()->where( 'id_user = ?', $id_user )
                    ->query()
                    ->fetchObject();
        if( $res->password == CORE_User::hashPassword( $pass, false )
            || $res->password == CORE_User::hashPassword( $pass, true ) ) {
            return true;
        }
        return false;
    }

    /**
     * Метод для авторизации пользователей. В случае успеха возвращает объект пользователя.
     * В случае неудачи возвращает false.
     * @ver 1.1.3
     * @param $login
     * @param $password
     * @param null $group
     * @param bool $alreadyHashed
     * @return bool|Zend_Db_Table_Row
     */
    function auth($login, $password, $group = null, $alreadyHashed = false)
    {
        $select = $this->getDefaultAdapter()->select()
            ->from(
                array('u' => $this->_name),
                array(
                    'id_user',
                    'name',
                    'login',
                    'group',
                    'post',
                    'id_role',
                    'id_organisation',
                    'email',
                    'phone'
                )
            )
            ->join(
                array('ar' => 'acl_roles'),
                'ar.id_role = u.id_role',
                array(
                    'role' => 'caption'
                )
            )
            ->where('u.login = ?', $login)
            ->where('u.status = ?', CORE_User::STATUS_ACTIVE);

        if (!$alreadyHashed) {
            $select->where('u.password = ?', CORE_User::hashPassword($password, true));
        } else {
            $select->where('u.password = ?', $password);
        }

        $row = $this->getDefaultAdapter()->fetchRow($select);

        return is_array($row) && count($row) ? $row : false;
    }

    /**
     * Получение объекта сессии для роли Гость
     * @ver 1.1.3
     * @return stdClass
     */
    static public function getGuest()
    {
        $guest = new stdClass();

        $guest->id_user = null;
        $guest->name    = null;
        $guest->login   = null;
        $guest->group   = CORE_User::GROUP_GUEST;
        $guest->post    = null;
        $guest->id_role = CORE_User::ROLE_GUEST;
        $guest->id_organisation = null;
        $guest->email   = null;
        $guest->phone   = null;

        return $guest;
    }

    public function getUserRegion( $id_user )
    {
        return $this->getAdapter()
                ->select()
                ->from( array( 'u' => 'users'), array('id_user') )
                ->join( array('o' => 'organisations'), 'o.id_organisation = u.id_organisation', array( 'id_region' ) )
                ->where('u.id_user = ?', $id_user)
                ->query()
                ->fetchObject();

    }

    public function getOrganisationPersonalList($page = 1, $limit = 10, $id_organisation = null )
    {
        if( is_null($id_organisation) ) {
            $id_organisation = CORE_User::getOrganisationID();
        }

        $users = array();
        if (is_numeric($id_organisation)) {
            $select = $this->getAdapter()->select()
                                        ->from(
                                            array('u' => $this->_name)
                                        )
                                        ->join(
                                            array('ur' => 'acl_roles'),
                                            'u.id_role = ur.id_role',
                                            array('role_title' => 'title')
                                        )
                                        ->where('id_organisation = ?', $id_organisation)
                                        ->where('`group` = ?', CORE_User::GROUP_SCHOOL)
                                        ->where('u.status != ?', Model_Users::USER_STATUS_DELETE)
                                        ->limitPage($page, $limit)
                                        ->order('id_role ASC');

            $users = $this->getAdapter()->fetchAll($select);

            if (!is_array($users) || count($users) == 0) {
                $users = array();
            }
        }

        return $users;
    }

    public static function getReporterGeoSettings($id_user)
    {
        $db = Zend_Registry::get('db');

        if (CORE_User::getGroup() == 'school') {
            $settings = array(
                'id_user' => CORE_User::getId(),
                'geo_type' => 'school',
                'geo_id' => CORE_User::getOrganisationID()
            );
        }
        else {
            $settings = $db->fetchRow($db->select()->from('reports_users_info')->where('id_user = ?', $id_user));
        }

        return $settings;
    }

    /**
     * @todo что за функция?
     * @param $person
     * @param $device
     * @param null $id_organization
     * @return array|bool
     */
    public function apiGetUserID($person, $device, $id_organization = null)
    {
        $return = array(
            'id_user' => null,
            'class_number' => null,
            'class_letter' => null
        );


        /*if( is_null( $id_organization )) {
            $id_organization = CORE_User::getOrganisationID();
        }*/

        $class = trim($person[ Model_Sessions::XML_NODENAME_PERSON_GROUP ]);
        /* hack */
        $class = trim( preg_replace('|класс|isU','',$class) );
        if ( preg_match('/(\d{1,2})\s*(.{0,10})\s?/ui', $class, $matches) ) {
            $class_number = $matches[1];
            $class_letter = $matches[2];
        } else {
            $class_number = null;
            $class_letter = $class;
        }

        $return['class_number'] = $class_number;
        $return['class_letter'] = $class_letter;


        /*if (preg_match('/^\d{1,2}\.\d{4}$/ui', $person[ Model_Sessions::XML_NODENAME_PERSON_BIRTHDAY ])) {
            if (!$birthday = date('Y-m-d', strtotime(rand(1, 30) . '.' . $person[ Model_Sessions::XML_NODENAME_PERSON_BIRTHDAY ]))) {
                $birthday = null;
            }
        } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/ui', $person[ Model_Sessions::XML_NODENAME_PERSON_BIRTHDAY ])) {
            if (!$birthday = date('Y-m-d', strtotime($person[ Model_Sessions::XML_NODENAME_PERSON_BIRTHDAY ]))) {
                $birthday = null;
            }
        } else {
            $birthday = null;
        }

        if ($person[ Model_Sessions::XML_NODENAME_PERSON_G ] == 'm') {
            $gender = 'm';
        } else {
            $gender = 'f';
        }*/

        return $return;

        /**
         * @todo Здесь создаётся новый пользователь для обследуемого
         */
        /*if (empty($person[ Model_Sessions::XML_NODENAME_PERSON_ID ])) {
            $user = $this->createRow();

            $fio = array();
            if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_LASTNAME ])) {
                array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_LASTNAME ]);
            }
            if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_NAME ])) {
                array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_NAME ]);
            }
            if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_MIDDLENAME ])) {
                array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_MIDDLENAME ]);
            }



            if (count($fio) > 0) {
                $user->name = implode(' ', $fio);
            }

            $user->password = 'pwduser';
            $user->id_role = CORE_User::ROLE_PERSON;
            $user->post = 'Ученик';
            $user->group = CORE_User::GROUP_PERSON;

            $user->id_organisation = $id_organization;
            $user->last_login = '2000-01-01 00:00:00';

            try {
                if ($id_user = $user->save()) {
                    //$this->update(array('login' => $id_user, 'password' => md5("pwd{$id_user}")), 'id_user = ' . $id_user);

                    //$this->getAdapter()->insert('school_students_info', array('id_user' => $id_user, 'birthday' => $birthday, 'gender' => $gender));

                    $class = $this->getAdapter()->fetchRow(
                                    $this->getAdapter()->select()
                                                        ->from('school_classes')
                                                        ->where('id_school = ?', $id_organization )
                                                        ->where('number = ?', $class_number)
                                                        ->where('letter = ?', $class_letter)
                                    );

                    if (!empty($class)) {
                        $this->getAdapter()->insert('school_classes_students', array('id_user' => $id_user, 'id_class' => $class['id_class']));
                    } else {
                        $this->getAdapter()->insert('school_classes', array('id_school' => $id_organization, 'number' => $class_number, 'letter' => $class_letter));
                        if ($id_class = $this->getAdapter()->lastInsertId()) {
                            $this->getAdapter()->insert('school_classes_students', array('id_user' => $id_user, 'id_class' => $id_class));
                        } else {
                            return false;
                        }
                    }


                    $return['id_user'] = $id_user;
                    return $return;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        } else {
            $user = $this->fetchRow($this->select()->where('id_user = ?', $person[ Model_Sessions::XML_NODENAME_PERSON_ID ]));
            if (!$user instanceof Zend_Db_Table_Row) {
                $user = $this->createRow();

                $fio = array();
                if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_LASTNAME ])) {
                    array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_LASTNAME ]);
                }
                if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_NAME ])) {
                    array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_NAME ]);
                }
                if (!empty($person[ Model_Sessions::XML_NODENAME_PERSON_MIDDLENAME ])) {
                    array_push($fio, $person[ Model_Sessions::XML_NODENAME_PERSON_MIDDLENAME ]);
                }

                $user->name = implode(' ', $fio);
                $user->login = uniqid();
                $user->password = 'pwduser';
                $user->id_role = CORE_User::ROLE_PERSON;
                $user->post = 'Ученик';
                $user->group = CORE_User::GROUP_PERSON;
                $user->id_organisation = $id_organization;
                $user->last_login = '2000-01-01 00:00:00';

                try {
                    if ($id_user = $user->save()) {
                        $this->update(array('login' => $id_user, 'password' => "pwd{$id_user}"), 'id_user = ' . $id_user);

                        //$this->getAdapter()->insert('school_students_info', array('id_user' => $id_user, 'birthday' => $birthday, 'gender' => $gender));

                        $class = $this->getAdapter()->fetchRow(
                            $this->getAdapter()->select()
                                ->from('school_classes')
                                ->where('id_school = ?', $device->id_organisation)
                                ->where('number = ?', $class_number)
                                ->where('letter = ?', $class_letter)
                        );

                        if (!empty($class)) {
                            $this->getAdapter()->insert('school_classes_students', array('id_user' => $id_user, 'id_class' => $class['id_class']));
                        } else {
                            $this->getAdapter()->insert('school_classes', array('id_school' => $device->id_organisation, 'number' => $class_number, 'letter' => $class_letter));
                            if ($id_class = $this->getAdapter()->lastInsertId()) {
                                $this->getAdapter()->insert('school_classes_students', array('id_user' => $id_user, 'id_class' => $id_class));
                            } else {
                                return false;
                            }
                        }

                        $return['id_user'] = $id_user;
                        return $return;
                    } else {
                        return false;
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                    return false;
                }
            } else {
                $return['id_user'] = $user->id_user;
                return $return;
            }
        }*/
    }

    public static function getSettings($id_user)
    {
        if (!$id_user) {
            throw new Zend_Exception('Невозможно получить настройки пользователя.');
        }

        $db = Zend_Registry::get('db');

        switch (CORE_User::getGroup()) {
            case CORE_User::GROUP_ANALYTIC:
                $settings = $db->fetchRow($db->select()->from('reports_users_info')->where('id_user = ?', $id_user));
                break;
            case CORE_User::GROUP_SCHOOL:
                $settings = array(
                    'id_user' => CORE_User::getId(),
                    'geo_type' => 'school',
                    'geo_id' => CORE_User::getOrganisationID()
                );
                break;
            default:
                $settings = false;
        }

        return $settings;
    }

    public function loginOrEmailExists($login, $email)
    {
        $res = $this->select()
            ->where('login = ?', $login)
            ->orWhere('email = ?', $email)
            ->query()
            ->fetch();
        if( $res ) {
            return true;
        }
        return false;
    }

    public function existsByNameAndOrg( $name, $id_org )
    {
        $res = $this->select()->where('name = ?', $name)
            ->where('id_organisation = ?', $id_org)
            ->query()
            ->fetch();
        if( $res ) {
            return true;
        }
        return false;
    }

    /**
     * Определение возраста пациента по его дате рождения
     * @static
     * @param $birthday
     * @return int|null
     */
    public static function getAge( $birthday )
    {
        try {
            $datetime1 = new DateTime( $birthday );
            $datetime2 = new DateTime('now');
            $interval = $datetime1->diff($datetime2);
            $age = (int)$interval->format('%Y%');
        } catch (Exception $e) {
            $age = null;
        }

        return $age;
    }

    /**
     * @param $data
     * @return Zend_Db_Table_Row_Abstract
     */
    public function addNurse( $data )
    {
        $row = $this->createRow();
        $row->name = $data['name'];
        $row->id_role = CORE_User::ROLE_SCHOOL_NURSE;
        $row->group = CORE_User::GROUP_SCHOOL;
        $row->status = self::USER_STATUS_ACTIVE;
        $row->id_organisation = $data['id_organisation'];
        $row->password = $data['password'];
        $id = $row->save();
        $row->login = $id;
        $row->save();
        return $row;
    }

    /**
     * Nurse info
     */
    public function infoNurse( $id_user )
    {
        $data = array();

        $mSessions = new Model_Sessions();
        $data['sessions_qty'] = $mSessions->calcSessionsByUser( $id_user );

        return $data;
    }

    /**
     * Psych info
     */
    public function infoPsych( $id_user )
    {
        $data = array();
        return $data;
    }

    /**
     * Director info
     */
    public function infoDirector( $id_user )
    {
        $data = array();
        return $data;
    }

    /**
     * Default info
     */
    public function infoDefault( $id_user )
    {
        $data = array();
        return $data;
    }

    public static function getSubdomainByGroup($group)
    {
        switch ($group) {
            case 'user':
                return 'person';
            case 'school':
                return 'school';
            case 'admin':
                return 'admin';
            case 'report':
                return 'report';
            case 'dealer':
                return 'dealer';
            case 'store':
                return 'store';
            case 'person':
                return 'person';
            case 'api':
                return 'api';
            case 'system';
                return '';
        }
    }

    /**
     * Получение информации о последней сессии пользователя по ID школы
     * @ver 1.1.3
     * @param $idSchool
     * @return array|bool
     */
    public function getLastSessionBySchoolId($idSchool)
    {
        $select = $this->getDefaultAdapter()->select()
            ->from(
                array('u' => 'users'),
                array(
                    'name',
                    'login',
                    'last_login'
                )
            )
            ->where('id_organisation = ?', $idSchool)
            ->order('last_login DESC')
            ->limit(1);

        $session = $this->getDefaultAdapter()->fetchRow($select);
        if (is_array($session) && !is_null($session['last_login'])) {
            return $session;
        } else {
            return false;
        }
    }
}