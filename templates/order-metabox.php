<div class="bnpl-cheque-info">

    <div class="cheque-main-info">
        <div>
            <h3>اطلاعات چک ها</h3>
            <ul class="cheque-rule">
                <li>
                    <span>نام شرط: </span>
                    <span><?= $order_cheque_condition['condition_name'] ?></span>
                </li>
                <li>
                    <span>پیش پرداخت: </span>
                    <span>

                    <span class="woocommerce-Price-amount amount"><?= priceFormat( $prepayment_price ) ?><bdi><span class="woocommerce-Price-currencySymbol"><?= get_woocommerce_currencies()[ get_woocommerce_currency() ] ?></span></bdi></span>
                    <span class="woocommerce-Price-amount amount"><?= discountFormat( $order_cheque_condition['prepayment'] ) ?> </span>
                </span>
                </li>
                <li>
                    <span>اقساط: </span>
                    <span><?= convertEnglishToPersian( $order_cheque_condition['installments'] ) ?></span>
                </li>
                <li>
                    <span>مبلغ چک ها:</span>
                    <span><span class="woocommerce-Price-amount amount"><bdi><?= priceFormat( $every_installment_price ) ?><span class="woocommerce-Price-currencySymbol"><?= get_woocommerce_currencies()[ get_woocommerce_currency() ] ?></span></bdi></span></span>
                </li>
                <li>
                    <span>مبلغ نهایی: </span>
                    <span><span class="woocommerce-Price-amount amount"><bdi><?= priceFormat( $final_price ) ?><span class="woocommerce-Price-currencySymbol"><?= get_woocommerce_currencies()[ get_woocommerce_currency() ] ?></span></bdi></span></span>
                </li>
            </ul>

			<?php
			$add   = $order_cheque_condition['term_of_installments'] / $order_cheque_condition['installments'];
			$dates = [];
			for ( $i = 0; $i < $order_cheque_condition['installments']; $i ++ ) :
                $add_day = $add * $i;
				$date    = date( 'Y-m-d', strtotime( $order->get_date_created()." + $add_day days" ) );
				$dates[] = wc_string_to_datetime($date);
			endfor;
			?>
            <ul class="cheque_date">

				<?php foreach ( $dates as $key => $cheque_date ) : ?>
                    <li>
                        <span class="label">تاریخ <?= ++ $key ?> :</span>
                        <span><?= convertEnglishToPersian( esc_html( wc_format_datetime( $cheque_date ) ) ) ?></span>
                    </li>
				<?php endforeach; ?>
            </ul>
			<?php if ( $extra_fields ) : ?>
				<?php foreach ( $extra_fields as $extra_field ) : ?>
                    <ul>

						<?php if ( $extra_field['field_type'] == 'file' && ! empty( $order_extra_fields_value[ $extra_field['field_id'] ] ) ) : ?>
                            <li class="label"><?= $extra_field['field_name'] ?>:</li>
                            <li>
                                <img src="<?= esc_url( $order_extra_fields_value[ $extra_field['field_id'] ] ) ?>" alt="<?= $extra_field['field_name'] ?>" style="width: 100%;;margin: 0.3rem 0;border: 1px solid #0f0f0f">
                            </li>
						<?php else : ?>
							<?php if ( ! empty( $order_extra_fields_value[ $extra_field['field_id'] ] ) ) : ?>
                                <li class="label"><span><?= $extra_field['field_name'] ?>:</span> <span><?= convertEnglishToPersian( $order_extra_fields_value[ $extra_field['field_id'] ] ) ?></span></li>
							<?php else : ?>
                                <li style="color: #999">موردی وجود ندارد</li>
							<?php endif; ?>
						<?php endif; ?>
                    </ul>
				<?php endforeach; ?>
			<?php
			endif; ?>
        </div>
    </div>
    <div class="cheque-images">
        <h3>تصاویر چک ها</h3>
		<?php
		if ( ! empty( $order_cheques ) ) :
			foreach ( $order_cheques as $cheque ) : ?>

                <img src="<?= esc_url( $cheque ) ?>" alt="<?= $cheque ?>" style="width: 100%;;margin: 0.3rem 0;border: 1px solid #0f0f0f">

			<?php endforeach; ?>
		<?php else : ?>
            <h3 style="color: #999">موردی وجود ندارد</h3>
		<?php endif; ?>
    </div>

</div>