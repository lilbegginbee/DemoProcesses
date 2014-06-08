<?php

 class CORE_Debug
 {
     public static $enabled = false;

     public static function init()
     {
         $firephp = false;
         if( isset(Zend_Registry::get('config')->debug) && isset(Zend_Registry::get('config')->debug->firephp) ) {
            $firephp = Zend_Registry::get('config')->debug->firephp;
         }

         if( $firephp ) {
            self::$enabled = true;
            require_once( APPLICATION_PATH . './../library/FirePHPCore/fb.php');
         }
         else {
            self::$enabled = false;
         }


     }

     public static function dump( $message )
     {
         if( self::$enabled ) {
             ob_start();
             var_dump( $message ) ;
             $message = ob_get_clean();
             FB::log( $message );
         }
     }

     public static function log( $message )
     {
        if( self::$enabled ) {
            FB::log( $message );
        }
     }

     public static function info( $message )
     {
         if( self::$enabled ) {
             FB::info( $message );
         }
     }

     public static function warn( $message )
     {
         if( self::$enabled ) {
             FB::warn( $message );
         }
     }

     public static function error( $message )
     {
         if( self::$enabled ) {
             FB::error( $message );
         }
     }
 }