<?php

/* -----------------------------------------------------------------------------------

  Plugin Name: Contato Post Type
  Plugin URI:
  Description: Adiciona dados a compo contato
  Version: 0.3
  Author: Fábio Oliveira Pimentel
  Author URI:
  License: GPLv2

  ----------------------------------------------------------------------------------- */

namespace Cpts;

require_once __DIR__ . '../../security.php';

class ContatoPostType
{

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'contatoPostTypeActivation'));
        $contato =  new PostTypes('contato','Contato','Contato','contato');
        $contato->setSupport( array('title', 'revisions', 'thumbnail') );
        $contato->setCapabilities( array('create_posts' => 'edit_others_posts') );
        $contato->addImageSize('contato', '238', '238');
        $contato->setColumns(array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => _x('Title', 'column name'),
            "test_text" => __('Test Text'),
            "date" => __('Date'),
        ));
        $contato->setColumnsDisplay(array('test_text' => 'metabox'));
        $contato->setSortable(array('test_text'=>'Test Text'));
        add_action('init', array($this, 'customFields'));
    }

    public function contatoPostTypeActivation()
    {
        flush_rewrite_rules();
    }

    public function customFields(){
        $contato =  new OdinMetabox(
            'areacontato',
            'Campos da área contato',
            'contato',
            'normal',
            'high'
        );

        $contato->set_fields(
            array(
                array(
                    'id'         => 'test_text', // Required
                    'label'      => __( 'Test Text', 'odin' ), // Required
                    'type'       => 'text', // Required
                    'attributes' => array( // Optional (html input elements)
                        'placeholder' => __( 'Some text here!' )
                    ),
                    // 'default'  => 'Default text', // Optional
                    'description' => __( 'Text field description', 'odin' ) // Optional
                ),
                // Image Plupload field.
                array(
                    'id'          => 'test_image_plupload', // Required
                    'label'       => __( 'Test Image Plupload', 'odin' ), // Required
                    'type'        => 'image_plupload', // Required
                    // 'default'     => '', // Optional (image attachment ids separated with comma)
                    'description' => __( 'Image Plupload field description', 'odin' ), // Optional
                )
            )
        );
    }


}
