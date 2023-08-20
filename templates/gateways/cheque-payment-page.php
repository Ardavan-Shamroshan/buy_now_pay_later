<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bnplTailwindCss', BNPL_URL . '/assets/dist/output.css', [], null );
	wp_enqueue_script( 'bnplTailwindCssCdn', 'https://cdn.tailwindcss.com', [], null );
	wp_enqueue_script( 'chequePaymentScript', BNPL_URL . '/assets/cheque-payment.js', [ 'jquery' ], null );
	wp_enqueue_style( 'chequePaymentStyle', BNPL_URL . '/assets/cheque-payment.css', [], null );
} )

?>

<div id="bnpl-container">
    <div class="relative isolate overflow-hidden bg-white px-6 py-5 lg:overflow-visible lg:px-0">

        <div id="loader" role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2 hidden">
            <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
            <span class="sr-only">Loading...</span>
        </div>

        <div class="lg:pr-4 py-5">
            <p class="text-base font-semibold leading-7 text-indigo-600">قوانین</p>
            <p class="mt-6 text-xl leading-8 text-gray-700"><?= html_entity_decode( $this->rules ) ?></p>
        </div>
        <div id="loading-container" class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start lg:gap-y-10 shadow-md p-5 rounded-xl shadow-indigo-500/20 border">
            <div class="lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <div class="lg:pr-4">
                    <div class="lg:max-w-lg">
                        <p class="text-base font-semibold leading-7 text-indigo-600">نوع اقساط</p>
                    </div>
                </div>
            </div>
            <div class="-ml-12 -mt-12 p-12 lg:sticky lg:top-4 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:overflow-hidden">
                <img class="w-[48rem] max-w-none rounded-xl bg-gray-900 shadow-xl ring-1 ring-gray-400/10 sm:w-[57rem]" src="https://tailwindui.com/img/component-images/dark-project-app-screenshot.png" alt="">
            </div>
            <div class="lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <div>
                    <div class="max-w-xl text-base leading-7 text-gray-700 lg:max-w-lg py-2">
                        <div class="border-2 border-gray-100 rounded-lg p-5">
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ سفارش:</p>
                                <p class="font-bold"> 69,900,000 تومان</p>
                            </div>

                            <ul class="grid w-full gap-6 md:grid-cols-2">
								<?php
								$i = - 1;
								if ( $this->cheque_conditions ):
									foreach ( $this->cheque_conditions as $condition ):
										$i ++;
										?>
                                        <li>
                                            <input type="radio" id="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]" name="themedoni_bnpl_order_condition_name" value="<?= esc_attr( $condition['condition_name'] ) ?>" class="hidden peer" required>
                                            <label for="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]"
                                                   class="inline-flex items-center justify-between w-full p-2 text-center text-gray-500 bg-white border border-gray-200 rounded-md cursor-pointer peer-checked:border-blue-600 peer-checked:bg-indigo-50 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100"><?= esc_attr( $condition['condition_name'] ) ?></label>
                                        </li>
									<?php
									endforeach;
								endif;
								?>
                            </ul>
                        </div>
                    </div>
                    <div class="max-w-xl text-base leading-7 text-gray-700 lg:max-w-lg py-2 relative">

                        <div class="border-2 border-gray-100 rounded-lg p-5">
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">پیش پرداخت:</p>
                                <p class="font-bold" id="bnpl_prepayment">-</p>
                            </div>
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">تعداد چک ها:</p>
                                <p class="font-bold" id="bnpl_installments">-</p>
                            </div>
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ چک ها:</p>
                                <p class="font-bold" id="bnpl_commission_rate">-</p>
                            </div>
                            <hr>
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ نهایی:</p>
                                <p class="font-bold" id="bnpl_final_price">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-xl text-base leading-7 text-gray-700 lg:max-w-lg py-2">
                        <div class="border-2 border-gray-100 rounded-lg p-5">
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">تاریخ چک 1:</p>
                                <p class="font-bold" id="bnpl_cheque_date">7 شهریور 1402</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!--    <div class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained">-->
<!--        <div>-->
<!--            <form method="post" action="" enctype="multipart/form-data">-->
<!--                <div class="col2-set">-->
<!--                    <div class="woocommerce-checkout-review-order">-->
<!--                        <div>-->
<!--                            <h3 class="text-rose-900">تصاویر چک</h3>-->
<!--                            <div id="bnpl_installments_container"></div>-->
<!--                            <div>-->
<!--                                <h3>فیلد های ضروری</h3>-->
<!---->
<!--								--><?php //foreach ( $this->extra_fields as $field ): ?>
<!--                                    <div>-->
<!--                                        <label for="--><?php //= $field['field_id'] ?><!--">--><?php //= $field['field_name'] ?><!--</label>-->
<!--                                        <!-- $field['field_type'] -->-->
<!--                                        <input type="text" name="--><?php //= $field['field_id'] ?><!--" id="--><?php //= $field['field_id'] ?><!--">-->
<!--                                    </div>-->
<!--								--><?php //endforeach; ?>
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!--                    <button name="themedoni_bnpl_submit" type="submit">ارسال</button>-->
<!--                </div>-->
<!--                <div class="col2-set">-->
<!--                    <h3>نوع اقساط</h3>-->
<!--                    <div class="woocommerce-checkout-review-order">-->
<!--                        <table class="shop_table woocommerce-checkout-review-order-table">-->
<!--                            <tbody>-->
<!--                            <tr class="order-total">-->
<!--                                <th>مجموع</th>-->
<!--                                <td><strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"></span>58.99</bdi></span></strong></td>-->
<!--                            </tr>-->
<!--							--><?php
// 							$i = - 1;
// 							if ( $this->cheque_conditions ) {
// 								foreach ( $this->cheque_conditions as $condition ) {
// 									$i ++;
// 									?>
<!--                                    <tr>-->
<!--                                        <td style="display: flex">-->
<!--                                            <input type="radio" value="--><?php //= esc_attr( $condition['condition_name'] ) ?><!--" name="themedoni_bnpl_order_condition_name" id="themedoni_bnpl_order_condition_name[--><?php //= esc_attr( $i ) ?><!--]"/>-->
<!--                                            <label for="themedoni_bnpl_order_condition_name[--><?php //= esc_attr( $i ) ?><!--]">--><?php //= esc_attr( $condition['condition_name'] ) ?><!--</label>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--									--><?php
// 								}
// 							}
// 							?>
<!--                            </tbody>-->
<!--                        </table>-->
<!--                        <table class="shop_table woocommerce-checkout-review-order-table">-->
<!--                            <tbody>-->
<!--                            <tr>-->
<!--                                <th>پیش پرداخت</th>-->
<!--                                <td id="bnpl_prepayment">0</td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <th>تعداد چک ها</th>-->
<!--                                <td id="bnpl_installments">0</td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <th>مبلغ چک ها</th>-->
<!--                                <td id="bnpl_commission_rate">0</td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!---->
<!--                        <table class="shop_table woocommerce-checkout-review-order-table">-->
<!--                            <tbody>-->
<!--                            <tr>-->
<!--                                <th>مبلغ نهایی</th>-->
<!--                                <td id="bnpl_final_price">0</td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!---->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
</div>

