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
 * Main Plugin class
 *
 * @since 1.0
 */
class WPPluginInfo {
	/**
	 * @since 1.0
	 */
	const TRANSIENT_NAME = 'wp_plugin_info';
	
	/**
	 * @since 1.0
	 */
	const REQUEST_URI = 'http://api.wordpress.org/plugins/info/1.0/';
	
	/**
	 * Singleton instance.
	 *
	 * @since 1.1
	 * @var object
	 */
	private static $instance;
	
	/**
	 * The slug associated to the plugin whose we want to retrieve info.
	 *
	 * @since 1.0
	 * @var string
	 */
	public $plugin_id;
	
	/**
	 * Array of retrieved info. Structure:
	 * $info = array(
	 * 		'plugin_id' => Array of Information,
	 *		'plugin_id' => Array of Information,
	 * 		. . .
	 * )
	 *
	 * @since 1.0
	 * @var array
	 */
	private $info;
	
	/**
	 * Returns a singleton instance.
	 *
	 * @since 1.1
	 * @return object
	 */
	public static function instance() {
		if(! self::$instance) {
			$class = __CLASS__;
			self::$instance = new $class;
		}
		
		return self::$instance;
	}
	
	/**
	 * Prevents direct instantiation of the class.
	 *
	 * @since 1.1.3
	 */
	private function __construct() {}
	
	/**
	 * Registers the shortcode.
	 *
	 * @since 1.1
	 */
	public function register_shortcode() {
		add_shortcode('wp-plugin-info', array($this, 'do_shortcode'));
	}
	
	/**
	 * Shortcode callback method.
	 *
	 * @since 1.0
	 * @return string
	 */
	public function do_shortcode($atts) {
		$atts = &shortcode_atts(array(
			'plugin_id' => WPPI_PLUGIN_ID,
			'info_id' => null,
			'default' => '-',
			'date_format' => null
		), $atts);
		
		return $this->get_plugin_info($atts);
	}
	
	/**
	 *
	 *
	 * @since 1.1
	 *
	 * @param string $info_ID
	 * @param array $args
	 * @return string
	 */
	public function get_plugin_info(&$args) {
		$info = null;
		$this->plugin_id = $args['plugin_id'];
		
		if (! ($this->plugin_id && $args['info_id'] && ( $info = $this->get_info($args['info_id']) )) )
			return esc_attr($args['default']);
		
		if ( $args['date_format'] ) {
			$timestamp = strtotime($info);
			$info = ($timestamp ? date($args['date_format'], $timestamp) : $args['default']);
		}
			
		return wp_kses_post($info);
	}
	
	/**
	 * Retrieves an info value for a given key.
	 *
	 * @since 1.0
	 *
	 * @param string $id
	 * @return string
	 */
	private function get_info($id) {
		if (! $this->info)
			$this->info = &get_transient(self::TRANSIENT_NAME, array());
		
		if (! isset($this->info[$this->plugin_id]))
			$this->fetch_remote_data();
		
		return (isset($this->info[$this->plugin_id][$id]) ? (string) $this->info[$this->plugin_id][$id] : false);
	}
	
	/**
	 *
	 *
	 * @since 1.1
	 */
	private function fetch_remote_data() {
		$obj = new stdClass();
		$obj->slug = $this->plugin_id;
		
		$args = array(
			'timeout' => 10,
			'body' => array( 'action' => 'plugin_information', 'request' => serialize($obj))
		);
		
		$response = wp_remote_post(self::REQUEST_URI, $args);
		
		if (is_wp_error($response) || (! isset($response['body'])))
			return false;
		
		$data = (array) @unserialize($response['body']);
		
		// Extracts the sections array
		if (isset($data['sections']) && is_array($data['sections'])) {
			foreach ($data['sections'] as $key => $value)
				$data[$key] = $value;
		}
		
		// Turns the array of contributors into a comma separated list of links
		if (isset($data['contributors']) && is_array($data['contributors'])) {
			foreach ($data['contributors'] as $key => &$value)
				$value = '<a href="' . $value . '">' . $key . '</a>';
				
			$data['contributors'] = &implode(', ', $data['contributors']);
		}	
		
		// Turns the array of tags into a comma separated list of tags
		if (isset($data['tags']) && is_array($data['tags']))
			$data['tags'] = &implode(', ', $data['tags']);
		
		// Just unset unnecessary array elements, reduntancy isn't what we need
		unset($data['compatibility'], $data['sections']);
		
		// Stores the new retrieved data into the global $info array
		$this->info[$this->plugin_id] = &$data;
		
		// We cache data because performace matter most
		// The expiration time is limited to 10 minutes
		set_transient(self::TRANSIENT_NAME, $this->info, max(WPPI_CACHE_EXPIRATION, 600));
	}
	
	/**
	 * Returns a formatted and localised version of the cache expiration date 
	 * or false if an error occurs.
	 *
	 * @since 1.1.3
	 * @return bool|string
	 */
	public static function get_cache_timeout() {
		$exp_timestamp = (int) get_option( '_transient_timeout_' . self::TRANSIENT_NAME );
		
		if (! $exp_timestamp ) return false;
		
		$format = get_option('date_format') . ' \a\t ' . get_option('time_format');
		
		return gmdate( $format , $exp_timestamp + get_option('gmt_offset') * 3600 );
	}
}
?>