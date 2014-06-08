<?php
class UnitTestController extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {

        defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));


        $this->bootstrap = new Zend_Application(
            'testing',
            APPLICATION_PATH . '/configs/application.ini'
        );

        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->registerNamespace('CORE');

        $registry = Zend_Registry::getInstance();
        $config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini', 'development');
        $registry->set('config', $config);

        CORE_Debug::init();

// Адаптер работы с БД
        $db = new CORE_System_DB($config->db->config->toArray());
        $registry->set('db', $db);
        $db->getProfiler()->setEnabled(true);

// Cacher init
        $cacher = CORE_Cache_Driver_Factory::createCacher($config->cacher);
        $registry->set('Cacher', $cacher);

// Кеш для хранения мета информации о БД
        $frontendOptions = array(
            'lifetime' => 86400*31,
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => '../application/cache/dbschema',
            'hashed_directory_perm' => 0777
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
// Set db cache
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Db_Table::setDefaultAdapter($db);

// Set locale
        $locale = 'ru_RU';
        Zend_Registry::set('locale', $locale);
        $locale = new Zend_Locale($locale);
        Zend_Registry::set('Zend_Locale', $locale);

        Zend_Layout::startMvc(
            array(
                'layoutPath' => APPLICATION_PATH . '/views/layouts',
                'layout' => 'layout'
            )
        );
        Zend_Registry::set('Layout', Zend_Layout::getMvcInstance());

        parent::setUp();
    }

}