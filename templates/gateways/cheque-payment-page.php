<div id="bnpl-container" class="my-2">
    <input type="hidden" name="themedoni_bnpl_order_total" value="<?= $order->get_total() ?>">
    <div class="relative px-6 py-5 overflow-hidden isolate lg:overflow-visible lg:px-0">

        <div id="loader" role="status" class="absolute hidden -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
            <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentFill"/>
            </svg>
            <span class="sr-only">Loading...</span>
        </div>

        <div class="py-5 lg:pr-4 bg-white border border-indigo-600 lg:pr-4 mb-2 px-5 shadow-sm rounded-xl" id="rules" style="border: 1px solid #9DABC5;">
            <p class="text-base font-semibold leading-7 text-indigo-600">قوانین</p>
            <p class="mt-6 text-xl leading-8 text-gray-700 "><?= html_entity_decode( $this->rules ) ?></p>
        </div>


        <div id="error-log" class="hidden mb-2 bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 p-1 shadow-md" role="alert">
            <div class="flex">
                <div class="px-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold" id="error-message"></p>
                </div>
            </div>
        </div>


        <form id="loading-container" method="post" action="" enctype="multipart/form-data" class="grid bg-white max-w-2xl grid-cols-1 p-5 mx-auto border shadow-md gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start lg:gap-y-10 rounded-xl shadow-indigo-500/20"
              style="border: 1px solid #9DABC5;">
			<?php wp_nonce_field() ?>

            <div class="lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <div class="lg:pr-4">
                    <div class="lg:max-w-lg">
                        <p class="text-base font-semibold leading-7 text-indigo-600">نوع اقساط</p>
                    </div>
                </div>
            </div>
            <div class="lg:sticky lg:top-4 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:overflow-hidden">
                <div class="w-full" id="bnpl_installments_container">
                    <div class="bg-teal-100 border-t-4 border-teal-500 mb-2 p-1 px-2 rounded-lg shadow-md text-teal-900" role="alert" style="border: 1px solid #16d068">
                        <div class="flex">
                    <div class="px-1">
                        <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">برای انتخاب چند عکس آنها را با هم انتخاب کنید</p>
                        <p class="text-sm">با نگهداشتن دکمه ctrl میتوانید چند عکس را انتخاب کنید و یا به درون کادر زیر بکشید </p>
                    </div>
                </div>
            </div>
            <div class="p-4 border-2 border-gray-100 rounded-lg">
                <div class="flex mb-4 flex-col cursor-pointer ">
                    <div id="drop-zone" class="w-full h-48 border-2 border-dashed border-gray-300 rounded-lg flex flex-col justify-center items-center text-gray-400 text-lg hover:bg-gray-100">
                        <span>تصاویر را به اینجا بکشید و یا</span>
                        <label for="file-input" class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            انتخاب کنید</label>
                        <input id="file-input" type="file" multiple class="hidden " name="themedoni_bnpl_cheque_image[]"/>
                    </div>
                    <div id="selected-files-count" class="text-gray-500 text-sm font-medium"></div>
                    <div id="selected-images" class="flex flex-wrap -mx-2 mt-6"></div>
                </div>

            </div>

    </div>
    <div class="w-full py-2">
		<?php foreach ( $this->extra_fields as $field ) : ?>
            <label for="<?= $field['field_id'] ?>" class="text-sm font-medium leading-6 text-gray-900"><?= $field['field_name'] ?></label>
            <div class="my-2">
                <input type="<?= $field['field_type'] ?>" name="<?= $field['field_id'] ?>" id="<?= $field['field_id'] ?>"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>

            </div>
		<?php endforeach; ?>
    </div>

    <div class="flex items-center justify-end mt-6 gap-x-2">
        <button type="submit" name="themedoni_bnpl_submit" value="themedoni_bnpl_submit"
                class="rounded-md bg-green-500 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500">ارسال
        </button>
        <button type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">بازگشت</button>

    </div>
</div>
<div class="lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
    <div>


        <div class="py-2 text-base leading-7 text-gray-700">
            <div class="w-full p-5 border-2 border-gray-100 rounded-lg shadow cheque-calculator cheque-product-calculator" style="border: 1px solid #9DABC5;">
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ سفارش:</p>
                    <p class="font-bold">
                        <bdi><?= priceFormat( $order->get_total() ) ?><span class="woocommerce-Price-currencySymbol"><?= get_woocommerce_currencies()[ get_woocommerce_currency() ] ?></span></bdi>
                        </span> </p>
                </div>

                <ul class="flex flex-wrap flex-row w-full gap-2 mt-2">
					<?php
					$i = - 1;
					if ( $this->cheque_conditions ) :
						foreach ( $this->cheque_conditions as $condition ) :
							$i ++;
							?>
                            <li>
                                <input type="radio" id="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]" name="themedoni_bnpl_order_condition_name" value="<?= esc_attr( $condition['condition_name'] ) ?>" class="hidden peer" required <?= ( $i == 0 ) ? 'checked' : '' ?>>
                                <label for="themedoni_bnpl_order_condition_name[<?= esc_attr( $i ) ?>]"
                                       class="font-bold w-full py-2 px-10 text-center shadow text-indigo-600 bg-white border border-indigo-600 rounded-sm cursor-pointer peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-600 hover:text-green-600 hover:border-green-600 hover:bg-green-100"
                                       style="border: 1px solid rgb(157, 171, 197)"><?= esc_attr( $condition['condition_name'] ) ?></label>
                            </li>
						<?php
						endforeach;
					endif;
					?>
                </ul>
            </div>
        </div>
        <div class="relative py-2 text-base leading-7 text-gray-700">

            <div class="w-full p-5 border-2 border-gray-100 rounded-lg shadow cheque-calculator cheque-product-calculator" style="border: 1px solid #9DABC5;">
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">پیش پرداخت:</p>
                    <p class="font-bold" id="bnpl_prepayment">-</p>

                </div>
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">تعداد چک ها:</p>
                    <p class="font-bold" id="bnpl_installments">-</p>
                </div>
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">بازپرداخت:</p>
                    <p class="font-bold" id="bnpl_term_of_installments">-</p>
                </div>
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">نرخ کارمزد:</p>
                    <p class="font-bold" id="bnpl_commission_rate">-</p>
                </div>
                <hr>
                <div class="w-full gap-6 flex flex-row justify-between items-center">
                    <p class="text-base font-semibold leading-7 text-indigo-600">مبلغ نهایی:</p>
                    <p class="font-bold" id="bnpl_final_price">-</p>
                </div>
            </div>
        </div>
        <div class="py-2 text-base leading-7 text-gray-700">
            <div class="p-5 border-2 border-gray-100 rounded-lg shadow cheque-calculator cheque-product-calculator" style="border: 1px solid #9DABC5;">
                <div class="w-full gap-6 flex flex-col justify-between" id="bnpl_cheque_dates">
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