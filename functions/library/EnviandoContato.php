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
        $are_all_fields_ok = $this->areAllFieldsOk($all_fields);

        if (!$are_all_fields_ok) {
            echo $this->getStatusMessage('error')->scalar;
            die();
        }

        if (!$this->messageSent($all_fields)) {
            echo $this->getStatusMessage('error_sent')->scalar;
            die();
        }

        echo $this->getStatusMessage('success')->scalar;
        die();
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
                    'status' => 'danger'
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

    private function messageSent($fields)
    {

        $mail = new PHPMailer;
        $nome = $fields['nome']['value'];
        $email = $fields['email']['value'];
        $dest = $fields['destinatario']['value'];
        $prod = $fields['produto']['value'];
        $tel = $fields['tel']['value'];
        $msg = $fields['msg']['value'];


        $mail->SMTPDebug  =  0 ;
        $mail->IsSMTP(); //Defina que será SMTP 
        $mail->Host = ''; //Endereço do servidor SMTP
        $mail->SMTPAuth = true; //Usar autenticação SMTP (opcional)
        $mail->Username = ''; //Usuario do servidor SMTP
        $mail->Password = ''; //Senha do servidor SMTP
        $mail->SMTPSecure = 'ssl'; //Tipo de encriptação
        $mail->Port = 465;

        //Defina o remetente
        $mail->setFrom('', "Contato Site"); //Seu email e nome
        //Defina destinatario(s)
        $mail->addAddress($dest); //Email destinatario
        $mail->addReplyTo($email); //Email para resposta

        $mail->isHTML(true); //Define se o email é HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagen (opcional)

        $mail->Subject = 'Contato pelo Site'; //Define o assunto
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
                                        <b>Telefone:</b> {$tel}<br>
                                        <b>Produto:</b> {$prod}<br>
                                        <br>
                                        <b>Mensagem:</b> {$msg}
                                    </td>
                                </tr>
                            </tbody>
                    </table>";

        return $mail->Send();
    }

}
