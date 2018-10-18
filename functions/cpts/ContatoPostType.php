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

use StoutLogic\AcfBuilder\FieldsBuilder;

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
        add_action('acf/init', array($this, 'customFields'));
    }

    public function contatoPostTypeActivation()
    {
        flush_rewrite_rules();
    }

    public function customFields(){
        $banner = new FieldsBuilder('banner');
        $banner
            ->addText('title')
            ->addWysiwyg('content')
            ->addImage('background_image')
            ->addRepeater('slides')
            ->addWysiwyg('content')
            ->setLocation('post_type', '==', 'cabecalho');

        acf_add_local_field_group($banner->build());
    }


}
