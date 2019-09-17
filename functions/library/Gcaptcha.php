<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

class Gcaptcha 
{
    public function verifyCaptcha()
    {
        $this->queryBuilder();
        $this->setOptions();

        $context = stream_context_create($this->opts);

        $serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

        $this->defineResult($serverResponse);
    }

    protected function queryBuilder()
    {
        $this->secret = '[YOU-SECRET]';
        $this->response = filter_input(INPUT_POST,'response');
        $this->ip = filter_input(INPUT_POST,'ip');

        $this->post = http_build_query(
            array (
                'response' => $this->response,
                'secret' => $this->secret,
                'remoteip' => $this->ip
            )
        );
    }

    protected function setOptions()
    {
        $this->opts = array('http' => 
            array (
                'method' => 'POST',
                'header' => 'application/x-www-form-urlencoded',
                'content' => $this->post
            )
        );
    }

    protected function defineResult($serverResponse)
    {
        if (!$serverResponse) {
            exit(
                json_encode( 
                    array(
                        'msg' => 'falha ao validar o Recaptcha',
                        'status' => false
                    ) 
                )    
            );
        }

        $result = json_decode($serverResponse);

        if (!$result -> success) {
                exit(
                    json_encode( 
                        array(
                        'msg' => 'Recaptcha Invalido',
                        'status' => false
                    ) 
                ) 
            );
        }

        
        exit(
            json_encode( 
                array(
                'msg' => 'Recaptcha Valido',
                'status' => true
                ) 
            )
        );

        die();
    }

}