<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EnviandoCustomContato
{

    public function sendContactForm()
    {
        $all_fields = $this->getFields();
        $are_all_fields_ok = $this->areAllFieldsOk($all_fields);

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
            if (!$are_all_fields_ok) {
                echo $this->getStatusMessage('error')->scalar;
            }
            elseif (!$this->messageSent($all_fields)) {
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
            if (!$are_all_fields_ok) {
                echo $this->getStatusMessage('error')->scalar;
            }
            elseif (!$file_upload) {
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
        $fields = array(
            'nome' => array(
                'value' => sanitize_text_field(filter_input(INPUT_POST, 'nome')),
                'is_required' => true
            ),
            'produto' => array(
                'value' => sanitize_text_field(filter_input(INPUT_POST, 'produto')),
                'is_required' => false
            ),
            'email' => array(
                'value' => sanitize_email(filter_input(INPUT_POST, 'email')),
                'is_required' => true
            ),
            'destinatario' => array(
                'value' => sanitize_email(filter_input(INPUT_POST, 'destinatario')),
                'is_required' => true
            )
            ,
            'tel' => array(
                'value' => $this->validTel(filter_input(INPUT_POST, 'tel')),
                'is_required' => true
            ),
            'msg' => array(
                'value' => wp_kses(filter_input(INPUT_POST, 'msg'), 'br'),
                'is_required' => true
            )
        );

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
            if ($this->isFieldRequiredEmpty($field)) {
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

    private function isFieldRequiredEmpty($field)
    {
        return $field['is_required'] && empty($field['value']);
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
                    'status' => 'error'
                ]
        );
    }

    private function messageSent($fields,$file=null)
    {

        $mail = new PHPMailer;

        $nome = $fields['nome']['value'];
        $email = $fields['email']['value'];

        $mail->SMTPDebug  = 0;
        $mail->IsSMTP(); //Defina que será SMTP 
        $mail->Host = ''; //Endereço do servidor SMTP
        $mail->SMTPAuth = true; //Usar autenticação SMTP (opcional)
        $mail->Username = ''; //Usuario do servidor SMTP
        $mail->Password = ''; //Senha do servidor SMTP
        //$mail->SMTPSecure = 'tls'; //Tipo de encriptação
        $mail->Port = 587;

        //Defina o remetente
        $mail->setFrom('', ''); //Seu email e nome
        //Defina destinatario(s)
        $mail->addAddress(''); //Email Homologação
        $mail->addReplyTo($email); //Email para resposta

        $mail->isHTML(true); //Define se o email é HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagen (opcional)

        $mail->Subject = 'Contato pelo Site'; //Define o assunto

        if($file)
            $mail->addAttachment($file['field_tmp']['value'],$file['field_name']['value']);
            
        $mail->Body = "<table>
                            <thead>
                                <tr>
                                    <th>
                                        Contado Pelo site
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Nome:</b> {$nome}<br>
                                        <b>Email:</b> {$email}<br>
                                    </td>
                                </tr>
                            </tbody>
                    </table>";

        return $mail->Send();
    }

}
