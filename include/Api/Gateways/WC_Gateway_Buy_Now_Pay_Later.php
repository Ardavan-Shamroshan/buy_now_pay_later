<?php

use Inc\Api\Callbacks\GatewayCallbacks;
use Inc\Api\Callbacks\OptionsPaymentGatewayCallback;

function init_buy_now_pay_later() {
	add_filter( 'woocommerce_payment_gateways', 'WC_Add_Buy_Now_Pay_Later', 10, 1 );

	function WC_Add_Buy_Now_Pay_Later( $methods ) {

		$methods[] = 'WC_Gateway_Buy_Now_Pay_Later';

		return $methods;
	}

	add_filter( 'woocommerce_currencies', 'woo_behpardakht_IR_currency' );

	function woo_behpardakht_IR_currency( $currencies ) {
		$currencies['IRR']  = __( 'ریال' );
		$currencies['IRT']  = __( 'تومان' );
		$currencies['IRHR'] = __( 'هزار ریال' );
		$currencies['IRHT'] = __( 'هزار تومان' );

		return $currencies;
	}

	add_filter( 'woocommerce_available_payment_gateways', 'bbloomer_paypal_disable_manager' );

	function bbloomer_paypal_disable_manager( $available_gateways ) {
		if ( isset( $available_gateways['WC_Gateway_Buy_Now_Pay_Later'] ) && !is_user_logged_in() ) {
			unset( $available_gateways['WC_Gateway_Buy_Now_Pay_Later'] );
		}
		return $available_gateways;
	}

	/**
	 * Set a minimum order amount for checkout
	 */
	add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );

	function wc_minimum_order_amount() {
		// Set this variable to specify a minimum order value
		$minimum = get_option( 'woocommerce_WC_Gateway_Buy_Now_Pay_Later_settings' )['min_purchase'] ?? 0;

		if ( WC()->cart->total < $minimum ) {
			if ( is_cart() ) {
				wc_print_notice(
					sprintf(
						'مبلغ سبد خرید شما %s تومان است - برای استفاده از این درگاه پرداخت باید حداقل %s تومان در سبد خرید شما باشد.',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);
			} else {
				wc_add_notice(
					sprintf(
						'مبلغ سبد خرید شما %s تومان است - برای استفاده از این درگاه پرداخت باید حداقل %s تومان در سبد خرید شما باشد.',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);
			}
		}
	}

	class WC_Gateway_Buy_Now_Pay_Later extends WC_Payment_Gateway {
		public $gateway_callbacks;
		public $options_payment_gateway_callbacks;
		public $rules;

		/**
		 * 'condition_name'       => نام شرط,
		 * 'prepayment'           => پیش پرداخت,
		 * 'installments'         => اقساط,
		 * 'term_of_installments' => مدت اقساط,
		 * 'commission_rate'      => نرخ کارمزد
		 *
		 * @var false|mixed|null
		 */
		public $cheque_conditions;

		/**
		 * 'field_name'         => نام فیلد,
		 * 'field_id'           => آیدی فیلد,
		 * 'field_type'         => نوع فیلد,
		 *
		 * @var false|mixed|null
		 */
		public $extra_fields;

		public function __construct() {
			$this->gateway_callbacks                 = new GatewayCallbacks;
			$this->options_payment_gateway_callbacks = new OptionsPaymentGatewayCallback();

			$this->id                 = 'WC_Gateway_Buy_Now_Pay_Later';
			$this->method_title       = __( 'پرداخت با چک پیشرفته' );
			$this->method_description = __( 'تنظیمات درگاه پرداخت با چک پیشرفته برای افزونه فروشگاه ساز ووکامرس' );
			$this->icon               = apply_filters( 'WC_Gateway_Buy_Now_Pay_Later_logo', BNPL_URL . '/assets/images/cheque_32.png', __FILE__ );
			$this->has_fields         = true;

			// These are options you’ll show in admin on your gateway settings page and make use of the WC Settings API.
			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->settings['title'];
			$this->description = $this->settings['description'];

			$this->rules = get_option( 'buy_now_pay_later_rules' );

			$this->cheque_conditions = get_option(
				'buy_now_pay_later_cheque_conditions',
				[
					[
						'condition_name'       => $this->get_option( 'condition_name' ),
						'prepayment'           => $this->get_option( 'prepayment' ),
						'installments'         => $this->get_option( 'installments' ),
						'term_of_installments' => $this->get_option( 'term_of_installments' ),
						'commission_rate'      => $this->get_option( 'commission_rate' ),
					],
				]
			);

			$this->extra_fields = get_option(
				'buy_now_pay_later_extra_fields',
				[
					[
						'field_name' => $this->get_option( 'field_name' ),
						'field_id'   => $this->get_option( 'field_id' ),
						'field_type' => $this->get_option( 'field_type' ),
					],
				]
			);

			// if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) )
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this->options_payment_gateway_callbacks, 'save_rules' ] );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this->options_payment_gateway_callbacks, 'save_cheque_conditions' ] );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this->options_payment_gateway_callbacks, 'save_extra_fields' ] );

			// payment process
			add_action( 'woocommerce_receipt_' . $this->id, [ $this, 'redirect_to_cheque_payment_page' ] );
		}

		public function init_form_fields() {
			$this->form_fields = apply_filters(
				'WC_Gateway_Buy_Now_Pay_Later_Config',
				[
					'enabled'           => [
						'title'       => __( 'فعالسازی/غیرفعالسازی' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعالسازی درگاه پرداخت اقساطی با چک پیشرفته' ),
						'description' => __( 'برای فعالسازی درگاه پرداخت اقساطی با چک پیشرفته باید این قسمت را  را علامتگذاری کنید.' ),
						'default'     => 'no',
						'desc_tip'    => true,
					],
					'title'             => [
						'title'       => __( 'عنوان درگاه' ),
						'type'        => 'text',
						'description' => __( 'عنوان درگاه که در طول خرید به مشتری نمایش داده می‌شود' ),
						'default'     => __( ' درگاه پرداخت با چک' ),
						'desc_tip'    => true,
					],
					'description'       => [
						'title'       => __( 'توضیحات درگاه' ),
						'type'        => 'text',
						'desc_tip'    => true,
						'description' => __( 'توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد' ),
						'default'     => __( 'پرداخت اقساطی با چک صیادی' )
					],
					'cheque_confirm'    => [
						'title'       => __( 'تایید به نام کردن چک' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعال سازی فیلد تایید' ),
						'description' => __( 'یک فیلد تایید ثبت چک ها در زیر فیلد آپلود چک ها اضافه می کند. این گزینه برای اطمینان از' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'calculator'        => [
						'title'       => __( 'فعال سازی ماشین حساب' ),
						'type'        => 'checkbox',
						'label'       => __( 'فعال سازی ماشین حساب در صفحه نمایش محصول' ),
						'description' => __( 'با فعال کردن این گزینه یک ماشین حساب به صفحه نمایش محصول اضافه میشود' ),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'min_purchase'      => [
						'title'       => __( 'حداقل مبلغ سبد خرید  (' . get_woocommerce_currencies()[ get_woocommerce_currency() ] . ')' ),
						'type'        => 'number',
						'desc_tip'    => true,
						'description' => __( 'در صورتی که مبلغ سبد خرید مشتری از این مبلغ با واحد ' . get_woocommerce_currencies()[ get_woocommerce_currency() ] . ' کمتر باشد، درگاه پرداخت با چک در آن سفارش نمایش داده نخواهد شد. این گزینه کمک می کند تا برای مبالغ کم، امکان پر داخت چکی را غیرفعال کنید .' ),
						'placeholder' => get_woocommerce_currencies()[ get_woocommerce_currency() ],
						'default'     => 0
					],
					'rules'             => [
						'type' => 'rules',
					],
					'cheque_conditions' => [
						'type' => 'cheque_conditions',
					],
					'extra_fields'      => [
						'type' => 'extra_fields',
					],
					'success_message'   => [
						'title'       => __( 'پیام ثبت موفقیت:' ),
						'type'        => 'textarea',
						'description' => __( 'متن پیامی که میخواهید بعد از ثبت موفق به کاربر نمایش دهید را وارد کنید' ),
					]
				]
			);
		}

		public function generate_rules_html() {
			ob_start();
			?>

            <tr>
                <th scope="row" class="titledesc"><?php esc_html_e( 'قوانین:', 'bnpl' ); ?></th>
                <td class="forminp">
                    <div class="wc_input_table_wrapper">
						<?php
						$content = html_entity_decode( $this->rules ) ?? '';
						wp_editor( $content, 'bnpl_rules', [
							'textarea_name' => 'bnpl_rules',
							'textarea_rows' => 10
						] );
						?>
                    </div>
                </td>
            </tr>

			<?php
			return ob_get_clean();
		}


		/**
		 * Generate cheque conditions html.
		 *
		 * @return string
		 */
		public function generate_cheque_conditions_html() {
			ob_start();

			?>
            <tr>
                <th scope="row" class="titledesc"><?php esc_html_e( 'شرایط چک ها:', 'bnpl' ); ?></th>
                <td class="forminp" id="bnpl_cheque_condition">
                    <div class="wc_input_table_wrapper">
                        <table class="widefat wc_input_table sortable" cellspacing="0">
                            <thead>
                            <tr>
                                <th class="sort">&nbsp;</th>
                                <th><?php esc_html_e( 'نام شرط', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'پیش پرداخت (درصد)', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'اقساط', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'مدت اقساط (به روز)', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'نرخ کارمزد (درصد)', 'bnpl' ); ?></th>
                            </tr>
                            </thead>
                            <tbody class="accounts">
							<?php
							$i = - 1;
							if ( $this->cheque_conditions ) {
								foreach ( $this->cheque_conditions as $condition ) {
									$i ++;

									echo '<tr class="account">
                                            <td class="sort"></td>
                                            <td><input type="text" value="' . esc_attr( $condition['condition_name'] ) . '" name="bnpl_condition_name[' . esc_attr( $i ) . ']" placeholder="سه ماهه" /></td>
                                            <td><input type="number" value="' . esc_attr( $condition['prepayment'] ) . '" name="bnpl_prepayment[' . esc_attr( $i ) . ']" placeholder="10" /></td>
                                            <td><input type="number" value="' . esc_attr( $condition['installments'] ) . '" name="bnpl_installments[' . esc_attr( $i ) . ']" placeholder="1" /></td>
                                            <td><input type="number" value="' . esc_attr( $condition['term_of_installments'] ) . '" name="bnpl_term_of_installments[' . esc_attr( $i ) . ']" placeholder="90" /></td>
                                            <td><input type="number" value="' . esc_attr( $condition['commission_rate'] ) . '" name="bnpl_commission_rate[' . esc_attr( $i ) . ']" placeholder="12" /></td>
                                        </tr>';
								}
							}
							?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="7"><a href="#" class="add button"><?php esc_html_e( '+ افزودن شرایط', 'bnpl' ); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e( 'حذف شرایط انتخابی', 'bnpl' ); ?></a></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#bnpl_cheque_condition').on('click', 'a.add', function () {

                                var size = jQuery('#bnpl_cheque_condition').find('tbody .account').length;

                                jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bnpl_condition_name[' + size + ']" placeholder="سه ماهه"/></td>\
									<td><input type="number" name="bnpl_prepayment[' + size + ']" placeholder="10"/></td>\
									<td><input type="number" name="bnpl_installments[' + size + ']" placeholder="1"/></td>\
									<td><input type="number" name="bnpl_term_of_installments[' + size + ']" placeholder="90"/></td>\
									<td><input type="number" name="bnpl_commission_rate[' + size + ']" placeholder="12"/></td>\
								</tr>').appendTo('#bnpl_cheque_condition table tbody');

                                return false;
                            });
                        });
                    </script>
                </td>
            </tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Generate cheque conditions html.
		 *
		 * @return string
		 */
		public function generate_extra_fields_html() {

			ob_start();

			?>
            <tr>
                <th scope="row" class="titledesc"><?php esc_html_e( 'فیلدهای اضافی:', 'bnpl' ); ?></th>
                <td class="forminp" id="bnpl_extra_fields">
                    <div class="wc_input_table_wrapper">
                        <table class="widefat wc_input_table sortable">
                            <thead>
                            <tr>
                                <th class="sort">&nbsp;</th>
                                <th><?php esc_html_e( 'نام فیلد', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'آیدی فیلد', 'bnpl' ); ?></th>
                                <th><?php esc_html_e( 'نوع فیلد', 'bnpl' ); ?></th>
                            </tr>
                            </thead>
                            <tbody class="accounts">

							<?php
							$i = - 1;
							if ( $this->extra_fields ) {
								foreach ( $this->extra_fields as $field ) {
									$i ++;

									?>
                                    <tr class="account">
                                        <td class="sort"></td>
                                        <td><input type="text" value="<?= esc_attr( $field['field_name'] ) ?>" name="bnpl_field_name['<?= esc_attr( $i ) ?>']" placeholder="کدملی"/></td>
                                        <td><input type="text" value="<?= esc_attr( $field['field_id'] ) ?>" name="bnpl_field_id['<?= esc_attr( $i ) ?>']" placeholder="national_code"/></td>
                                        <td>
                                            <select name="bnpl_field_type['<?= esc_attr( $i ) ?>']">
                                                <option value="text" <?= $field['field_type'] == 'text' ? 'selected' : '' ?>>متنی</option>
                                                <option value="number" <?= $field['field_type'] == 'number' ? 'selected' : '' ?>>عددی</option>
                                                <option value="file" <?= $field['field_type'] == 'file' ? 'selected' : '' ?>>آپلود فایل</option>
                                            </select>
                                        </td>
                                    </tr>
									<?php
								}
							}
							?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="7"><a href="#" class="add button"><?php esc_html_e( '+ افزودن فیلد', 'bnpl' ); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e( 'حذف فیلدهای انتخابی', 'bnpl' ); ?></a></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#bnpl_extra_fields').on('click', 'a.add', function () {

                                var size = jQuery('#bnpl_extra_fields').find('tbody .account').length;

                                jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bnpl_field_name[' + size + ']" placeholder="کدملی"/></td>\
									<td><input type="text" name="bnpl_field_id[' + size + ']" placeholder="national_code"/></td>\
									<td><select name="bnpl_field_type[' + size + ']"><option value="text">متنی</option><option value="number">عددی</option><option value="file">آپلود فایل</option></td></select>\
								</tr>').appendTo('#bnpl_extra_fields table tbody');

                                return false;
                            });
                        });
                    </script>
                </td>
            </tr>
			<?php
			return ob_get_clean();
		}

		public function redirect_to_cheque_payment_page( $order_id ) {
			$order = new WC_Order( $order_id );
			if ( isset( $_POST['bnpl_submit'] ) ) {
				$result = $this->gateway_callbacks->redirect_to_cheque_payment_page( $order_id, $this->extra_fields );
				if ( $result ) {
					wp_redirect( add_query_arg( 'wc_status', 'success', $this->get_return_url( $order ) ) );
				}
			}

			extract( [ $this->rules, $this->cheque_conditions, $this->extra_fields ] );
			include_once BNPL_PATH . 'templates/gateways/cheque-payment-page.php';
		}


		public function process_payment( $order_id ) {
			$order = new WC_Order( $order_id );

			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true )
			);
		}
	}
}
