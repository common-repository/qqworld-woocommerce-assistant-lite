<?php
namespace qqworld\extension;
use qqworld\core\extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class logistics_query extends extension {
	var $free = 0;
	var $slug = 'logistics-query';

	public function init() {
		add_filter( 'qqworld-woocommerce-assistant-extensions', array($this, 'register') );
	}

	public function register($extensions) {
		$this->label = _x('Logistics Query', 'extension', $this->text_domain);
		$this->description = _x("Query the logistics in customer's orders infomation.", 'extension', $this->text_domain);
		$this->image = QAFW_URL . "images/extensions/{$this->slug}.png";
		$extensions[] = $this;
		return $extensions;
	}
}
$logistics_query = new logistics_query();
$logistics_query->init();