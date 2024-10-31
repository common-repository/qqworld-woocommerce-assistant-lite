<?php
namespace qqworld\extension;
use qqworld\core\extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class chinization extends extension {
	var $free = 0;
	var $slug = 'chinization';

	public function init() {
		add_filter( 'qqworld-woocommerce-assistant-extensions', array($this, 'register') );
	}

	public function register($extensions) {
		$this->label = _x('Chinization', 'extension', $this->text_domain);
		$this->description = _x('Woocommerce address is not in conformity with the Chinese habit, this feature can adjust the items of address.', 'extension', $this->text_domain);
		$this->image = QAFW_URL . "images/extensions/{$this->slug}.png";
		$extensions[] = $this;
		return $extensions;
	}
}
$chinization = new chinization();
$chinization->init();