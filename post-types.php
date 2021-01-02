<?php

class DIERREWEB_Post_Types {

  public $domain = 'dierreweb_starter';

	protected static $_instance = null;

	public static function instance() {
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'dr' ), '2.1' );
	}

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'dr' ), '2.1' );
	}

  public function __construct() {

    // Hook into the 'init' action
		add_action( 'init', array( $this, 'register_blocks' ), 1 );
		add_action( 'init', array( $this, 'slider' ), 1 );
    add_action( 'init', array( $this, 'adoption' ), 1 );

    // Duplicate post action for slides
		add_filter( 'post_row_actions', array( $this, 'duplicate_slide_action'), 10, 2 );
		add_filter( 'admin_action_dierreweb_duplicate_post_as_draft', array( $this, 'duplicate_post_as_draft' ), 10, 2 );

    // Manage slides list columns
    add_filter( 'manage_edit-dierreweb_slide_columns', array( $this, 'edit_dierreweb_slide_columns' ) );
    add_action( 'manage_dierreweb_slide_posts_custom_column', array( $this, 'manage_slide_columns') , 10, 2 );

    // Manage slider list columns
    add_filter( 'manage_edit-dierreweb_slider_columns', array( $this, 'edit_dierreweb_slider_columns' ) );
    add_action( 'manage_dierreweb_slider_custom_column', array( $this, 'manage_slider_columns') , 10, 3 );

    // Add shortcode column to block list
		add_filter( 'manage_edit-cms_block_columns', array( $this, 'edit_html_blocks_columns' ) );
		add_action( 'manage_cms_block_posts_custom_column', array( $this, 'manage_html_blocks_columns' ), 10, 2 );

  }

  // **********************************************************************//
  // ! Register Custom Post Type for Dierreweb slider
  // **********************************************************************//

  public function slider() {

    //if(function_exists( get_theme_mod( 'dierreweb_slider' ) ) return;

    $labels = array(
      'name'               => esc_html__( 'Dierreweb Slider', 'dr' ),
      'singular_name'      => esc_html__( 'Slide', 'dr' ),
      'menu_name'          => esc_html__( 'Slides', 'dr' ),
      'parent_item_colon'  => esc_html__( 'Parent Item:', 'dr' ),
      'all_items'          => esc_html__( 'All Slides', 'dr' ),
      'view_item'          => esc_html__( 'View Slide', 'dr' ),
      'add_new_item'       => esc_html__( 'Add New Slide', 'dr' ),
      'add_new'            => esc_html__( 'Add New', 'dr' ),
      'new_item'           => esc_html__( 'New Slide', 'dr' ),
      'edit_item'          => esc_html__( 'Edit Slide', 'dr' ),
      'search_items'       => esc_html__( 'Search Slides', 'dr' ),
      'not_found'          => esc_html__( 'No slides found', 'dr' ),
      'not_found_in_trash' => esc_html__( 'No slides found in Trash', 'dr' )
    );

    $args = array(
      'label'         => esc_html__( 'slide', 'dr' ),
      'labels'        => $labels,
      'supports'      => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes'
      ),
      'public'        => false,
      'show_ui'       => true,
      'menu_position' => 28,
      'menu_icon'     => 'dashicons-images-alt2',
      'show_in_rest'  => true
    );

    register_post_type( 'dierreweb_slide', $args );

    $labels = array(
      'name'                  => esc_html__( 'Sliders', 'dr' ),
      'singular_name'         => esc_html__( 'Slider', 'dr' ),
      'search_items'	        => esc_html__( 'Search Sliders', 'dr' ),
      'popular_items'	        => esc_html__( 'Popular Sliders', 'dr' ),
      'all_items'             => esc_html__( 'All Sliders', 'dr' ),
      'parent_item'		        => esc_html__( 'Parent Slider', 'dr' ),
      'parent_item_colon'	    => esc_html__( 'Parent Slider', 'dr' ),
      'edit_item'				      => esc_html__( 'Edit Slider', 'dr' ),
      'update_item'			      => esc_html__( 'Update Slider', 'dr' ),
      'add_new_item'			    => esc_html__( 'Add New Slider', 'dr' ),
      'new_item_name'			    => esc_html__( 'New Slide', 'dr' ),
      'add_or_remove_items'	  => esc_html__( 'Add or remove Sliders', 'dr' ),
      'choose_from_most_used'	=> esc_html__( 'Choose from most used sliders', 'dr' ),
      'menu_name'             => esc_html__( 'Slider', 'dr' )
    );

    $args = array(
      'labels'       => $labels,
      'public'       => false,
      'hierarchical' => true,
      'show_ui'      => true,
      'show_in_rest' => true
    );

    register_taxonomy( 'dierreweb_slider', array( 'dierreweb_slide' ), $args );
  }

  public function edit_dierreweb_slide_columns( $columns ) {
    $columns = array(
      'cb'           => '<input type="checkbox" />',
      'thumb'        => esc_html__( 'Thumbnail', 'dr' ),
      'title'        => esc_html__( 'Title', 'dr' ),
      'slide-slider' => esc_html__( 'Slider', 'dr' ),
      'date'         => esc_html__( 'Date', 'dr' )
    );

    return $columns;
  }

  public function manage_slide_columns( $column, $post_id ) {
    switch( $column ) {
    case 'slide-slider':
      $terms = get_the_terms( $post_id, 'dierreweb_slider' );
      if( $terms && !is_wp_error( $terms ) ) :
        $cats_links = array();
        foreach( $terms as $term ) {
          $cats_links[] = $term->name;
        }
        $cats = join( ', ' , $cats_links );
      ?>
      <span><?php echo esc_html( $cats) ;?></span>
      <?php endif;
    break;
    }
  }

  public function edit_dierreweb_slider_columns( $columns ) {
    $columns = array(
      'cb'        => '<input type="checkbox" />',
      'name'      => esc_html__( 'Name', 'dr' ),
      'slug'      => esc_html__( 'Slug', 'dr' ),
      'shortcode' => esc_html__( 'Shortcode', 'dr' ),
      'posts'     => esc_html__( 'Count', 'dr' )
    );

    return $columns;
  }

  public function manage_slider_columns( $terms, $columns, $post_id ) {
    switch( $columns ) {
      case 'shortcode':
        if( !$terms && !is_wp_error( $terms ) ) :
          $terms = get_term( $post_id, 'dierreweb_slider' );
          $slug = $terms->slug;
          echo '<strong>[dierreweb_slider slider="' . esc_html( $slug ) . '"]</strong>';
        endif;
      break;
    }
  }

  // **********************************************************************//
  // ! Duplicate Custom Post Type for Dierreweb slider
  // **********************************************************************//

  public function duplicate_slide_action( $actions, $post ) {
    if( $post->post_type != 'dierreweb_slide' ) return $actions;
    if( current_user_can( 'edit_posts' ) ) {
      $actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=dierreweb_duplicate_post_as_draft&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    }
    return $actions;
  }

  public function duplicate_post_as_draft() {

    global $wpdb;

    if( !( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'dierreweb_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
      wp_die( 'No post to duplicate has been supplied!' );
    }

    if( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) return;

    $post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

    $post = get_post( $post_id );

    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    if( isset( $post ) && $post != null ) {

      $args = array(
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $post->post_name,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'draft',
        'post_title'     => $post->post_title . ' (duplicate)',
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
      );

      $new_post_id = wp_insert_post( $args );

      $taxonomies = get_object_taxonomies( $post->post_type );
      foreach( $taxonomies as $taxonomy ) {
        $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
        wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
      }

      $post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
      if( count( $post_meta_infos )!= 0 ) {
        $sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
        foreach( $post_meta_infos as $meta_info ) {
          $meta_key = $meta_info->meta_key;
          if( $meta_key == '_wp_old_slug' ) continue;
          $meta_value = addslashes( $meta_info->meta_value );
          $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
        }
        $sql_query.= implode( " UNION ALL ", $sql_query_sel );
        $wpdb->query( $sql_query );
      }

      wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
      exit;
    } else {
      wp_die( 'Post creation failed, could not find original post: ' . $post_id );
    }
  }

  // **********************************************************************//
	// ! Register Custom Post Type for HTML Blocks
	// **********************************************************************//

  public function register_blocks() {

    $labels = array(
      'name'               => esc_html__( 'HTML Blocks', 'dr' ),
      'singular_name'      => esc_html__( 'HTML Block', 'dr' ),
      'menu_name'          => esc_html__( 'HTML Blocks', 'dr' ),
      'parent_item_colon'  => esc_html__( 'Parent Item:', 'dr' ),
      'all_items'          => esc_html__( 'All Items', 'dr' ),
      'view_item'          => esc_html__( 'View Item', 'dr' ),
      'add_new_item'       => esc_html__( 'Add New Item', 'dr' ),
      'add_new'            => esc_html__( 'Add New', 'dr' ),
      'edit_item'          => esc_html__( 'Edit Item', 'dr' ),
      'update_item'        => esc_html__( 'Update Item', 'dr' ),
      'search_items'       => esc_html__( 'Search Item', 'dr' ),
      'not_found'          => esc_html__( 'Not found', 'dr' ),
      'not_found_in_trash' => esc_html__( 'Not found in Trash', 'dr' )
    );

    $args = array(
      'label'           => esc_html__( 'cms_block', 'dr' ),
      'description'     => esc_html__( 'CMS Blocks for custom HTML to place in your pages', 'dr' ),
      'labels'          => $labels,
      'supports'        => array(
        'title',
        'editor'
      ),
      'show_ui'         => true,
      'menu_position'   => 29,
      'menu_icon'       => 'dashicons-schedule',
      'show_in_rest'    => true,
      'capability_type' => 'page'
    );

    register_post_type( 'cms_block', $args );
  }

  public function edit_html_blocks_columns( $columns ) {
    $columns = array(
      'cb'        => '<input type="checkbox" />',
      'title'     => esc_html__( 'Title', 'dr' ),
      'shortcode' => esc_html__( 'Shortcode', 'dr' ),
      'date'      => esc_html__( 'Date', 'dr' )
    );

    return $columns;
  }

  public function manage_html_blocks_columns( $column, $post_id ) {
    switch( $column ) {
      case 'shortcode':
        echo '<strong>[html_block id="' . $post_id . '"]</strong>';
      break;
    }
  }

  // **********************************************************************//
  // ! Register Custom Post Type for Dierreweb adoption
  // **********************************************************************//

  public function adoption() {

    $labels = array(
      'name'               => esc_html__( 'Dierreweb Adoption', 'dr' ),
      'singular_name'      => esc_html__( 'Adoption', 'dr' ),
      'menu_name'          => esc_html__( 'Adoptions', 'dr' ),
      'parent_item_colon'  => esc_html__( 'Parent Item:', 'dr' ),
      'all_items'          => esc_html__( 'All Items', 'dr' ),
      'view_item'          => esc_html__( 'View Item', 'dr' ),
      'add_new_item'       => esc_html__( 'Add New Item', 'dr' ),
      'add_new'            => esc_html__( 'Add New', 'dr' ),
      'edit_item'          => esc_html__( 'Edit Item', 'dr' ),
      'update_item'        => esc_html__( 'Update Item', 'dr' ),
      'search_items'       => esc_html__( 'Search Item', 'dr' ),
      'not_found'          => esc_html__( 'Not found', 'dr' ),
      'not_found_in_trash' => esc_html__( 'Not found in Trash', 'dr' )
    );

    $args = array(
      'label'               => esc_html__( 'adoption', 'dr' ),
      'labels'              => $labels,
      'supports'            => array(
        'title',
        'editor',
        'author',
        'thumbnail',
        'excerpt',
        'comments',
        'revisions'
      ),
      'rewrite'             => array(
        'slug' => 'adoption'
      ),
      'capability_type'     => 'page',
      'has_archive'         => true,
      'menu_icon'           => 'dashicons-pets',
      'menu_position'       => 28,
      'public'              => true,
      'exclude_from_search' => true
    );

    register_post_type( 'adoption ', $args );

    $labels = array(
      'name'					        => esc_html__( 'Adoption Categories', 'dr' ),
      'singular_name'			    => esc_html__( 'Adoption Category', 'dr' ),
      'search_items'			    => esc_html__( 'Search Categories', 'dr' ),
      'popular_items'		    	=> esc_html__( 'Popular Adoption Categories', 'dr' ),
      'all_items'			      	=> esc_html__( 'All Adoption Categories', 'dr' ),
      'parent_item'			      => esc_html__( 'Parent Category', 'dr' ),
      'parent_item_colon'		  => esc_html__( 'Parent Category', 'dr' ),
      'edit_item'				      => esc_html__( 'Edit Category', 'dr' ),
      'update_item'			      => esc_html__( 'Update Category', 'dr' ),
      'add_new_item'		     	=> esc_html__( 'Add New Category', 'dr' ),
      'new_item_name'			    => esc_html__( 'New Category', 'dr' ),
      'add_or_remove_items'	  => esc_html__( 'Add or remove Categories', 'dr' ),
      'choose_from_most_used'	=> esc_html__( 'Choose from most used text-domain', 'dr' ),
      'menu_name'				      => esc_html__( 'Category', 'dr' )
    );

    $args = array(
      'labels'       => $labels,
      'pubblic'      => true,
      'hierarchical' => true,
      'capabilities' => array()
     );

     register_taxonomy( 'adopted', array( 'adoption' ), $args );
  }
}

function DIERREWEB_Theme_Plugin() {
	return DIERREWEB_Post_Types::instance();
}

$GLOBALS['dierreweb_theme_plugin'] = DIERREWEB_Theme_Plugin();

// Support shortcodes in text widget
add_filter( 'widget_text', 'do_shortcode' );
