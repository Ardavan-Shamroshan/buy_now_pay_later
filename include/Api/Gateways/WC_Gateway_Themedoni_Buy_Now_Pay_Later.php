<?php

namespace Inc\Api\Gateways;

use WC_Payment_Gateway;

class WC_Gateway_Themedoni_Buy_Now_Pay_Later extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->author             = 'اردوان شام روشن';
        $this->id                 = 'WC_BehPardakht';
        $this->method_title       = __('به پرداخت ملت');
        $this->method_description = __('تنظیمات درگاه پرداخت به پرداخت ملت برای ووکامرس');
        $this->icon               = apply_filters('WC_BehPardakht_logo', plugins_url('/assets/images/logo.png', __FILE__));
        $this->has_fields         = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = $this->settings['title'];
        $this->description = $this->settings['description'];

        $this->terminal_id = $this->settings['terminal_id'];
        $this->username    = $this->settings['username'];
        $this->password    = $this->settings['password'];

        $this->order_pay_show = $this->settings['order_pay_show'] ?? 'yes';

        $this->success_massage = $this->settings['success_massage'];
        $this->failed_massage  = $this->settings['failed_massage'];

    }

    public function init_form_fields()
    {
        $this->form_fields = apply_filters(
            'WC_BehPardakht_Config',
            array(
                'base_config'     => array(
                    'title'       => __('تنظیمات درگاه'),
                    'type'        => 'title',
                    'description' => '',
                ),
                'enabled'         => array(
                    'title'       => __('فعالسازی/غیرفعالسازی'),
                    'type'        => 'checkbox',
                    'label'       => __('فعالسازی درگاه پرداخت به پرداخت ملت'),
                    'description' => __('برای فعالسازی درگاه به پرداخت ملت باید این قسمت را  را علامتگذاری کنید.'),
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'title'           => array(
                    'title'       => __('عنوان درگاه'),
                    'type'        => 'text',
                    'description' => __('عنوان درگاه که در طول خرید به مشتری نمایش داده می‌شود'),
                    'default'     => __('به پرداخت ملت'),
                    'desc_tip'    => true,
                ),
                'description'     => array(
                    'title'       => __('توضیحات درگاه'),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'description' => __('توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد'),
                    'default'     => __('پرداخت امن از طریق درگاه پرداخت به پرداخت ملت(قابل پرداخت با کلیه کارتهای عضو شتاب)')
                ),
                'account_config'  => array(
                    'title'       => __('اطلاعات درگاه پرداخت'),
                    'type'        => 'title',
                    'description' => '',
                ),
                'terminal_id'     => array(
                    'title'       => __('شماره ترمینال'),
                    'type'        => 'text',
                    'description' => __('Terminal ID'),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'username'        => array(
                    'title'       => __('نام کاربری'),
                    'type'        => 'text',
                    'description' => __('Username'),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'password'        => array(
                    'title'       => __('کلمه عبور'),
                    'type'        => 'text',
                    'description' => __('Password'),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'payment_config'  => array(
                    'title'       => __('تنظیمات عملیات پرداخت'),
                    'type'        => 'title',
                    'description' => '',
                ),
                'order_pay_show'  => array(
                    'title'       => __('برگه پیش فاکتور'),
                    'type'        => 'checkbox',
                    'label'       => __('نمایش برگه پیش فاکتور'),
                    'description' => __('برای نمایش برگه پیش فاکتور این قسمت را علامتگذاری کنید'),
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'success_massage' => array(
                    'title'       => __('پیام پرداخت موفق'),
                    'type'        => 'textarea',
                    'description' => __('متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید.
                                            همچنین می توانید از کدهای کوتاه زیر استفاده کنید:<br/>
                                            <strong>%Transaction_id%</strong> : کد رهگیری<br/>
                                            <strong>%Order_Number%</strong> : شماره درخواست تراکنش<br/>'),
                    'default'     => __('پرداخت با موفقیت انجام شد.'),
                ),
                'failed_massage'  => array(
                    'title'       => __('پیام پرداخت ناموفق'),
                    'type'        => 'textarea',
                    'description' => __('متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد %fault% برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت به پرداخت ملت ارسال میگردد .'),
                    'default'     => __('پرداخت با شکست مواجه شد. شرح خطا: %fault%'),
                ),
            )
        );
    }

}
