<?php 

namespace Cpts;

require_once __DIR__ . '../../security.php';

class PostTypes
{
    public $post_type;

    public $singular;

    public $plural;

    public $slug;

    public $support;

    public $showInMenu;

    public $capabilities;

    public $icon;

    public $position;

    public $taxonomies;

    public $useTax;

    public $taxonomy_settings;

    public $columns;

    public $columnsDisplay;

    public $sortable;

    public $textdomain;
    /**
     * Construct Function
     *
     * @param [string] post_type Name
     * @param [string] Singular name
     * @param [string] Plural name
     * @param [string] slug
     */
    public function __construct($post_type_name,$singular,$plural,$slug)
    {
        $this->post_type = $post_type_name;
        $this->singular = $singular;
        $this->plural = $plural;
        $this->slug = $slug;
        register_activation_hook(__FILE__, array($this, 'postTypeActivation'));
        add_action('init', array($this, 'postType'));
        add_action('init', array($this, 'postTypeTaxonomy'));
        add_filter("manage_edit-{$this->post_type}_sortable_columns", array($this, "customRegisterSortable") );
        add_filter("manage_edit-{$this->post_type}_columns", array($this, 'postTypeEditColumns'));
        add_action('manage_posts_custom_column', array($this, 'postTypeColumnsDisplay'), 10, 2);
    }

    public function setSupport($value = array())
    {
        $this->support = $value;
    }

    public function setCapabilities($value = array())
    {
        $this->capabilities = $value;
    }

    public function setColumns($value = array())
    {
        $this->columns = $value;
    }

    public function setColumnsDisplay($value = array())
    {
        $this->columnsDisplay = $value;
    }

    public function setSortable($value = array())
    {
        $this->sortable = $value;
    }

    public function setShowInMenu($value)
    {
        $this->showInMenu = $value;
    }

    public function setIcon($value)
    {
        $this->icon = $value;
    }

    public function setPosition($value)
    {
        $this->position = $value;
    }

    public function setUseTax($value)
    {
        $this->useTax = $value;
    }

    public function postTypeActivation()
    {
        flush_rewrite_rules();
    }

    public function postType()
    {
        $labels = array(
            'name' => sprintf( __('%s', $this->textdomain), $this->plural),
            'singular_name' => sprintf( __('%s', $this->textdomain), $this->singular),
            'edit_item' => sprintf( __('Editar %s', $this->textdomain), $this->singular),
            'new_item' => sprintf( __('Novo %s', $this->textdomain), $this->singular),
            'view_item' => sprintf( __('Ver %s', $this->textdomain), $this->singular),
            'search_items' => sprintf( __('Buscar %s', $this->textdomain), $this->plural),
            'not_found' => sprintf( __('%s não encontrado', $this->textdomain), $this->singular),
            'not_found_in_trash' => sprintf( __('Não há %s na lixaira', $this->textdomain), $this->singular)
        );

        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_menu' => $this->showInMenu,
            'supports' => isset($this->support) ? $this->support : array('title', 'editor', 'revisions', 'thumbnail'),
            'capability_type' => 'post',
            'capabilities' => isset($this->capabilities) ? $this->capabilities : array('create_posts' => 'edit_others_posts'),
            'map_meta_cap' => true,
            "menu_icon" => isset($this->icon) ? $this->icon : 'dashicons-admin-post',
            'menu_position' => $this->position,
            'rewrite' => array("slug" => "contato"),
            'has_archive' => false
        );

        register_post_type($this->post_type, $args);
    }

    public function postTypeTaxonomy()
    {
        $taxonomy_category_labels = [
            'name' => _x('Categoria', $this->textdomain),
            'singular_name' => _x('Categoria', $this->textdomain),
            'search_items' => _x('Buscar categoria', $this->textdomain),
            'popular_items' => _x('Categorias populares', $this->textdomain),
            'all_items' => _x('Todas as categorias', $this->textdomain),
            'parent_item' => _x('Categoria pai', $this->textdomain),
            'parent_item_colon' => _x('Categoria pai:', $this->textdomain),
            'edit_item' => _x('Editar categoria', $this->textdomain),
            'update_item' => _x('Atualizar categoria', $this->textdomain),
            'add_new_item' => _x('Adicionar nova categoria', $this->textdomain),
            'new_item_name' => _x('Novo nome de categoria', $this->textdomain),
            'separate_items_with_commas' => _x('Separar categoriapor virgulas', $this->textdomain),
            'add_or_remove_items' => _x('Adicionar ou remover categoria', $this->textdomain),
            'choose_from_most_used' => _x('Escolher categoria mais usada', $this->textdomain),
            'menu_name' => _x('Categoria', $this->textdomain),
        ];

        $taxonomy_category_args = [
            'labels' => $taxonomy_category_labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => true,
            'query_var' => true
        ];

        if(isset($this->useTax)):
            register_taxonomy($this->slug.'_category', $this->post_type, $taxonomy_category_args);
        endif;
    }

    public function postTypeEditColumns()
    {
        $custom_columns = isset($this->columns) ? $this->columns : array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => _x('Title', 'column name'),
            "date" => __('Date'),
        );
        return $custom_columns;
    }

    public function postTypeColumnsDisplay($custom_columns, $post_id)
    {
        if(isset($this->columnsDisplay)):
            foreach($this->columnsDisplay as $name => $value):
                if($value == 'metabox'):
                    echo  get_post_meta( $post_id, $name, false )[0];
                elseif($value == 'category'):
                    if ($category_list = get_the_term_list($post_id, $name, '', ', ', '')):
                        echo $category_list;
                    else:
                        echo __('Vazio', $this->post_type);
                    endif;
                endif;
            endforeach;
        endif;
    }

    public function customRegisterSortable( $custom_columns )
    {
        if(isset($this->sortable)):
            foreach($this->sortable as $columns => $values):
                $custom_columns[$columns] = $values;
            endforeach;
        endif;
        return $custom_columns;
    }

    public function addImageSize($name,$x,$y)
    {
        add_image_size($name, $x, $y, true);
    }

}