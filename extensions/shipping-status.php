<?php
namespace qqworld\extension;
use qqworld\core\extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class shipping extends extension {
	var $free = 1;
	var $slug = 'shipping-status';

	public function init() {
		add_filter( 'qqworld-woocommerce-assistant-extensions', array($this, 'register') );

		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_action( 'admin_init', array($this, 'register_settings') );

		if ($this->options->extension_shipping_status_enabled != 1) return;

		add_action( 'init', array($this, 'register_new_order_statuses') );
		add_filter( 'wc_order_statuses', array($this, 'wc_order_statuses') );
	}

	public function register($extensions) {
		$this->label = _x('Shipping Status', 'extension', $this->text_domain);
		$this->description = _x('Add the order status of shipping.', 'extension', $this->text_domain);
		$this->image = QAFW_URL . "images/extensions/{$this->slug}.png";
		$extensions[] = $this;
		return $extensions;
	}

	public function register_new_order_statuses() {
		register_post_status( 'wc-delivered', array(
			'label'                     => _x( 'Delivered', 'Order status', $this->text_domain ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', $this->text_domain )
		) );
	}

	function wc_order_statuses( $order_statuses ) {
		$order_statuses['wc-delivered'] = _x( 'Delivered', 'Order status', $this->text_domain );
		return $order_statuses;
	}

	public function page() {
?>
<div class="wrap" id="qqworld-woocommerce-assistant-container">
	<h2><?php echo $this->label; ?></h2>
	<form action="options.php" method="post" id="update-form">
		<?php settings_fields($this->text_domain.'-extension-'.$this->slug); ?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="extension-shipping-add-on"><?php _e('Shipping Status', $this->text_domain); ?></label>
						<span class="woocommerce-help-tip" data-header="<?php _e('Shipping Status', $this->text_domain); ?>" data-content="<?php _e('Add shipping status to product order status.', $this->text_domain); ?>"></span>
					</th>
					<td class="forminp">
						<label><input type="checkbox" id="extension-shipping-add-on" name="QAWC_EXTENSION_<?php echo $this->slug; ?>[enabled]" value="1" <?php checked($this->options->extension_shipping_status_enabled, 1);?> /> <?php _e('Enabled', $this->text_domain); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
	}
}
$shipping = new shipping();
$shipping->init();
?>