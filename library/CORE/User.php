<?php

class CORE_User
{
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLOCK = 'block';
    const STATUS_DELETE = 'delete';

    // Посетители системы
    const GROUP_GUEST = 'guest';
    // API для АПК Армис
    const GROUP_API = 'api';
    // Администраторы системы - сотрудники Корвиты
    const GROUP_ADMIN = 'admin';
    // Аналитики системы - люди, просматривающие отчеты согласно их региональной привязке
    const GROUP_ANALYTIC = 'analytic';
    // Работники склада
    const GROUP_STORE = 'store';
    // Дилеры
    const GROUP_DEALER = 'dealer';
    // Школа
    const GROUP_SCHOOL = 'school';
    const GROUP_PERSON = 'person';
    // Психологическое тестирование
    const GROUP_TEST_SESSION = 'test';
    // Управление системой - очистка кеша, включение/выключение возможностей и т.п.
    const GROUP_SYSTEN = 'system';

    // Список ролей (Роль принадлежит одной группе, но пользователь может принадлежать к множеству ролей)
    // Роль посетителя сайта
    const ROLE_GUEST = 0;
    // Роль директора школы
    const ROLE_SCHOOL_DIRECTOR = 20;
    const ROLE_SCHOOL_PSYCH = 25;
    const ROLE_SCHOOL_FIZRUK = 26;
    // Роль медсестры школы
    const ROLE_SCHOOL_NURSE = 28;
    // Роль администратора системы
    const ROLE_ADMIN = 30;
    // Роль вспомогательного администратора системы, по идеи имеет чуть-чуть урезаные права,
    // например, не может удалять администраторов и менять им пароли.
    const ROLE_SUB_ADMIN = 32;
    // Роль управляющего системой
    const ROLE_SYSTEM = 100;
    // Роль аналитиков системы
    const ROLE_ANALYTIC = 40;
    // Роль работников склада
    const ROLE_STORE = 50;
    // Роль дилера
    const ROLE_DEALER = 60;
    // Роль обследуемый
    const ROLE_PERSON = 10;
    // Роль пользователя АПИ
    const ROLE_API = 110;

    const ROLE_VISITOR = 5;


    //@todo Нужно заполнить до конца
    protected static $_role_by_id = array(
        self::ROLE_GUEST => 'guest',
        self::ROLE_ADMIN => 'admin',
        self::ROLE_SCHOOL_DIRECTOR => 'school_director',
        self::ROLE_SUB_ADMIN => 'sub_admin',
        self::ROLE_SCHOOL_PSYCH => 'psych',
        self::ROLE_ANALYTIC => 'analytic'
    );

    //@todo Нужно заполнить до конца
    protected static $_group_by_role = array(
        self::ROLE_SCHOOL_DIRECTOR => self::GROUP_SCHOOL,
        self::ROLE_SCHOOL_NURSE => self::GROUP_SCHOOL,
        self::ROLE_SCHOOL_PSYCH => self::GROUP_SCHOOL,

        self::ROLE_ADMIN        => self::GROUP_ADMIN,
        self::ROLE_SUB_ADMIN    => self::GROUP_ADMIN,

        self::ROLE_ANALYTIC => self::GROUP_ANALYTIC,

        self::ROLE_STORE => self::GROUP_STORE
    );

    private static $_instance = null;

    private $_user = null;

    private function __construct()
    {
        $authSession = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);

        if (isset($authSession->user) && is_object($authSession->user)) {
            $this->_user = $authSession->user;
            if (isset($authSession->restore)) {
                $this->_user->loginas = true;
            } else {
                $this->_user->loginas = false;
            }
        } else {
            $this->_user = Model_Users::getGuest();
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new CORE_User();
        }

