<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EnviandoContato
{

    public function sendContactForm()
    {
        $all_fields = $this->getFields();

        if ( 
            ! isset( $_POST['security_contact'] ) 
            || ! wp_verify_nonce( $_POST['security_contact'], 'contato_seguro' ) 
        ) {
         
            echo json_encode(
                [
                    'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.',
                    'status' => 'error'
                ]
            );
           die;
         
        } else {
            if (!$this->messageSent($all_fields)) {
                echo $this->getStatusMessage('error_sent')->scalar;
            }
            else{
                echo $this->getStatusMessage('success')->scalar;
            }
            die;
        }
    }

    public function sendContactFormAtt()
    {
        $all_fields = $this->getFields();
        $are_all_fields_ok = $this->areAllFieldsOk($all_fields);
        $file_upload = $this->filterAttachment('arquivo',array("jpg", "pdf"),2097152);

        if ( 
            ! isset( $_POST['security_contact'] ) 
            || ! wp_verify_nonce( $_POST['security_contact'], 'contato_seguro' ) 
        ) {
         
            echo json_encode(
                [
                    'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.',
                    'status' => 'error'
                ]
            );
           die;
         
        } else {
            if (!$file_upload) {
                echo $this->getStatusMessage('error_sent')->scalar;
            }
            elseif (!$this->messageSent($all_fields,$file_upload)) {
                echo $this->getStatusMessage('error_sent')->scalar;
            }
            else{
                echo $this->getStatusMessage('success')->scalar;
            }
            die;
        }
    }

    private function getFields()
    {
        $fields = array();

        foreach($_POST as $key=>$value){
            if(
                $key !== 'security_contact' &&
                $key !== '_wp_http_referer' &&
                $key !== 'g-recaptcha-response' &&
                $key !== 'action'
            ){
                if(is_array($value)){
                    $fields[$key] = $value;
                }else{
                    $fields[$key] = ($key == 'mensagem') ? wp_kses($value, 'br') : wp_strip_all_tags(trim($value));
                } 
            }
            
        }

        return $fields;
    }

    private function filterAttachment($name,$types=array(),$size)
    {
        $file_tmp  = $_FILES[$name]['tmp_name'];
        $file_name = $_FILES[$name]['name'];
        $file_size = $_FILES[$name]['size'];
        if($file_size > $size){
            return false;
        }
        $extension = end(explode(".", $file_name));
        
        $allowedExts = $types;
        if (in_array($extension, $allowedExts))
        {
            $field = array(
                'field_name' => array(
                    'value' =>  $file_name,
                    'is_required' => true
                ),
                'field_tmp' => array(
                    'value' =>  $file_tmp,
                    'is_required' => true
                ),
            );

            return $field;
        } else {
            return false;
        }
    }

    private function areAllFieldsOk($fields)
    {
        foreach ($fields as $field) {
            if ($this->isFieldEmpty($field)) {
                return false;
            }
        }
        return true;
    }

    private function validTel($tel)
    {
        $re_tel = "/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})?\s?\-?(\d{4,5}))$/";
        if (!preg_match($re_tel, $tel)) {
            return false;
        } else {
            return $tel;
        }
    }

    private function isFieldEmpty($field)
    {
        return empty($field['value']);
    }

    private function getStatusMessage($status = 'success')
    {
        $message = array(
            'success' => $this->getStatusMessageSuccess(),
            'error' => $this->getStatusMessageError(),
            'error_sent' => $this->getStatusMessageErrorSent()
        );
        return (object) $message[$status];
    }

    private function getStatusMessageSuccess()
    {
        return json_encode(
            [
                'message' => "Email enviado com sucesso!",
                'status' => 'success'
            ]
        );
    }

    private function unslug($slugs)
    {
        $slugs = str_replace('-'," ",$slugs);
        return $result = ucfirst($slugs);
    }

    private function getStatusMessageError()
    {
        return json_encode(
            [
                'message' => 'Preencha todos os campos corretamenta!',
                'status' => 'warning'
            ]
        );
    }

    private function getStatusMessageErrorSent()
    {
        return json_encode(
            [
                'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.',
                'status' => 'danger'
            ]
        );
    }

    private function messageSent($fields,$file=null)
    {

        $mail = new PHPMailer;

        $mail->SMTPDebug  =  0;
        $mail->IsSMTP(); //Defina que será SMTP 
        $mail->Host = 'smtp.gmail.com'; //Endereço do servidor SMTP
        $mail->SMTPAuth = true; //Usar autenticação SMTP (opcional)
        $mail->Username = 'taointerativa@gmail.com'; //Usuario do servidor SMTP
        $mail->Password = 't@ointerativ@2019!'; //Senha do servidor SMTP
        $mail->SMTPSecure = 'ssl'; //Tipo de encriptação
        $mail->Port = 465;

        //Defina o remetente
        $mail->setFrom('taointerativa@gmail.com', $fields['assunto']); //Seu email e nome
        //Defina destinatario(s)
        foreach($fields['destinatario'] as $dest){
            $mail->addAddress(sanitize_email($dest));
        }
        
        $mail->addReplyTo( sanitize_email($fields['email']) ); //Email para resposta

        $mail->isHTML(true); //Define se o email é HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagen (opcional)

        $mail->Subject = 'Contato pelo Site'; //Define o assunto

        if($file)
            $mail->addAttachment($file['field_tmp']['value'],$file['field_name']['value']);
            
        $content = "<table><thead><tr><th>Contado Pelo site</th></tr></thead><tbody><tr><td>";
        foreach($fields as $key => $value){
            if($key != 'destinatario')
                $content .= "<strong>".$this->unslug($key).":</strong> $value<br><br>";
        }
        $content .= "</td></tr></tbody></table>";

        $mail->Body = $content;

        return $mail->Send();
    }

}