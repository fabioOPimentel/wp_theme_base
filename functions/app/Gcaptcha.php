<?php

namespace App;

require_once __DIR__ . '../../security.php';

class Gcaptcha 
{

    public function verifyCaptcha()
    {
        $secret = '[secret]';
        $response = filter_input(INPUT_POST,'response');
        $ip = filter_input(INPUT_POST,'ip');

        $post = http_build_query(
            array (
                'response' => $response,
                'secret' => $secret,
                'remoteip' => $ip
            )
        );
        $opts = array('http' => 
            array (
                'method' => 'POST',
                'header' => 'application/x-www-form-urlencoded',
                'content' => $post
            )
        );

        $context = stream_context_create($opts);

        $serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if (!$serverResponse) {
            exit('falha ao validar o Recaptcha');
        }

        $result = json_decode($serverResponse);

        if (!$result -> success) {
            exit('Recaptcha Invalido');
        }

        exit('Recaptcha Validado');

        die();
    }

}