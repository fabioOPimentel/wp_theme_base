<?php

namespace Filters;

if ( !defined( 'ABSPATH' ) ) { exit; };

class Filters
{
    /**
     * Singleton
     */
    
    private static $instance;

    public static function getInstance() 
    {
        if (self::$instance == NULL):
            self::$instance = new self();
        endif;
    }

    function __construct()
    {
        //Insira seus filtros aqui
        add_filter( 'slug', array($this, 'slugfy') );
        /** Disabilitando Gutemberg */
        // disabilitando para posts
        //add_filter('use_block_editor_for_post', '__return_false', 10);

        // disabilitando para post types
        //add_filter('use_block_editor_for_post_type', '__return_false', 10);

        // desabilitando para páginas com base no id
        // add_filter('use_block_editor_for_post_type', function ($is_enabled){
        //     return $this->disableGutembergToPageById($is_enabled, 31);
        // }, 10, 2);
        
        //Desabilitando Blocos especificos do gutemberg
        // add_filter('allowed_block_types', function (){
        //     global $post; 
        //     $allowed_blocks = array(
        //         'core/embed',
        //         'core-embed/youtube',
        //         'core-embed/twitter',
        //         'core-embed/facebook',
        //         'core-embed/instagram',
        //         'core-embed/wordpress'
        //     );
        //     $posttype = 'videos';
        //     return $this->allowedBlockTypes($allowed_blocks, $post, $posttype);
        // }, 10, 2);
    }

    public function disableGutembergToPageById($is_enabled, $pageID)
    {
        
        if ( get_the_ID() === $pageID ) return false;
        
        return $is_enabled;
        
    }

    /**
     * allowedBlockTypes habilita um ou mais blocos para um posttype específico
     * acesse para ver a lista de blocos: https://rudrastyh.com/gutenberg/remove-default-blocks.html
     *
     * @param [array] $allowed_blocks
     * @param [global] $post
     * @param [string] $posttype
     * @return void
     */
    public function allowedBlockTypes($allowed_blocks, $post, $posttype)
    {
        
        if( $post->post_type === $posttype ) {
            return $allowed_blocks;
        }
       return true;
    }

    /**
	 * Filtro Slugfy
	 */

	public function slugfy($string) 
	{
		$table = array(
		'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
		'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
		'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
		'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
		'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
		'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
		'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
		);

		// -- Remove duplicated spaces
		$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

		// -- Returns the slug
		return strtolower(strtr($string, $table));
    }
}