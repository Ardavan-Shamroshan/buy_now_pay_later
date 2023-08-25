<?php

namespace Inc\Controllers;

class GatewayHooksController extends BaseController
{

    public function register()
    {
        add_action('init', [$this, 'register_cheque_approval_order_status']);
        add_filter('wc_order_statuses', [$this, 'add_cheque_approval_to_order_statuses']);

        add_action('admin_head', [$this, 'styling_admin_order_list']);
    }


    /**
     * Custom order status background button color in Woocommerce admin order list
     */
    public function styling_admin_order_list()
    {
        global $pagenow, $post;

        if ($pagenow != 'edit.php') return; // Exit
        if (get_post_type($post->ID) != 'shop_order') return; // Exit

        // HERE we set your custom status
        $order_status = 'cheque_approval'; // <==== HERE
?>
        <style>
            .order-status.status-<?php echo sanitize_title($order_status); ?> {
                background: #d7f8a7;
                color: #0c942b;
            }
        </style>
<?php
    }



    public function register_cheque_approval_order_status()
    {
        register_post_status('wc-cheque_approval', array(
            'label'                     => 'در انتظار تایید چک',
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop('در انتظار تایید چک <span class="count">(%s)</span>', 'در انتظار تایید چک <span class="count">(%s)</span>')
        ));
    }

    public function add_cheque_approval_to_order_statuses($order_statuses)
    {
        $new_order_statuses = array();
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;
            if ('wc-processing' === $key) {
                $new_order_statuses['wc-cheque_approval'] = 'در انتظار تایید چک';
            }
        }
        return $new_order_statuses;
    }
}
