<?php

/* ---------------------------------------------------------------------------------------------
   REGISTER SHORTCODES
------------------------------------------------------------------------------------------------ */

add_shortcode( 'social_buttons', 'dierreweb_shortcode_social' );
add_shortcode( 'html_block', 'dierreweb_html_block_shortcode' );

/* ---------------------------------------------------------------------------------------------
   REGISTER AND DEREGISTER THEME WIDGETS
------------------------------------------------------------------------------------------------ */

if( !function_exists( 'dierreweb_widgets_init' ) ) {
  function dierreweb_widgets_init() {
   if ( !is_blog_installed() ) return;

    /* Register html block widget -------------- */
    register_widget( 'DIERREWEB_Html_Block_Widget' );

    /* Register custom widgets comments -------------- */
    register_widget( 'DIERREWEB_Recent_Comments' );

    /* Register custom widgets recent posts -------------- */
    register_widget( 'DIERREWEB_Recent_Posts' );

    /* Register custom widgets popular posts -------------- */
    register_widget( 'DIERREWEB_Popular_Posts' );

    /* Deregister default widgets replaced by our custom widgets -------------- */
    unregister_widget( 'WP_Widget_Recent_Comments' );

    /* Register adoption categories widget -------------- */
    register_widget('DIERREWEB_Recent_Adoptions');

    /* Register adoption categories widget -------------- */
    register_widget('DIERREWEB_Adoption_Categories');

  }
  add_action( 'widgets_init', 'dierreweb_widgets_init' );
}
