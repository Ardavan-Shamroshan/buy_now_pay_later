<?php

namespace Inc\Api\Callbacks;

class OptionsPaymentGatewayCallback {
	public function save_rules() {
		$rules = '';

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if ( isset( $_POST['bnpl_rules'] ) ) {
			$rules = htmlentities( wpautop( $_POST['bnpl_rules'] ) );
		}
		// phpcs:enable

		do_action( 'woocommerce_update_option', [ 'id' => 'buy_now_pay_later_rules' ] );
		update_option( 'buy_now_pay_later_rules', $rules );
	}


	/**
	 * Save cheque conditions table.
	 */
	public function save_cheque_conditions() {

		$conditions = [];

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if (
			isset( $_POST['bnpl_condition_name'] ) &&
			isset( $_POST['bnpl_prepayment'] ) &&
			isset( $_POST['bnpl_installments'] ) &&
			isset( $_POST['bnpl_term_of_installments'] ) &&
			isset( $_POST['bnpl_commission_rate'] )
		) {

			$condition_names      = wc_clean( wp_unslash( $_POST['bnpl_condition_name'] ) );
			$prepayments          = wc_clean( wp_unslash( $_POST['bnpl_prepayment'] ) );
			$installments         = wc_clean( wp_unslash( $_POST['bnpl_installments'] ) );
			$term_of_installments = wc_clean( wp_unslash( $_POST['bnpl_term_of_installments'] ) );
			$commission_rates     = wc_clean( wp_unslash( $_POST['bnpl_commission_rate'] ) );

			foreach ( $condition_names as $i => $name ) {
				if ( ! isset( $condition_names[ $i ] ) ) {
					continue;
				}

				$conditions[] = [
					'condition_name'       => $condition_names[ $i ],
					'prepayment'           => $prepayments[ $i ],
					'installments'         => $installments[ $i ],
					'term_of_installments' => $term_of_installments[ $i ],
					'commission_rate'      => $commission_rates[ $i ],
				];
			}
		}
		// phpcs:enable

		do_action( 'woocommerce_update_option', [ 'id' => 'buy_now_pay_later_cheque_conditions' ] );
		update_option( 'buy_now_pay_later_cheque_conditions', $conditions );
	}


	/**
	 * Save cheque conditions table.
	 */
	public function save_extra_fields() {

		$fields = [];

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if (
			isset( $_POST['bnpl_field_name'] ) &&
			isset( $_POST['bnpl_field_id'] ) &&
			isset( $_POST['bnpl_field_type'] )
		) {

			$fields_names = wc_clean( wp_unslash( $_POST['bnpl_field_name'] ) );
			$fields_ids   = wc_clean( wp_unslash( $_POST['bnpl_field_id'] ) );
			$fields_types = wc_clean( wp_unslash( $_POST['bnpl_field_type'] ) );

			foreach ( $fields_names as $i => $name ) {
				if ( ! isset( $fields_names[ $i ] ) ) {
					continue;
				}

				$fields[] = [
					'field_name' => $fields_names[ $i ],
					'field_id'   => $fields_ids[ $i ],
					'field_type' => $fields_types[ $i ],
				];
			}
		}
		// phpcs:enable

		do_action( 'woocommerce_update_option', [ 'id' => 'buy_now_pay_later_extra_fields' ] );
		update_option( 'buy_now_pay_later_extra_fields', $fields );
	}
}