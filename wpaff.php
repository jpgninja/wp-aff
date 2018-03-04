<?php

/*
Plugin Name: Affiliates International
Plugin URI: http://affinternational.com
Description: Change outbound Amazon links to geo-located domain
Author: Client Coffee
Version: 0.2
Author URI:
*/

class IntlAffiliate {

  /**
   * Instantiate variables
   */
  static $plugin_path;
  static $url_slug;


  /**
   * Constructor
   *
   * Instantiate hooks
   * 
   */
  public function __construct() {
    $this->plugin_path = plugin_dir_path(__FILE__);

    include $this->plugin_path . '_includes/admin_options.php';
    
    // @TODO Make this dynamic.
    // @TODO When this changes, we must resave permalinks
    $this->url_slug = 'am';
    
    register_activation_hook( __FILE__, array( $this, 'flush' ) );
    add_action( 'init', array( $this, 'init') );
    add_filter( 'query_vars', array( $this, 'query_vars') );
    add_action( 'parse_request', array( $this, 'parse_request') );

    /**
     * register our affint_settings_init to the admin_init action hook
     */
    add_action('admin_init', 'affint_settings_init');

    /**
     * register our affint_options_page to the admin_menu action hook
     */
    add_action('admin_menu', 'affint_options_page');
  }


  /**
   * Initialize our plugin
   *
   */
  public function init() {
    /**
     * Listen for outgoing Amazon ASIN links
     *
     * @TODO: Merge this rule 
     */

    // Custom tag
    add_rewrite_rule(
      '^'.$this->url_slug.'/([A-Za-z0-9]+)/([A-Za-z0-9-]+)/?',
      'index.php?asin=$matches[1]&aff_id=$matches[2]',
      'top'
    );

    // Default
    add_rewrite_rule(
      '^'.$this->url_slug.'/([A-Za-z0-9]+)/?',
      'index.php?asin=$matches[1]',
      'top'
    );
  }


  /**
   * Flush Rewrite Rules
   * think this is like resetting permalinks? if so, we should run it after save.
   *
   */
  public function flush() {
    $this->init();
    flush_rewrite_rules();
  }


  /**
   * Add query variables to our WP object
   *
   */
  public function query_vars( $query_vars ) {
    $query_vars[] = 'asin';
    $query_vars[] = 'aff_id';

    return $query_vars;
  }


  /**
   * Upon the ASIN key coming in, we initiate our plugin handler
   *
   */
  public function parse_request( &$wp ){
    if ( array_key_exists( 'asin', $wp->query_vars ) ) {
      include $this->plugin_path . '_includes/handler.php';
      exit();
    }
    return;
  }

}


$wpa8185_IntlAffiliate = new IntlAffiliate();