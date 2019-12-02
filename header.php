<?php if ( !defined( 'ABSPATH' ) ) { exit; }; ?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <?php wp_head() ?>
        <title>Teme Base</title>

        <!-- Google Recaptcha -->
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script type="text/javascript">
            var onloadCallback = function() {
                widgetId = grecaptcha.render('html_element', {
                'sitekey' : '[SITE_KEY]',
                'callback' : myapp.correctCaptcha
                });
            };
        </script>
    </head>
    <body <?php body_class(); ?>>
        <section id="main">