<?php


$onepage_settings = array();

/*
 * OPTIONS
 * =======
 *
 */

$onepage_settings['one_address'] = false;
$onepage_settings['one_step'] = true;
$onepage_settings['fixed_loading'] = false;

$onepage_settings['is_coupon'] = true;
$onepage_settings['is_voucher'] = true;
$onepage_settings['is_reward'] = false; //NOT IMPLEMENTED. KEEP AS FALSE

$onepage_settings['layout'] = 'onepage.tpl';


// For shoppica
//$onepage_settings['theme'] = 'onepage/onepage_shoppica.tpl';

// For default OC theme
$onepage_settings['theme'] = 'onepage/onepage_wrapper.tpl';


/*
 * DEFAULT COUNTRY/ZONE
 * ====================
 *
 */

$onepage_settings['payment_zone'] = 2454;
$onepage_settings['payment_country'] = 161;
$onepage_settings['shipping_zone'] = 2454;
$onepage_settings['shipping_country'] = 161;



/*
 * LANGUAGE ENTRIES
 * =======================
 *
 * To add phrases in additional languages, add the language code as the 2nd array parameter:
 * $onepage_settings['entry_also_register']['en'] = "While at it, also create an account";
 *
 */
$onepage_settings['entry_also_register']['en'] = "While at it, also create an account";
$onepage_settings['entry_also_register']['ru'] = "Создать аккаунт, после покупки.";
$onepage_settings['entry_also_register']['ua'] = "Створити обліковий запис, після покупки.";
$onepage_settings['entry_also_register']['ar'] = "Ø£Ù†Ø´Ø¦ Ø­Ø³Ø§Ø¨Ø§ Ù„Ø­Ù�Ø¸ Ù…Ø¹Ù„ÙˆÙ…Ø§ØªÙƒ Ù„Ø·Ù„Ø¨Ø§ØªÙƒ Ø§Ù„Ù‚Ø§Ø¯Ù…Ø©";
$onepage_settings['entry_also_register']['fr'] = "While at it, also create an account";

$onepage_settings['entry_return']['en'] = "<< Return to previous page";
$onepage_settings['entry_return']['ru'] = "<< Возвратится на предыдущую страницу";
$onepage_settings['entry_return']['ua'] = "<< Повернутися на попередню сторінку";
$onepage_settings['entry_return']['ar'] = " Ø§Ù„Ø¹ÙˆØ¯Ø© Ø§Ù„Ù‰ Ø§Ù„ØµÙ�Ø­Ø© Ø§Ù„Ø³Ø§Ø¨Ù‚Ø©>>";
$onepage_settings['entry_return']['fr'] = "<< Return to previous page";


$onepage_settings['text_checkout_payment_address']['en']  = 'Billing Details';
$onepage_settings['text_checkout_payment_address']['ru']  = 'Платежная информация';
$onepage_settings['text_checkout_payment_address']['ua']  = 'Платіжна інформація';
$onepage_settings['text_checkout_shipping_address']['en'] = 'Delivery Details';
$onepage_settings['text_checkout_shipping_address']['ru'] = 'Информация о доставке';
$onepage_settings['text_checkout_shipping_address']['ua'] = 'Інформація про доставку';
$onepage_settings['text_checkout_shipping_method']['en']  = 'Delivery Method';
$onepage_settings['text_checkout_shipping_method']['ru']  = 'Способ доставки';
$onepage_settings['text_checkout_shipping_method']['ua']  = 'Спосіб доставки';
$onepage_settings['text_checkout_payment_method']['en']   = 'Payment Method';
$onepage_settings['text_checkout_payment_method']['ru']   = 'Способ оплаты';
$onepage_settings['text_checkout_payment_method']['ua']   = 'Спосіб оплати';
$onepage_settings['text_checkout_confirm']['en']          = 'Accept & Confirm Order';
$onepage_settings['text_checkout_confirm']['ru']          = 'Подтверждение заказа';
$onepage_settings['text_checkout_confirm']['ua']          = 'Підтвердження замовлення';
$onepage_settings['text_checkout_summary']['en']          = 'Order Summary';
$onepage_settings['text_checkout_summary']['ru']          = 'Сводка заказов';
$onepage_settings['text_checkout_summary']['ua']          = 'Загальна інформація';


?>
