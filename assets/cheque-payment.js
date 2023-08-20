jQuery(document).ready(function ($) {
    $('input[name="themedoni_bnpl_order_condition_name"]').on('click', function () {
        let installment_name = jQuery('input[name="themedoni_bnpl_order_condition_name"]:checked').val();

        $.ajax({
            type: "post",
            // contentType: 'application/json; charset'
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
                    $("#bnpl_installments").html(JSON.parse(response.response.installments));
                    $("#bnpl_commission_rate").html(JSON.parse(response.response.commission_rate));
                }
            },
            error: function (error) {
                console.log(error)
            }
        });

    });
});
