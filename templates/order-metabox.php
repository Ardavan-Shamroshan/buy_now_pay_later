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
            <table style="width:100%;margin-top: 1rem;">
                <tbody>
                    <tr>
                        <td class="label">نام شرط:</td>
                        <td><?= $order_cheque_condition['condition_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="label">پیش پرداخت:</td>
                        <td><?= discountFormat($order_cheque_condition['prepayment']) ?></td>
                        <td><?= priceFormat($prepayment_price) ?></td>
                    </tr>
                    <tr>
                        <td class="label">اقساط:</td>
                        <td><?= convertEnglishToPersian($order_cheque_condition['installments']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">مبلغ چک ها:</td>
                        <td><?= priceFormat($every_installment_price) ?></td>
                    </tr>
                    <tr>
                        <td class="label">مبلغ نهایی:</td>
                        <td><?= priceFormat($final_price) ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="clear"></div>

            <?php
            $daysToAdd = $order_cheque_condition['term_of_installments'] / $order_cheque_condition['installments'];
            $dates     = [];
            for ($i = 0; $i < $order_cheque_condition['installments']; $i++) :
                $date    = date('Y-m-d', strtotime("+$daysToAdd days"));
                $dates[] = wc_string_to_datetime($date);
            endfor;
            ?>
            <table style="width:100%;margin-top: 1rem;">
                <tbody>

                    <?php foreach ($dates as $key => $cheque_date) : ?>
                        <tr>
                            <td class="label">تاریخ <?= ++$key ?> :</td>
                            <td><?= esc_html(wc_format_datetime($cheque_date)) ?></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
            <div class="clear"></div>
            <?php if ($extra_fields) : ?>
                <table style="width:100%;margin-top: 1rem;">
                    <tbody>

                        <?php foreach ($extra_fields as $extra_field) : ?>
                            <tr>
                                <td class="label"><?= $extra_field['field_name'] ?>:</td>
                                <?php if ($extra_field['field_type'] == 'file' && !empty($order_extra_fields_value[$extra_field['field_id']])) : ?>
                                    <td>
                                        <img src="<?= esc_url( $order_extra_fields_value[$extra_field['field_id']]) ?>" alt="<?= $extra_field['field_name'] ?>" style="width: 100%;;margin: 0.3rem 0;border: 1px solid #0f0f0f">
                                    </td>
                                <?php else : ?>
                                    <?php if (!empty($order_extra_fields_value[$extra_field['field_id']])) : ?>
                                        <td><?= convertEnglishToPersian($order_extra_fields_value[$extra_field['field_id']]) ?></td>
                                    <?php else : ?>
                                        <td style="color: #999">موردی وجود ندارد</td>
                                    <?php endif; ?>
                                <?php endif; ?>
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
        <?php if (!empty($order_cheques)) :
            foreach ($order_cheques as $cheque) : ?>
                <img src="<?= esc_url($cheque) ?>" alt="<?= $cheque ?>" style="width: 100%;;margin: 0.3rem 0;border: 1px solid #0f0f0f">
            <?php endforeach; ?>
        <?php else : ?>
            <h3 style="color: #999">موردی وجود ندارد</h3>
        <?php endif; ?>
    </div>
</div>