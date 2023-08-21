<?php
$uploads = wp_upload_dir();
?>
<style>
    tbody tr {
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding-bottom: 0.5rem;
        padding-top: 0.5rem;
        border-bottom: 1px solid #e8e8e8;
    }
</style>
<div style="width: 100%;display:inline-flex;justify-content: space-between; padding 0.2rem 0; gap: 0.5rem">
    <div style="width: 30%;border: 1px solid #e8e8e8;border-radius: 3px; padding: 0.5rem">
        <h4>اطلاعات چک ها</h4>
        <div>
            <table style="margin-top: 1rem;">
                <tbody>
                <tr>
                    <td class="label">نام شرط:</td>
                    <td><?= $order_cheque_condition['condition_name'] ?></td>
                </tr>
                <tr>
                    <td class="label">پیش پرداخت:</td>
                    <td><?= $order_cheque_condition['prepayment'] ?></td>
                </tr>
                <tr>
                    <td class="label">اقساط:</td>
                    <td><?= $order_cheque_condition['installments'] ?></td>
                </tr>
                <tr>
                    <td class="label">کارمزد:</td>
                    <td><?= $order_cheque_condition['commission_rate'] ?></td>
                </tr>
                <tr>
                    <td class="label">مبلغ چک:</td>
                    <td>2,000,000 تومان</td>
                </tr>
                <tr>
                    <td class="label">مبلغ نهایی:</td>
                    <td>24,000,000 تومان</td>
                </tr>
                </tbody>
            </table>
            <div class="clear"></div>

            <table style="margin-top: 1rem;">
                <tbody>
                <tr>
                    <td class="label">تاریخ 1:</td>
                    <td>7 شهریور 1401</td>
                </tr>
                </tbody>
            </table>
            <div class="clear"></div>

			<?php if ( $extra_fields ): ?>
                <table style="margin-top: 1rem;">
                    <tbody>

					<?php foreach ( $extra_fields as $extra_field ): ?>
                        <tr>
                            <td class="label"><?= $extra_field['field_name'] ?>:</td>
                            <td><?= $order_extra_fields_value[ $extra_field['field_id'] ] ?></td>
                        </tr>
					<?php endforeach; ?>

                    </tbody>
                </table>
                <div class="clear"></div>
			<?php endif; ?>

        </div>
    </div>

    <div style="width: 70%;border: 1px solid #e8e8e8;;border-radius: 3px; padding: 0.5rem;">
        <h4>تصاویر چک ها</h4>
		<?php foreach ( $order_cheques as $cheque ): ?>
            <img src="<?= esc_url( $uploads['baseurl'] . $cheque ) ?>" alt="<?= $cheque ?>" style="width: 100%;;margin: 0.3rem 0;border: 1px solid #0f0f0f">

		<?php endforeach; ?>
    </div>
</div>

