<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

class Taxonomy 
{
    public function __construct($post_type_name,$name,$textdomain,$enableRest)
    {
        $this->post_type = $post_type_name;
        $this->slug = $name;
        $this->textdomain = $textdomain;
        $this->enableRest = $enableRest;
        
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
        add_filter("manage_edit-{$this->post_type}_sortable_columns", array($this, "customRegisterSortable") );
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
        add_filter("manage_edit-{$this->post_type}_columns", array($this, 'postTypeEditColumns'));
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
        if(isset($this->columnsDisplay)){
            add_action('manage_posts_custom_column', array($this, 'postTypeColumnsDisplay'), 10, 2);
            add_action('manage_pages_custom_column', array($this, 'postTypeColumnsDisplay'), 10, 2);
        }
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

        register_taxonomy($this->slug.'_category', $this->post_type, $taxonomy_category_args);
        add_action('restrict_manage_posts', array($this, 'FilterPostTypeByTaxonomy'));
        add_filter('parse_query', array($this, 'ConvertIdToTermInQuery'));
    }

    public function FilterPostTypeByTaxonomy() 
    {
		global $typenow;
        $taxonomy  = $this->slug.'_category';
		if ($typenow == $this->post_type) {
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
        $taxonomy  = $this->slug.'_category';
		$q_vars    = &$query->query_vars;
		if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
			$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
			$q_vars[$taxonomy] = $term->slug;
		}
    }

    public function postTypeColumnsDisplay($custom_columns, $post_id)
    {
        if(isset($this->columnsDisplay)){
            foreach($this->columnsDisplay as $name => $value):
                if($value == 'metabox'):
                    if($custom_columns == $name){
                        echo  get_post_meta( $post_id, $name, false )[0];
                    };
                elseif($value == 'category'):
                    if ($category_list = get_the_term_list($post_id, $name, '', ', ', '')):
                        echo $category_list;
                    else:
                        if($custom_columns == $name)
                            echo __('Vazio', $this->post_type);
                    endif;
                elseif($value == 'datepicker'):
                    if($custom_columns == $name){
                        if(get_post_meta( $post_id, $name, false )[0])
                            echo  date('d/m/Y', strtotime(get_post_meta( $post_id, $name, false )[0]));
                    };
                endif;
            endforeach;
        }
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

    public function postTypeEditColumns()
    {
        $custom_columns = isset($this->columns) ? $this->columns : array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => _x('Title', 'column name'),
            "date" => __('Date'),
        );
        return $custom_columns;
    }
}