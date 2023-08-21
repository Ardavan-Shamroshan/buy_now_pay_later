jQuery(document).ready(function ($) {
    $('input[name="themedoni_bnpl_order_condition_name"]').on('click', function () {
        let installment_name = $('input[name="themedoni_bnpl_order_condition_name"]:checked').val();

        $(document)
            .ajaxStart(function () {
                $("#loading-container").addClass('opacity-20');
                $("#loader").removeClass('hidden');
            })
            .ajaxStop(function () {
                $("#loading-container").removeClass('opacity-20');
                $("#loader").addClass('hidden');
            });


        handleAjax(installment_name);
    });

    function handleAjax(installment_name) {
        $.ajax({
            type: "post",
            url: '//localhost/buy-now-pay-later/wp-admin/admin-ajax.php',
            data: {
                action: 'bnpl_get_data',
                name: installment_name
            },
            success: function (response) {
                response = JSON.parse(response);
                $('#bnpl_installments_container').empty();
                $('#bnpl_prepayment').empty();
                $('#bnpl_installments').empty();
                $('#bnpl_commission_rate').empty();
                $('#bnpl_final_price').empty();
                for (let i = 0; i < response.response.installments; i++) {
                    // $('<label for="dropzone-file" class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"><div class="flex flex-col items-center justify-center pt-5 pb-6"><svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">\n' +
                    //     '                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>\n' +
                    //     '                            </svg><p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">تصویر چک را انتخاب کنید</span> یا آنرا به داخل کادر بکشید</p>\n' +
                    //     '                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>\n' +
                    //     '                        </div>\n' +
                    //     '                        <input id="dropzone-file" type="file" name="themedoni_bnpl_cheque_image_' + i + '" class="hidden"/>\n' +
                    //     '                    </label>').appendTo('#bnpl_installments_container');
                    $('<label class="block mb-2 text-sm font-medium text-gray-900 " for="file_input"></label><input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="file_input" name="themedoni_bnpl_cheque_image_' + i + '" type="file">').appendTo('#bnpl_installments_container');

                    $("#bnpl_prepayment").html(JSON.parse(response.response.prepayment));
                    $("#bnpl_installments").html(JSON.parse(response.response.installments) + ' مورد ');
                    $("#bnpl_commission_rate").html(JSON.parse(response.response.commission_rate));
                }
            },
            error: function (error) {
                console.log(error)
            },
        });


    }
});
