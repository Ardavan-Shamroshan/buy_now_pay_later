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
                    $('<input type="file" name="themedoni_bnpl_cheque_image_' + i + '" />').appendTo('#bnpl_installments_container');
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
