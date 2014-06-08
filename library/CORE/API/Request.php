<?php

class CORE_API_Request {

    /**
     * Обращение к API изнутри
     *
     * @param string $url
     * @param array $postData
     */
    public static function execute($url,$postData)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_COOKIE         => http_build_query($_COOKIE)
        );



        $ch = curl_init($url);
        curl_setopt_array($ch,$options);
        session_write_close();
        $content = curl_exec($ch);
        session_start();
        return $content;
    }
}