        return self::$_instance;
    }

    public static function setRememberMe($value)
    {
        $authSession = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);
        $authSession->rememberme = $value;
    }

    public static function getRememberMe()
    {
        $authSession = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);
        return $authSession->rememberme;
    }

    static public function getUser()
    {
        $user = self::getInstance();
        return $user->_user;
    }

    static public function setUser($user = null)
    {
        if (is_null($user)) {
            $sessionUser = Model_Users::getGuest();
        } elseif ($user instanceof Zend_Db_Table_Row) {
            $sessionUser = new stdClass();

            $sessionUser->id_user = $user->id_user;
            $sessionUser->name    = $user->name;
            $sessionUser->login   = $user->login;
            $sessionUser->group   = $user->group;
            $sessionUser->post    = $user->post;
            $sessionUser->id_role = $user->id_role;
            $sessionUser->role = $user->role;
            $sessionUser->id_organisation = $user->id_organisation;
            $sessionUser->email   = $user->email;
            $sessionUser->phone   = $user->phone;
        } elseif (is_array($user)) {
            $sessionUser = new stdClass();

            $sessionUser->id_user = isset($user['id_user']) ? $user['id_user'] : null;
            $sessionUser->name    = isset($user['name']) ? $user['name'] : null;
            $sessionUser->login   = isset($user['login']) ? $user['login'] : null;
            $sessionUser->group   = isset($user['group']) ? $user['group'] : null;
            $sessionUser->post    = isset($user['post']) ? $user['post'] : null;
            $sessionUser->id_role = isset($user['id_role']) ? $user['id_role'] : null;
            $sessionUser->role = isset($user['role']) ? $user['role'] : null;
            $sessionUser->id_organisation = isset($user['id_organisation']) ? $user['id_organisation'] : null;
            $sessionUser->email   = isset($user['email']) ? $user['email'] : null;
            $sessionUser->phone   = isset($user['phone']) ? $user['phone'] : null;
        } else {
            throw new Exception('User instance has unknown type');
        }

        $authSession = new Zend_Session_Namespace(Zend_Registry::get('config')->session->namespace->account);
        $authSession->user = $sessionUser;

        $userManager = self::getInstance();
        $userManager->_user = $sessionUser;
        return $sessionUser;
    }

    static public function getId()
    {
        $user = self::getUser();
        return $user->id_user;
    }

    static public function isLoginas()
    {
        $user = self::getUser();
        return $user->loginas == true;
    }

    static public function getLogin()
    {
        $user = self::getUser();
        return $user->login;
    }

    static public function getEmail()
    {
        $user = self::getUser();
        return $user->email;
    }

    static public function getGroup()
    {
        $user = self::getUser();
        return $user->group;
    }

    /**
     * @param int $id_role
     * @return mixed
     * @deprecated
     */
    static public function getGroupByRole($id_role)
    {
        if (isset(self::$_group_by_role[$id_role])) {
            return self::$_group_by_role[$id_role];
        }

        return null;
    }

    static public function getRole()
    {
        $user = self::getUser();
        return $user->id_role;
    }

    static public function getRoleCaption()
    {
        $user = self::getUser();
        return $user->role;
    }

    static public function getRoleById($id_role)
    {
        if (isset(self::$_role_by_id[$id_role])) {
            return self::$_role_by_id[$id_role];
        }

        return null;
    }

    static public function getName()
    {
        $user = self::getUser();
        return $user->name;
    }

    static public function getOrganisationID()
    {
        $user = self::getUser();
        return $user->id_organisation;
    }

    static public function noGuest()
    {
        return self::getRole() != self::ROLE_GUEST;
    }

    static public function noVisitor()
    {
        return self::getRole() != self::ROLE_VISITOR;
    }

    static public function hashPassword($password, $salted = true)
    {
        return md5($password . ($salted ? Zend_Registry::get('config')->session->salt : ''));
    }

    static public function getHomePage()
    {
        $user = self::getUser();

        $router = Zend_Controller_Front::getInstance()->getRouter();

        if (isset(self::$_group_by_role[$user->id_role])) {
            $group = mb_strtolower(self::$_group_by_role[$user->id_role], 'UTF-8');
            $routeName = "home_{$group}";
            if ($router->hasRoute($routeName)) {
                return $router->assemble(array(), $routeName);
            }
        }

        return '/';
    }
}





