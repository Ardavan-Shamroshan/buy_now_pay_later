<?php

namespace Inc\Api\Callbacks;

use Inc\Controllers\BaseController;

class TestimonialCallbacks extends BaseController {
	public function shortcode_page() {
		return require_once $this->plugin_path . 'templates/testimonial-shortcode.php';
	}
}