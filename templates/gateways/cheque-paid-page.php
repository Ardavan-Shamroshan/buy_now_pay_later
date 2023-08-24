<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'bnplTailwindCss', BNPL_URL . '/assets/dist/output.css', ['tailwindcss'], BuyNowPayLaterVersion );
	// wp_register_style( 'chequePaymentStyle', BNPL_URL . '/assets/cheque-payment.css', [], null );
	wp_register_script( 'chequePaymentScript', BNPL_URL . '/assets/cheque-payment.js', [ 'jquery' ], BuyNowPayLaterVersion );

	wp_enqueue_style( 'bnplTailwindCss' );
	// wp_enqueue_style( 'chequePaymentStyle' );
	wp_enqueue_script( 'chequePaymentScript' );
	// wp_enqueue_script( 'bnplTailwindCssCdn', 'https://cdn.tailwindcss.com', [], null );
}, 999 );

?>


<div class="relative overflow-x-auto py-5">
    <div class="lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:mx-auto lg:grid lg:w-full lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
        <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">محصول</th>
                <th scope="col" class="px-6 py-3">مجموع</th>
            </tr>
            </thead>
            <tbody>

			<?php foreach ( $order->get_items() as $item ): ?>
			<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

