<?php
/**
 * Plugin Name: QQWorld WooCommerce Assistant Lite
 * Plugin URI: https://www.qqworld.org/product/qqworld-woocommerce-assistant/
 * Description: QQWorld WooCommerce Assistant, including Brands Add-on, Shipping Add-on.
 * Version: 1.0.1
 * Author: Michael Wang
 * Author URI: http://www.qqworld.org/
 * Text Domain: qqworld-woocommerce-assistant
 */
namespace qqworld;

use qqworld\core\options;
use qqworld\core\extension;

define('QAFW_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('QAFW_URL', plugin_dir_url(__FILE__));

include_once QAFW_DIR . 'options.php';
include_once QAFW_DIR . 'extension.php';

class core {
	var $text_domain = 'qqworld-woocommerce-assistant';
	var $options;
	var $extension;

	public function __construct() {
		$this->options = new options;
	}

	public function init() {
		add_action( 'plugins_loaded', array($this, 'load_language') );
		add_action( 'plugins_loaded', array($this, 'load_plugin') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_action( 'admin_init', array($this, 'register_settings') );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
	}

	public function load_plugin() {
		$this->extension = new extension;
		$this->extension->init();
	}

	public function register_settings() {
		register_setting($this->text_domain.'-extension', 'QAWC_EXTENSION');
	}

	public function load_language() {
		load_plugin_textdomain( $this->text_domain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function admin_menu() {
		$page_title = __('WooCommerce Assistant', $this->text_domain);
		$menu_title = __('WooCommerce Assistant', $this->text_domain);
		$capability = 'administrator';
		$menu_slug = 'qqworld-woocommerce-assistant';
		$function = array($this, 'admin_page');
		$icon_url = 'none';
		$settings_page = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if (preg_match('/page_qqworld-woocommerce-assistant.*?$/i', $screen->base, $matches) || $screen->base == 'post') {
			wp_enqueue_script('wp-pointer');
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('masonry');
			wp_enqueue_script( 'qawc', QAFW_URL . 'js/admin.js', array('jquery') );
		}
		wp_enqueue_style( 'qawc', QAFW_URL . 'css/style.css' );
	}

	//add link to plugin action links
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings_link = '<a href="' . menu_page_url( 'qqworld-woocommerce-assistant', 0 ) . '">' . __( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}
		return $links;
	}

	public function admin_page() {
?>
<div id="arena-3d">
	<div id="left-3d"></div>
	<div id="front-3d"></div>
	<div id="right-3d"></div>
	<div id="top-3d"></div>
	<div id="bottom-3d"></div>
	<div id="back-3d"></div>
</div>
<div class="wrap" id="qqworld-woocommerce-assistant-container">
	<h2><?php _e('QQWorld WooCommerce Assistant Lite', $this->text_domain); ?></h2>
	<p><?php _e("QQWorld WooCommerce Assistant, including Brands Add-on, Shipping Add-on.", $this->text_domain); ?></p>
	<img id="banner" src="<?php echo QAFW_URL; ?>images/banner-772x250.png" title="<?php _e('WooCommerce Assistant', $this->text_domain); ?>" />
	<form action="options.php" method="post" id="update-form">
		<?php settings_fields($this->text_domain.'-extension'); ?>
		<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br></div>
		<?php $this->extension->form(); ?>
	</form>
</div>
<?php
	}
}
$core = new core;
$core->init();