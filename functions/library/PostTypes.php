<?php 

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

class PostTypes
{
    private $post_type;

    private $singular;

    private $plural;

    private $slug;
    
    private $taxonomies;

    private $taxonomy_settings;

    private $textdomain;

    private $showInMenu;
    
    private $position;

    /**
     * Construct Function
     *
     * @param string $post_type string que determina o post_type
     * @param string $singular declara o singular name para o post type
     * @param string $plural declara o name para o post type
     * @param string $slug declara um slug para o post type
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

    /**
     * setSupport
     * 
     * Declare os items suportados por o menu de edição
     * do seu post type
     * 
     * @var array $value Declare os items suportados por o menu de edição do seu post type
     */
    public function setSupport($value = array())
    {
        $this->support = $value;
    }

    /**
     * setCapabilities
     *
     * Determina os capability_type para o post type
     * 
     * @link https://codex.wordpress.org/Function_Reference/register_post_type Documentação official
     * 
     * @var array $value Determina os $capabilities para o post type
     */
    public function setCapabilities($value = array())
    {
        $this->capabilities = $value;
    }

    /**
     * setColumns
     *
     * Defina o formato da coluna de exibição
     * 
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
     * 
     * @param array $value define o formato da coluna
     */
    public function setColumns($value = array())
    {
        $this->columns = $value;
    }

    /**
     * setColumnsDisplay
     *
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
     * 
     * @param array $value define um valor customizado que deve aparecer na coluna de exibição
     */
    public function setColumnsDisplay($value = array())
    {
        $this->columnsDisplay = $value;
    }

    /**
     * setSortable
     *
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
     * 
     * @param array $value define quais valores serão organizaveis
     */
    public function setSortable($value = array())
    {
        $this->sortable = $value;
    }

    /**
     * setShowInMenu
     * 
     * Determina seu o post type sera mostrado no menu
     * administrativo
     *
     * @var boolean $value mostra o post type no menu administrativo 
     */
    public function setShowInMenu($value)
    {
        $this->showInMenu = $value;
    }

    /**
     * setIcon
     *
     * Determina um icon para o post type com base
     * no Dashicons do wordpress
     * 
     * @link https://developer.wordpress.org/resource/dashicons/ Developer Resources: Dashicons
     * 
     * @var string $value Determina um icon com base no dashicons
     */
    public function setIcon($value)
    {
        $this->icon = $value;
    }

    /**
     * setPosition
     * 
     * Define a posição que o post type sera exibida no menu
     *
     * @var int $value Define a posição que o post type sera exibida no menu
     */
    public function setPosition($value)
    {
        $this->position = $value;
    }

    /**
     * setUseTax
     *
     * Define se o post type usa ou não taxonomias/categorias
     * 
     * @param boolean $value Define se o post type usa ou não taxonomias/categorias
     */
    public function setUseTax($value)
    {
        $this->useTax = $value;
    }

    /**
     * setRest
     *
     * Habilita REST API para custom post type
     * 
     * @param boolean $value Habilita REST API
     */
    public function setRest($value)
    {
        $this->enableRest = $value;
    }

    /**
     * addImageSize
     * 
     * Define um formato de corte para uploads
     * do WordPress
     *
     * @param string $name Identificador de tamanho da imagem.
     * @param int $x Largura da imagem em pixels.
     * @param int $y Altura da imagem em pixels.
     */
    public function addImageSize($name,$x,$y)
    {
        add_image_size($name, $x, $y, true);
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
            'show_in_rest' =>  isset($this->enableRest) ? $this->enableRest : false,
            'capability_type' => 'post',
            'capabilities' => isset($this->capabilities) ? $this->capabilities : array('create_posts' => 'edit_others_posts'),
            'map_meta_cap' => true,
            "menu_icon" => isset($this->icon) ? $this->icon : 'dashicons-admin-post',
            'menu_position' => $this->position,
            'rewrite' => array("slug" => $this->slug),
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
            'show_in_rest' =>  isset($this->enableRest) ? $this->enableRest : false,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => true,
            'query_var' => true
        ];

        if(isset($this->useTax)):
            register_taxonomy($this->slug.'_category', $this->post_type, $taxonomy_category_args);
            add_action('restrict_manage_posts', array($this, 'FilterPostTypeByTaxonomy'));
	        add_filter('parse_query', array($this, 'ConvertIdToTermInQuery'));
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
                        if($custom_columns == $name)
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

    public function FilterPostTypeByTaxonomy() 
    {
		global $typenow;
		$post_type = $this->post_type;
        $taxonomy  = $this->slug.'_category';
		if ($typenow == $post_type) {
			$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
			$info_taxonomy = get_taxonomy($taxonomy);
			wp_dropdown_categories(array(
				'show_option_all' => __("Todoas as categorias"),
				'taxonomy'        => $taxonomy,
				'name'            => $taxonomy,
				'orderby'         => 'name',
				'selected'        => $selected,
				'show_count'      => true,
				'hide_empty'      => true,
			));
		};
	}

    public function ConvertIdToTermInQuery($query) 
    {
		global $pagenow;
		$post_type = $this->post_type;
		$taxonomy  = $this->slug.'_category';
		$q_vars    = &$query->query_vars;
		if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
			$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
			$q_vars[$taxonomy] = $term->slug;
		}
	}

}