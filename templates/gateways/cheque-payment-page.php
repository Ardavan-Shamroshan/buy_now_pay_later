<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'bnplTailwindCss', BNPL_URL . '/assets/dist/output.css', [], null );
	// wp_register_style( 'chequePaymentStyle', BNPL_URL . '/assets/cheque-payment.css', [], null );
	wp_register_script( 'chequePaymentScript', BNPL_URL . '/assets/cheque-payment.js', [ 'jquery' ], null );

	wp_enqueue_style( 'bnplTailwindCss' );
	// wp_enqueue_style( 'chequePaymentStyle' );
	wp_enqueue_script( 'chequePaymentScript' );
	// wp_enqueue_script( 'bnplTailwindCssCdn', 'https://cdn.tailwindcss.com', [], null );
} , 999);

if ( isset( $_SESSION['message'] ) ) :
	echo $_SESSION['message'];
	unset( $_SESSION['message'] );
endif;
?>

<div id="bnpl-container">
    <div class="relative isolate overflow-hidden bg-white px-6 py-5 lg:overflow-visible lg:px-0">

        <div id="loader" role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2 hidden">
            <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentFill"/>
            </svg>
            <span class="sr-only">Loading...</span>
        </div>

        <div class="lg:pr-4 py-5" id="rules">
            <p class="text-base font-semibold leading-7 text-indigo-600">قوانین</p>
            <p class="mt-6 text-xl leading-8 text-gray-700"><?= html_entity_decode( $this->rules ) ?></p>
        </div>
        <form id="loading-container" method="post" action="" enctype="multipart/form-data" class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start lg:gap-y-10 shadow-md p-5 rounded-xl shadow-indigo-500/20 border">
            <div class="lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <div class="lg:pr-4">
                    <div class="lg:max-w-lg">
                        <p class="text-base font-semibold leading-7 text-indigo-600">نوع اقساط</p>
                    </div>
                </div>
            </div>
            <div class="-ml-12 -mt-12 p-12 lg:sticky lg:top-4 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:overflow-hidden">
                <div class="w-full" id="bnpl_installments_container">
                    <label for="dropzone-file"
                           class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 "><span class="font-semibold">تصویر چک را انتخاب کنید</span> یا آنرا به داخل کادر بکشید</p>
                            <p class="text-xs text-gray-500 ">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                        </div>
                        <input id="dropzone-file" type="file" class="hidden"/>
                    </label>
                </div>

                <div class="w-full py-2">
					<?php foreach ( $this->extra_fields as $field ): ?>
                        <label for="<?= $field['field_id'] ?>" class="text-sm font-medium leading-6 text-gray-900"><?= $field['field_name'] ?></label>
                        <div class="my-2">
                            <input type="<?= $field['field_type'] ?>" name="<?= $field['field_id'] ?>" id="<?= $field['field_id'] ?>"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>

                        </div>
					<?php endforeach; ?>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-2">
                    <button type="submit" name="themedoni_bnpl_submit" value="themedoni_bnpl_submit"
                            class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">ارسال
                    </button>
                    <button type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">بازگشت</button>
                </div>
            </div>
            <div class="lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <div>
                    <div class="max-w-xl text-base leading-7 text-gray-700 lg:max-w-lg py-2">
                        <div class="border-2 border-gray-100 rounded-lg p-5">
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ سفارش:</p>
                                <p class="font-bold"> <?= priceFormat( $order->get_total() ) ?></p>
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
                                <p class="text-base font-semibold leading-7 text-indigo-600">بازپرداخت:</p>
                                <p class="font-bold" id="bnpl_term_of_installments">-</p>
                            </div>
                            <div class="grid w-full gap-6 md:grid-cols-2">
                                <p class="text-base font-semibold leading-7 text-indigo-600">نرخ کارمزد:</p>
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
                            <div class="grid w-full gap-6 md:grid-cols-2" id="bnpl_cheque_dates">
                                <p class="text-base font-semibold leading-7 text-indigo-600">تاریخ چک :</p>
                                <p class="font-bold">-</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

