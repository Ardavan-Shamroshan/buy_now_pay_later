<?php

namespace Inc\Base;

class Activate
{
	public static function activate()
	{
		flush_rewrite_rules();

		// if there was no option with the given key, update the key with an empty array
		if (!get_option('themedoni_buy_now_pay_later')) {
			update_option('themedoni_buy_now_pay_later', []);
		}
	}
}
