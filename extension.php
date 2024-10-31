<?php
namespace qqworld\core;
use qqworld\core;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class extension extends core {
	var $free; // 是否免费
	var $label; // 扩展标签名
	var $slug; // 扩展的slug
	var $description; //扩展的描述
	var $image; //扩展的图片地址
	var $options;

	var $extensions;

	public function init() {
		$this->includes();
		$this->extensions = apply_filters('qqworld-woocommerce-assistant-extensions', array());
	}

	public function register_settings() {
		register_setting("{$this->text_domain}-extension-{$this->slug}", "QAWC_EXTENSION_{$this->slug}");
	}

	public function admin_menu() {
		$page_parent = 'qqworld-woocommerce-assistant';
		$page_title = $this->label;
		$menu_title = $this->label;
		$capability = 'administrator';
		$menu_slug = "qqworld-woocommerce-assistant-{$this->slug}";
		$function = array($this, 'page');
		$settings_page = add_submenu_page($page_parent, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	private function includes() {
		include_once(QAFW_DIR . 'extensions' . DIRECTORY_SEPARATOR . 'brand-taxonomy.php');
		include_once(QAFW_DIR . 'extensions' . DIRECTORY_SEPARATOR . 'shipping-status.php');
		include_once(QAFW_DIR . 'extensions' . DIRECTORY_SEPARATOR . 'chinization.php');
		include_once(QAFW_DIR . 'extensions' . DIRECTORY_SEPARATOR . 'logistics-query.php');
	}

	protected function is_activated() {
		$activation_code = isset($this->options->extension[$this->slug]['activation-code']) ? $this->options->extension[$this->slug]['activation-code'] : '';
		return $this->check_activation_code($activation_code, $this->product);
	}

	public function form() {
		if (empty($this->extensions)) return;
		echo '<ul id="extension-list">';
		foreach ($this->extensions as $extension) :
?>
			<li class="extension <?php echo $extension->free ? 'free' : 'commercial'?>">
				<?php if ($extension->free) :?><a title="<?php _e('Edit'); ?>" class="options" target="_blank" href="<?php echo admin_url("admin.php?page=qqworld-woocommerce-assistant-{$extension->slug}"); ?>"></a><?php endif; ?>
				<?php if ($extension->free) : ?>
					<aside class="attr free"><?php _ex('Free', 'extension', $this->text_domain); ?></aside>
				<?php else: ?>
					<aside class="attr pay"><a href="https://www.qqworld.org/product/qqworld-woocommerce-assistant" target="_blank"><?php _ex('$ Buy', 'extension', $this->text_domain); ?></a></aside>
				<?php endif; ?>
				<figure class="extension-image" title="<?php echo $extension->label; ?>"><img src="<?php echo $extension->image; ?>" /></figure>
				<h3 class="extension-label"><?php echo $extension->label; ?></h3>
				<p class="extension-description"><?php echo $extension->description; ?></p>
			</li>
<?php
		endforeach;
		echo '</ul>';
	}
}
?>