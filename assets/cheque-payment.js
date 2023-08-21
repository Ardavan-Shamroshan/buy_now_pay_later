jQuery(document).ready(function ($) {
    $('#bnpl-container #rules ul').addClass('list-disc text-slate-500')
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
                let today = new Date();
                response = JSON.parse(response);
                let daysToAdd = parseInt(response.response.term_of_installments) / parseInt(response.response.installments);

                $('#bnpl_installments_container').empty();
                $('#bnpl_prepayment').empty();
                $('#bnpl_installments').empty();
                $('#bnpl_commission_rate').empty();
                $('#bnpl_final_price').empty();
                $('#bnpl_cheque_dates').empty();
                for (let i = 0; i < response.response.installments; i++) {
                    $('<label for="dropzone-file" class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50hover:bg-gray-100"><div class="flex flex-col items-center justify-center pt-5 pb-6"><svg class="w-8 h-8 mb-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">\n' +
                        '                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>\n' +
                        '                            </svg><p class="mb-2 text-sm text-gray-500 "><span class="font-semibold">تصویر چک را انتخاب کنید</span> یا آنرا به داخل کادر بکشید</p>\n' +
                        '                            <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>\n' +
                        '                        </div>\n' +
                        '                        <input id="dropzone-file" type="file" name="themedoni_bnpl_cheque_image_' + i + '" class="hidden" required/>\n' +
                        '                    </label>').appendTo('#bnpl_installments_container');
                    // $('<label class="block mb-2 text-sm font-medium text-gray-900 " for="file_input"></label><input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="file_input" name="themedoni_bnpl_cheque_image_' + i + '" type="file">').appendTo('#bnpl_installments_container');

                    $("#bnpl_prepayment").html(insertrialcamma(toFarsiNumber(JSON.parse(response.response.prepayment))) + 'تومان');
                    $("#bnpl_installments").html(toFarsiNumber(JSON.parse(response.response.installments)) + ' مورد ');
                    $("#bnpl_term_of_installments").html(toFarsiNumber(JSON.parse(response.response.term_of_installments)) + 'روز');
                    $("#bnpl_commission_rate").html(toFarsiNumber(JSON.parse(response.response.commission_rate)) + '%');
                    $("#bnpl_final_price").html('درحال محاسبه');

                    let result = today.setDate(today.getDate() + daysToAdd);
                    let mydate = new Date(result);
                    let mypersiandate = mydate.toLocaleDateString('fa-IR');
                    $('<p class="text-base font-semibold leading-7 text-indigo-600"> تاریخ چک ' + toFarsiNumber(++i) + ': </p><p class="font-bold">' + mypersiandate + '</p>').appendTo('#bnpl_cheque_dates');
                }
            },
            error: function (error) {
                console.log(error)
            },
        });
    }


    function insertrialcamma(n) {
        var m = "";
        for (var i = 0; i < n.length; i++) {
            var c = n.substr(n.length - i - 1, 1);
            if (i % 3 == 0 & i > 0) {
                m = c + ',' + m;
            } else {
                m = c + m;
            }
        }
        return m;
    }

    function toFarsiNumber(n) {
        var o = "";
        n = n.toString();
        const farsiDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        for (var i = 0; i < n.length; i++) {
            for (var j = 0; j < englishDigits.length; j++) {
                if (n.substr(i, 1) == englishDigits[j]) {
                    o = o + farsiDigits[j];
                }
            }
        }
        return o;
    }
});
