<?php
/**
 * @package WP Plugin Info
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 *
 * -------------------------------------------------------------------------- */

// Direct script access denied
if (! defined('ABSPATH')) exit;


/**
 * Admin area class
 *
 * @since 1.1
 */
class WPPluginInfoAdmin {	
	/**
	 * @since 1.1
	 */
	const SETTINGS_PAGE_SLUG = 'wp-plugin-info-settings';
	
	/**
	 * @since 1.1
	 * @var bool
	 */
	private $display_msg = false;
	
	/**
	 * Utility method: returns an instance of the class.
	 *
	 * @since 1.1
	 */
	public static function load() {
		$class = __CLASS__;
		
		return new $class;
	}

	/**
	 * Adds the necessary actions and filters to manage the
	 * plugin's admin business.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'add_submenu'));
		
		if ( $this->pagenow_is_settings() )
			add_action('admin_init', array($this, 'process_flush_action'));
	}
	
	/**
	 * Adds the submenu item "Sitemap" to the WordPress menu "Settings",
	 * registers the plugin settings page into the WorPress Settings System.
	 *
	 * This method is hooked into the admin_menu action hook.
	 *
	 * @see __construct()
	 *
	 * @since 1.1
	 */
	public function add_submenu() {
		add_submenu_page('plugins.php', 'WP Plugin Info', 'WP Plugin Info', 'manage_options', self::SETTINGS_PAGE_SLUG, array($this, 'render_page'));
	}
	
	/**
	 * Processes the 'Delete Cache' action.
	 *
	 * This method is hooked into the admin_init action hook.
	 *
	 * @see __construct()
	 * @since 1.1
	 */
	public function process_flush_action() {
		if ( !( $_POST && isset($_POST['flush_cache']) ) )
			return false;
		
		if ( !current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );
			
		check_admin_referer(self::SETTINGS_PAGE_SLUG, 'wppi_nonce');
		
		// Try to delete the transient
		if (! ($this->display_msg = delete_transient(WPPluginInfo::TRANSIENT_NAME)))
			$this->display_msg = -1;
	}
	
	/**
	 * Renders the settings page.
	 *
	 * Callback method of add_submenu_page()
	 *
	 * @see add_submenu()
	 *
	 * @since 1.1
	 */
	public function render_page() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>' . get_admin_page_title() . '</h2>';
		
		if ( $this->display_msg ) {
			if ( $this->display_msg === true )
				echo '<div class="updated"><p>Cache <strong>deleted</strong>.</p></div>';
			else
				echo '<div class="error"><p>The cache has already been deleted or is empty.</p></div>';

			$this->display_msg = false;			
		}
				
		echo '<p><em>After clearing the cache, you have to visit the page where you added the shortcode '
		   . '(or template tag) in order to retrieve new info.</em></p>';
		  
		if ( $exp_date = WPPluginInfo::get_cache_timeout() ) {
			echo '<form method="post">'
			   . '<input type="hidden" name="wppi_nonce" value="' . wp_create_nonce(self::SETTINGS_PAGE_SLUG) . '" />'
			   . '<p>The cache will expire on: <strong>' . $exp_date 
			   . '</strong>&nbsp&nbsp&nbsp<input type="submit" class="button-secondary" name="flush_cache" value="Delete" /></p>'
			   . '</form>';
		 }
		 else { echo '<p><strong>Info:</strong> The cache is empty.</p>'; }
		 
		 echo '</div>';
	}
	
	/**
	 * Checks whether or not the current page is the plugin settings page
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	private function pagenow_is_settings() {
		return ( $_GET && isset($_GET['page']) && $_GET['page'] == self::SETTINGS_PAGE_SLUG );
	}
}
?>