<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bnplTailwindCss', BNPL_URL . '/assets/dist/output.css', [], null );
	wp_enqueue_script( 'chequePaymentScript', BNPL_URL . '/assets/cheque-payment.js', [ 'jquery' ], null );
} )

?>


<div>
    <p>قوانین:</p>
	<?= html_entity_decode( $this->rules ) ?>
    <div class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained">
        <div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="col2-set">
                    <div class="woocommerce-checkout-review-order">
                        <div>
                            <h3 class="text-rose-900">تصاویر چک</h3>
                            <div id="bnpl_installments_container"></div>
                            <div>
                                <h3>فیلد های ضروری</h3>

								<?php foreach ( $this->extra_fields as $field ): ?>
                                    <div>
                                        <label for="<?= $field['field_id'] ?>"><?= $field['field_name'] ?></label>
                                        <!-- $field['field_type'] -->
                                        <input type="text" name="<?= $field['field_id'] ?>" id="<?= $field['field_id'] ?>">
                                    </div>
								<?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <button name="themedoni_bnpl_submit" type="submit">ارسال</button>
                </div>
                <div class="col2-set">
                    <h3>نوع اقساط</h3>
                    <div class="woocommerce-checkout-review-order">
                        <table class="shop_table woocommerce-checkout-review-order-table">
                            <tbody>
                            <tr class="order-total">
                                <th>مجموع</th>
                                <td><strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"></span>58.99</bdi></span></strong></td>
                            </tr>
							<?php
							$i = - 1;
							if ( $this->cheque_conditions ) {
								foreach ( $this->cheque_conditions as $condition ) {
									$i ++;
									?>
                                    <tr>
                                        <td style="display: flex">
                                            <input type="radio" value="<?= esc_attr( $condition['condition_name'] ) ?>" name="themedoni_bnpl_order_condition_name" id="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]"/>
                                            <label for="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]"><?= esc_attr( $condition['condition_name'] ) ?></label>
                                        </td>
                                    </tr>
									<?php
								}
							}
							?>
                            </tbody>
                        </table>
                        <table class="shop_table woocommerce-checkout-review-order-table">
                            <tbody>
                            <tr>
                                <th>پیش پرداخت</th>
                                <td id="bnpl_prepayment">0</td>
                            </tr>
                            <tr>
                                <th>تعداد چک ها</th>
                                <td id="bnpl_installments">0</td>
                            </tr>
                            <tr>
                                <th>مبلغ چک ها</th>
                                <td id="bnpl_commission_rate">0</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="shop_table woocommerce-checkout-review-order-table">
                            <tbody>
                            <tr>
                                <th>مبلغ نهایی</th>
                                <td id="bnpl_final_price">0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>

