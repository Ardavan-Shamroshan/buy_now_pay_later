<?php

function init_themedoni_buy_now_pay_later() {
	add_filter( 'woocommerce_payment_gateways', 'WC_Add_Themedoni_Buy_Now_Pay_Later' );

	function WC_Add_Themedoni_Buy_Now_Pay_Later( $methods ) {
		$methods[] = 'WC_Gateway_Themedoni_Buy_Now_Pay_Later';

		return $methods;
	}


	class WC_Gateway_Themedoni_Buy_Now_Pay_Later extends WC_Payment_Gateway {
		public function __construct() {
			$this->id                 = 'WC_Gateway_Themedoni_Buy_Now_Pay_Later';
			$this->method_title       = __( 'پرداخت با چک پیشرفته' );
			$this->method_description = __( 'پرداخت اقساطی با چک صیادی' );
			$this->icon               = apply_filters( 'WC_Gateway_Themedoni_Buy_Now_Pay_Later_logo', plugins_url( '/assets/images/logo.png', __FILE__ ) );
			$this->has_fields         = true;

			// These are options you’ll show in admin on your gateway settings page and make use of the WC Settings API.
			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->settings['title'];
			$this->description = $this->settings['description'];

			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			} else {
				add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
			}
		}

		public function init_form_fields() {
			$this->form_fields = apply_filters( 'WC_Gateway_Themedoni_Buy_Now_Pay_Later_Config', [
					'enabled'        => [
						'title'       => __( 'فعالسازی/غیرفعالسازی' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعالسازی درگاه پرداخت اقساطی با چک پیشرفته' ),
						'description' => __( 'برای فعالسازی درگاه پرداخت اقساطی با چک پیشرفته باید این قسمت را  را علامتگذاری کنید.' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'title'          => [
						'title'       => __( 'عنوان درگاه' ),
						'type'        => 'text',
						'description' => __( 'عنوان درگاه که در طول خرید به مشتری نمایش داده می‌شود' ),
						'default'     => __( ' درگاه پرداخت با چک' ),
						'desc_tip'    => true,
					],
					'description'    => [
						'title'       => __( 'توضیحات درگاه' ),
						'type'        => 'text',
						'desc_tip'    => true,
						'description' => __( 'توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد' ),
						'default'     => __( 'پرداخت اقساطی با چک صیادی' )
					],
					'cheque_confirm' => [
						'title'       => __( 'تایید به نام کردن چک ها' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعال سازی فیلد تایید' ),
						'description' => __( 'تایید به نام کردن چک ها' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'min_purchase'   => [
						'title'       => __( 'حداقل مبلغ سبد خرید' ),
						'type'        => 'number',
						'desc_tip'    => true,
						'description' => __( 'حداقل مبلغ سبد خرید را مشخص کنید' ),
						'default'     => 0
					],
					'rules'          => [
						'title' => __( 'قوانین:' ),
						'type'  => 'textarea',
					],
				]
			);

		}
	}
}

add_action( 'plugins_loaded', 'init_themedoni_buy_now_pay_later' );