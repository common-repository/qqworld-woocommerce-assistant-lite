<?php
namespace qqworld\core;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class options {
	var $extension;
	var $extension_brands_enabled;

	var $payment;
	var $payment_gateways;

	public function __construct() {
		$this->extension = get_option('QAWC_EXTENSION', array());

		$this->extension_brands_taxonomy = get_option('QAWC_EXTENSION_brand-taxonomy', array());
		$this->extension_brands_taxonomy_enabled = isset($this->extension_brands_taxonomy['enabled']) ? $this->extension_brands_taxonomy['enabled'] : 0;

		$this->extension_shipping_status = get_option('QAWC_EXTENSION_shipping-status', array());
		$this->extension_shipping_status_enabled = isset($this->extension_shipping_status['enabled']) ? $this->extension_shipping_status['enabled'] : 0;

		$this->extension_chinization = get_option('QAWC_EXTENSION_chinization', array());
		$this->extension_chinization_address_habit = isset($this->extension_chinization['address_habit']) ? $this->extension_chinization['address_habit'] : 0;
	}
}
