<?php 

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

use Library\Taxonomy;

class PostTypes extends Taxonomy
{
    protected $post_type;

    protected $singular;

    protected $plural;

    protected $slug;

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
        add_filter("manage_edit-{$this->post_type}_sortable_columns", array($this, "customRegisterSortable") );
        add_filter("manage_edit-{$this->post_type}_columns", array($this, 'postTypeEditColumns'));
        add_action('manage_posts_custom_column', array($this, 'postTypeColumnsDisplay'), 10, 2);
    }

    /**
     * Set the value of textdomain
     *
     * @return  self
     */ 
    public function setTextdomain($textdomain)
    {
        load_theme_textdomain( $textdomain, TEMPLATEPATH.'/languages' );
        $this->textdomain = $textdomain;
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
        //$this->useTax = $value;
        if($value){

            add_action('init', function() use ($post_type,$name,$textdomain,$enableRest) {
                $this->postTypeTaxonomy();
            });
        }
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
     * getPostType
     * 
     * Retorna uma string com o post-type
     * 
     */
    public function getPostType()
    {
        return $this->post_type;
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
}