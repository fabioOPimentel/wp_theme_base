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

use Library\PostTypes;

class ContatoPostType
{

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'contatoPostTypeActivation'));
        $contato =  new PostTypes('contato','Contato','Contato','contato');
        $contato->setSupport( array('title', 'editor', 'revisions', 'thumbnail') );
        //$contato->setRest(true);
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
    }

    public function contatoPostTypeActivation()
    {
        flush_rewrite_rules();
    }
}
