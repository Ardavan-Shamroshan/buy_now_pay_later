<?php

use Inc\Api\Callbacks\GatewayCallbacks;
use Inc\Api\Callbacks\OptionsPaymentGatewayCallback;

function init_themedoni_buy_now_pay_later()
{

	add_filter('woocommerce_payment_gateways', 'WC_Add_Themedoni_Buy_Now_Pay_Later', 10, 1);

	function WC_Add_Themedoni_Buy_Now_Pay_Later($methods)
	{

		$methods[] = 'WC_Gateway_Themedoni_Buy_Now_Pay_Later';

		return $methods;
	}

	/**
	 * Set a minimum order amount for checkout
	 */
	add_action('woocommerce_checkout_process', 'wc_minimum_order_amount');
	add_action('woocommerce_before_cart', 'wc_minimum_order_amount');

	function wc_minimum_order_amount()
	{
		// Set this variable to specify a minimum order value
		$minimum = get_option('woocommerce_WC_Gateway_Themedoni_Buy_Now_Pay_Later_settings')['min_purchase'] ?? 0;


		if (WC()->cart->total < $minimum) {

			if (is_cart()) {

				wc_print_notice(
					sprintf(
						'مبلغ سبد خرید شما %s تومان است - برای استفاده از این درگاه پرداخت باید حداقل %s تومان در سبد خرید شما باشد.',
						wc_price(WC()->cart->total),
						wc_price($minimum)
					),
					'error'
				);
			} else {
				wc_add_notice(
					sprintf(
						'مبلغ سبد خرید شما %s تومان است - برای استفاده از این درگاه پرداخت باید حداقل %s تومان در سبد خرید شما باشد.',
						wc_price(WC()->cart->total),
						wc_price($minimum)
					),
					'error'
				);
			}
		}
	}

	class WC_Gateway_Themedoni_Buy_Now_Pay_Later extends WC_Payment_Gateway
	{
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

		public function __construct()
		{
			$this->gateway_callbacks                 = new GatewayCallbacks;
			$this->options_payment_gateway_callbacks = new OptionsPaymentGatewayCallback();

			$this->id                 = 'WC_Gateway_Themedoni_Buy_Now_Pay_Later';
			$this->method_title       = __('پرداخت با چک پیشرفته');
			$this->method_description = __('تنظیمات درگاه پرداخت با چک پیشرفته برای افزونه فروشگاه ساز ووکامرس');
			$this->icon               = apply_filters('WC_Gateway_Themedoni_Buy_Now_Pay_Later_logo', BNPL_URL . '/assets/images/cheque_32.png', __FILE__);
			$this->has_fields         = true;

			// These are options you’ll show in admin on your gateway settings page and make use of the WC Settings API.
			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->settings['title'];
			$this->description = $this->settings['description'];

			$this->rules = get_option('themedoni_buy_now_pay_later_rules');

			$this->cheque_conditions = get_option(
				'themedoni_buy_now_pay_later_cheque_conditions',
				[
					[
						'condition_name'       => $this->get_option('condition_name'),
						'prepayment'           => $this->get_option('prepayment'),
						'installments'         => $this->get_option('installments'),
						'term_of_installments' => $this->get_option('term_of_installments'),
						'commission_rate'      => $this->get_option('commission_rate'),
					],
				]
			);

			$this->extra_fields = get_option(
				'themedoni_buy_now_pay_later_extra_fields',
				[
					[
						'field_name' => $this->get_option('field_name'),
						'field_id'   => $this->get_option('field_id'),
						'field_type' => $this->get_option('field_type'),
					],
				]
			);

			// if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) )
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this->options_payment_gateway_callbacks, 'save_rules']);
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this->options_payment_gateway_callbacks, 'save_cheque_conditions']);
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this->options_payment_gateway_callbacks, 'save_extra_fields']);

			// payment process
			add_action('woocommerce_receipt_' . $this->id, [$this, 'redirect_to_cheque_payment_page']);
		}

		public function init_form_fields()
		{
			$this->form_fields = apply_filters(
				'WC_Gateway_Themedoni_Buy_Now_Pay_Later_Config',
				[
					'enabled'           => [
						'title'       => __('فعالسازی/غیرفعالسازی'),
						'type'        => 'checkbox',
						'label'       => __('فعالسازی درگاه پرداخت اقساطی با چک پیشرفته'),
						'description' => __('برای فعالسازی درگاه پرداخت اقساطی با چک پیشرفته باید این قسمت را  را علامتگذاری کنید.'),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'title'             => [
						'title'       => __('عنوان درگاه'),
						'type'        => 'text',
						'description' => __('عنوان درگاه که در طول خرید به مشتری نمایش داده می‌شود'),
						'default'     => __(' درگاه پرداخت با چک'),
						'desc_tip'    => true,
					],
					'description'       => [
						'title'       => __('توضیحات درگاه'),
						'type'        => 'text',
						'desc_tip'    => true,
						'description' => __('توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد'),
						'default'     => __('پرداخت اقساطی با چک صیادی')
					],
					'cheque_confirm'    => [
						'title'       => __('تایید به نام کردن چک'),
						'type'        => 'checkbox',
						'label'       => __('فعال سازی فیلد تایید'),
						'description' => __('یک فیلد تایید ثبت چک ها در زیر فیلد آپلود چک ها اضافه می کند. این گزینه برای اطمینان از'),
						'default'     => 'yes',
						'desc_tip'    => true,
					],
					'min_purchase'      => [
						'title'       => __('حداقل مبلغ سبد خرید'),
						'type'        => 'number',
						'desc_tip'    => true,
						'description' => __('در صورتی که مبلغ سبد خرید مشتری از این مبلغ کمتر باشد، درگاه پرداخت با چک در آن سفارش نمایش داده نخواهد شد. این گز ینه کمک می کند تا برای مبالغ کم، امکان پر داخت چکی را غیرفعال کنید .'),
						'placeholder' => 'تومان',
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
						'title'       => __('پیام ثبت موفقیت:'),
						'type'        => 'textarea',
						'description' => __('متن پیامی که میخواهید بعد از ثبت موفق به کاربر نمایش دهید را وارد کنید'),
					]
				]
			);
		}

		public function generate_rules_html()
		{
			ob_start();
?>

			<tr>
				<th scope="row" class="titledesc"><?php esc_html_e('قوانین:', 'themedoni'); ?></th>
				<td class="forminp">
					<div class="wc_input_table_wrapper">
						<?php
						$content = html_entity_decode($this->rules) ?? '';
						wp_editor($content, 'themedoni_bnpl_rules', [
							'textarea_name' => 'themedoni_bnpl_rules',
							'textarea_rows' => 10
						]);
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
		public function generate_cheque_conditions_html()
		{
			ob_start();

		?>
			<tr>
				<th scope="row" class="titledesc"><?php esc_html_e('شرایط چک ها:', 'themedoni'); ?></th>
				<td class="forminp" id="themedoni_bnpl_cheque_condition">
					<div class="wc_input_table_wrapper">
						<table class="widefat wc_input_table sortable" cellspacing="0">
							<thead>
								<tr>
									<th class="sort">&nbsp;</th>
									<th><?php esc_html_e('نام شرط', 'themedoni'); ?></th>
									<th><?php esc_html_e('پیش پرداخت (درصد)', 'themedoni'); ?></th>
									<th><?php esc_html_e('اقساط', 'themedoni'); ?></th>
									<th><?php esc_html_e('مدت اقساط (به روز)', 'themedoni'); ?></th>
									<th><?php esc_html_e('نرخ کارمزد (درصد)', 'themedoni'); ?></th>
								</tr>
							</thead>
							<tbody class="accounts">
								<?php
								$i = -1;
								if ($this->cheque_conditions) {
									foreach ($this->cheque_conditions as $condition) {
										$i++;

										echo '<tr class="account">
                                            <td class="sort"></td>
                                            <td><input type="text" value="' . esc_attr($condition['condition_name']) . '" name="themedoni_bnpl_condition_name[' . esc_attr($i) . ']" placeholder="سه ماهه" /></td>
                                            <td><input type="number" value="' . esc_attr($condition['prepayment']) . '" name="themedoni_bnpl_prepayment[' . esc_attr($i) . ']" placeholder="10" /></td>
                                            <td><input type="number" value="' . esc_attr($condition['installments']) . '" name="themedoni_bnpl_installments[' . esc_attr($i) . ']" placeholder="1" /></td>
                                            <td><input type="number" value="' . esc_attr($condition['term_of_installments']) . '" name="themedoni_bnpl_term_of_installments[' . esc_attr($i) . ']" placeholder="90" /></td>
                                            <td><input type="number" value="' . esc_attr($condition['commission_rate']) . '" name="themedoni_bnpl_commission_rate[' . esc_attr($i) . ']" placeholder="12" /></td>
                                        </tr>';
									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="7"><a href="#" class="add button"><?php esc_html_e('+ افزودن شرایط', 'themedoni'); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e('حذف شرایط انتخابی', 'themedoni'); ?></a></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<script type="text/javascript">
						jQuery(function() {
							jQuery('#themedoni_bnpl_cheque_condition').on('click', 'a.add', function() {

								var size = jQuery('#themedoni_bnpl_cheque_condition').find('tbody .account').length;

								jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="themedoni_bnpl_condition_name[' + size + ']" placeholder="سه ماهه"/></td>\
									<td><input type="number" name="themedoni_bnpl_prepayment[' + size + ']" placeholder="10"/></td>\
									<td><input type="number" name="themedoni_bnpl_installments[' + size + ']" placeholder="1"/></td>\
									<td><input type="number" name="themedoni_bnpl_term_of_installments[' + size + ']" placeholder="90"/></td>\
									<td><input type="number" name="themedoni_bnpl_commission_rate[' + size + ']" placeholder="12"/></td>\
								</tr>').appendTo('#themedoni_bnpl_cheque_condition table tbody');

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
		public function generate_extra_fields_html()
		{

			ob_start();

		?>
			<tr>
				<th scope="row" class="titledesc"><?php esc_html_e('فیلدهای اضافی:', 'themedoni'); ?></th>
				<td class="forminp" id="themedoni_bnpl_extra_fields">
					<div class="wc_input_table_wrapper">
						<table class="widefat wc_input_table sortable">
							<thead>
								<tr>
									<th class="sort">&nbsp;</th>
									<th><?php esc_html_e('نام فیلد', 'themedoni'); ?></th>
									<th><?php esc_html_e('آیدی فیلد', 'themedoni'); ?></th>
									<th><?php esc_html_e('نوع فیلد', 'themedoni'); ?></th>
								</tr>
							</thead>
							<tbody class="accounts">

								<?php
								$i = -1;
								if ($this->extra_fields) {
									foreach ($this->extra_fields as $field) {
										$i++;

								?>
										<tr class="account">
											<td class="sort"></td>
											<td><input type="text" value="<?= esc_attr($field['field_name']) ?>" name="themedoni_bnpl_field_name['<?= esc_attr($i) ?>']" placeholder="کدملی" /></td>
											<td><input type="text" value="<?= esc_attr($field['field_id']) ?>" name="themedoni_bnpl_field_id['<?= esc_attr($i) ?>']" placeholder="national_code" /></td>
											<td>
												<select name="themedoni_bnpl_field_type['<?= esc_attr($i) ?>']">
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
									<th colspan="7"><a href="#" class="add button"><?php esc_html_e('+ افزودن فیلد', 'themedoni'); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e('حذف فیلدهای انتخابی', 'themedoni'); ?></a></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<script type="text/javascript">
						jQuery(function() {
							jQuery('#themedoni_bnpl_extra_fields').on('click', 'a.add', function() {

								var size = jQuery('#themedoni_bnpl_extra_fields').find('tbody .account').length;

								jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="themedoni_bnpl_field_name[' + size + ']" placeholder="کدملی"/></td>\
									<td><input type="text" name="themedoni_bnpl_field_id[' + size + ']" placeholder="national_code"/></td>\
									<td><select name="themedoni_bnpl_field_type[' + size + ']"><option value="text">متنی</option><option value="number">عددی</option><option value="file">آپلود فایل</option></td></select>\
								</tr>').appendTo('#themedoni_bnpl_extra_fields table tbody');

								return false;
							});
						});
					</script>
				</td>
			</tr>
<?php
			return ob_get_clean();
		}

		public function redirect_to_cheque_payment_page($order_id)
		{
			$order = new WC_Order($order_id);
			if (isset($_POST['themedoni_bnpl_submit'])) {
				$this->gateway_callbacks->redirect_to_cheque_payment_page($order_id, $this->extra_fields);
				wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
			}

			extract([$this->rules, $this->cheque_conditions, $this->extra_fields]);
			include_once BNPL_PATH . 'templates/gateways/cheque-payment-page.php';
		}


		public function process_payment($order_id)
		{
			$order = new WC_Order($order_id);

			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url(true)
			);
		}
	}
}
