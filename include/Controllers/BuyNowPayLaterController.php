<?php

namespace Inc\Controllers;

use Inc\Api\WoocommerceSettings;

class BuyNowPayLaterController extends BaseController {
    public WoocommerceSettings $settings;

    public function register() {
        $this->settings      = new WoocommerceSettings();
        $this->settings->register();

    }

}