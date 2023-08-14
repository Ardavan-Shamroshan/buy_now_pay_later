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
					'base_config'     => [
						'title'       => __( 'تنظیمات درگاه' ),
						'type'        => 'title',
						'description' => '',
					],
					'enabled'         => [
						'title'       => __( 'فعالسازی/غیرفعالسازی' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعالسازی درگاه پرداخت به پرداخت ملت' ),
						'description' => __( 'برای فعالسازی درگاه به پرداخت ملت باید این قسمت را  را علامتگذاری کنید.' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'title'           => [
						'title'       => __( 'عنوان درگاه' ),
						'type'        => 'text',
						'description' => __( 'عنوان درگاه که در طول خرید به مشتری نمایش داده می‌شود' ),
						'default'     => __( 'به پرداخت ملت' ),
						'desc_tip'    => true,
					],
					'description'     => [
						'title'       => __( 'توضیحات درگاه' ),
						'type'        => 'text',
						'desc_tip'    => true,
						'description' => __( 'توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد' ),
						'default'     => __( 'پرداخت امن از طریق درگاه پرداخت به پرداخت ملت(قابل پرداخت با کلیه کارتهای عضو شتاب)' )
					],
					'account_config'  => [
						'title'       => __( 'اطلاعات درگاه پرداخت' ),
						'type'        => 'title',
						'description' => '',
					],
					'terminal_id'     => [
						'title'       => __( 'شماره ترمینال' ),
						'type'        => 'text',
						'description' => __( 'Terminal ID' ),
						'default'     => '',
						'desc_tip'    => true
					],
					'username'        => [
						'title'       => __( 'نام کاربری' ),
						'type'        => 'text',
						'description' => __( 'Username' ),
						'default'     => '',
						'desc_tip'    => true
					],
					'password'        => [
						'title'       => __( 'کلمه عبور' ),
						'type'        => 'text',
						'description' => __( 'Password' ),
						'default'     => '',
						'desc_tip'    => true
					],
					'payment_config'  => [
						'title'       => __( 'تنظیمات عملیات پرداخت' ),
						'type'        => 'title',
						'description' => '',
					],
					'order_pay_show'  => [
						'title'       => __( 'برگه پیش فاکتور' ),
						'type'        => 'checkbox',
						'label'       => __( 'نمایش برگه پیش فاکتور' ),
						'description' => __( 'برای نمایش برگه پیش فاکتور این قسمت را علامتگذاری کنید' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'success_massage' => [
						'title'       => __( 'پیام پرداخت موفق' ),
						'type'        => 'textarea',
						'description' => __( 'متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید.
                                            همچنین می توانید از کدهای کوتاه زیر استفاده کنید:<br/>
                                            <strong>%Transaction_id%</strong> : کد رهگیری<br/>
                                            <strong>%Order_Number%</strong> : شماره درخواست تراکنش<br/>' ),
						'default'     => __( 'پرداخت با موفقیت انجام شد.' ),
					],
					'failed_massage'  => [
						'title'       => __( 'پیام پرداخت ناموفق' ),
						'type'        => 'textarea',
						'description' => __( 'متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد %fault% برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت به پرداخت ملت ارسال میگردد .' ),
						'default'     => __( 'پرداخت با شکست مواجه شد. شرح خطا: %fault%' ),
					],
				]
			);

		}
	}
}

add_action( 'plugins_loaded', 'init_themedoni_buy_now_pay_later' );