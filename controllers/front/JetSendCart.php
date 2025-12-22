<?php

/**
 * CreditJet Send Cart Front Controller
 *
 * Handles AJAX requests for sending cart data and creating credit applications.
 * This controller processes credit requests from the frontend, validates input,
 * generates email notifications, and creates orders for credit applications.
 *
 * Provides functionality for both regular credit and credit card payment requests,
 * including customer data collection, product information processing, and order creation.
 *
 * @extends ModuleFrontController
 */
class CreditJetJetSendCartModuleFrontController extends ModuleFrontController
{

    /**
     * Process AJAX request for credit application submission
     *
     * Handles the complete credit application flow including:
     * - Security validation (AJAX requests only, CSRF protection)
     * - Input sanitization for customer and credit data
     * - Email content generation with customer and product details
     * - Email sending to administrators and PB Personal Finance
     * - Cart creation and order processing for successful applications
     * - JSON response generation for frontend feedback
     *
     * Supports both regular credit and credit card applications with different
     * processing logic and email content generation.
     *
     * Expected input parameters include customer info (name, email, EGN, phone),
     * credit details (amounts, installments, down payment), and product information.
     *
     * @return void Outputs JSON response and terminates execution
     */
    public function initContent()
    {
        // Security check - only allow AJAX requests (before any other logic)
        if (!$this->isXmlHttpRequest()) {
            die('Forbidden - AJAX requests only');
        }

        // CSRF protection - use general token for AJAX
        $csrf_token = \Tools::getToken();
        if (!Tools::isSubmit('jet_token') || Tools::getValue('jet_token') !== $csrf_token) {
            die('CSRF protection - invalid token');
        }

        parent::initContent();

        $json = [];

        if (!empty((string)Tools::getValue('jet_lname'))) die();
        $jet_priceall = filter_var(Tools::getValue('jet_priceall', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_vnoski = filter_var(Tools::getValue('jet_vnoski', (int)Configuration::get("JET_VNOSKI_DEFAULT")), FILTER_SANITIZE_NUMBER_INT);
        $jet_vnoska = filter_var(Tools::getValue('jet_vnoska', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_parva = filter_var(Tools::getValue('jet_parva', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_total_credit_price = filter_var(Tools::getValue('jet_total_credit_price', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_obshto = filter_var(Tools::getValue('jet_obshto', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_gpr = filter_var(Tools::getValue('jet_gpr', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_glp = filter_var(Tools::getValue('jet_glp', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_name = filter_var(Tools::getValue('jet_name', ' - '), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_lastname = filter_var(Tools::getValue('jet_lastname', ' - '), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_egn = filter_var(Tools::getValue('jet_egn', ' - '), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_email = filter_var(Tools::getValue('jet_email', ' - '), FILTER_SANITIZE_EMAIL);
        $jet_phone = filter_var(Tools::getValue('jet_phone', ' - '), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_card = filter_var(Tools::getValue('jet_card', 0), FILTER_SANITIZE_NUMBER_INT);
        $jet_products_id = filter_var(Tools::getValue('jet_products_id', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_qt = filter_var(Tools::getValue('jet_products_qt', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_pr = filter_var(Tools::getValue('jet_products_pr', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_ct = filter_var(Tools::getValue('jet_products_ct', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_vr = filter_var(Tools::getValue('jet_products_vr', ''), FILTER_SANITIZE_SPECIAL_CHARS);

        $jet_eur = (int)Configuration::get("JET_EUR");
        $jet_sign = 'лева';
        $jet_order_currency = 'BGN';
        switch ($jet_eur) {
            case 0:
                $jet_sign = 'лева';
                break;
            case 1:
                $jet_sign = 'лева';
                break;
            case 2:
                $jet_sign = 'евро';
                break;
            case 3:
                $jet_sign = 'евро';
                break;
        }

        $body = "Данни за потребителя:\r\n";

        $body .= "Собствено име: $jet_name;\r\n";
        $body .= "Фамилия: $jet_lastname;\r\n";
        $body .= "ЕГН: $jet_egn;\r\n";
        $body .= "Телефон за връзка: $jet_phone;\r\n";
        $body .= "Имейл адрес: $jet_email;\r\n\r\n";

        $body .= "Данни за стоката:\r\n";

        $_product = explode('_', $jet_products_id);
        $product_q = explode('_', $jet_products_qt);
        $product_p = explode('_', $jet_products_pr);
        $product_c = explode('_', $jet_products_ct);
        $product_v = explode('_', $jet_products_vr);

        for ($index = 0; $index < sizeof($_product); $index++) {
            $jet_product_id = $_product[$index];
            $product = new Product($jet_product_id, false, $this->context->language->id);
            $cat = new Category($product_c[$index], $this->context->language->id);
            if (isset($cat) && $cat->name) {
                $product_c_txt = $cat->name;
            } else {
                $product_c_txt = ' - ';
            }

            $product_m_txt = $product->name;
            if ($product_m_txt == '') {
                $product_m_txt = ' - ';
            }

            if (isset($product_p[$index]) && (float)$product_p[$index] != 0) {
                $product_p_txt = (float)$product_p[$index];
            } else {
                $product_p_txt = (float)Product::getPriceStatic($jet_product_id, true);
            }
            if (isset($product_q[$index]) && (int)$product_q[$index] != 0) {
                $product_q_txt = (int)$product_q[$index];
            } else {
                $product_q_txt = 1;
            }
            if (isset($product_v[$index]) && (int)$product_v[$index] != 0) {
                $product_v_txt = (int)$product_v[$index];
            } else {
                $product_v_txt = 0;
            }

            $sql = 'SELECT al.name AS attribute_name, pa.id_product_attribute
            FROM ' . _DB_PREFIX_ . 'product_attribute pa
            JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
            JOIN ' . _DB_PREFIX_ . 'attribute a ON pac.id_attribute = a.id_attribute
            JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON a.id_attribute = al.id_attribute
            WHERE pa.id_product_attribute = ' . $product_v_txt . '
            AND al.id_lang = ' . (int)$this->context->language->id;

            $result = Db::getInstance()->executeS($sql);
            if (sizeof($result) > 0) {
                $jet_product_vp = ' - ';
                foreach ($result as $row) {
                    $jet_product_vp .= $row['attribute_name'] . ', ';
                }
            } else {
                $jet_product_vp = '';
            }
            $jet_product_vp = substr($jet_product_vp, 0, -2);

            $body .= "Тип стока: " . $product_c_txt . ";\r\n";
            $body .= "Марка: " . "(" . $jet_product_id . ") " . $product_m_txt .  $jet_product_vp . ";\r\n";
            $body .= "Единична цена в " . $jet_sign . " с ДДС: " . number_format($product_p_txt, 2, ".", "") . ";\r\n";
            $body .= "Брой стоки: " . $product_q_txt . ";\r\n";
            $body .= "Обща сума в " . $jet_sign . " с ДДС: " . number_format((float)$product_q_txt * (float)$product_p_txt, 2, ".", "") . ";\r\n\r\n";
        }

        if ($jet_card == 1) {
            $body .= "Тип стока: Кредитна Карта;\r\n";
            $body .= "Марка: -;\r\n";
            $body .= "Единична цена в " . $jet_sign . " с ДДС: 0.00;\r\n";
            $body .= "Брой стоки: 1;\r\n";
            $body .= "Обща сума в " . $jet_sign . " с ДДС: 0.00;\r\n\r\n";
        }

        $body .= "Данни за кредита:\r\n";

        $body .= "Размер на кредита в " . $jet_sign . ": " . number_format($jet_priceall - $jet_parva, 2, '.', '') . ";\r\n";
        $body .= "Срок на изплащане в месеца: $jet_vnoski;\r\n";
        $body .= "Месечна вноска в " . $jet_sign . ": $jet_vnoska;\r\n";
        $body .= "Първоначална вноска в " . $jet_sign . ": " . number_format(floatval($jet_parva), 2, ".", "") . ";\r\n";

        $jetcredit_id = (string)Configuration::get('JET_ID');
        $creditjet_count = (int)Configuration::get('JET_COUNT');
        Configuration::updateValue('JET_COUNT', $creditjet_count + 1);
        $subject = $jetcredit_id . ", онлайн заявка по поръчка - $creditjet_count";

        $psMailType = intval(Configuration::get('PS_MAIL_TYPE'));
        $psMailSubjectPrefix = Configuration::get('PS_MAIL_SUBJECT_PREFIX');
        Configuration::updateValue('PS_MAIL_TYPE', 2);
        Configuration::updateValue('PS_MAIL_SUBJECT_PREFIX', false);

        $fromName = (string)Configuration::get('PS_SHOP_NAME');
        $fromEmail = (string)Configuration::get('PS_SHOP_EMAIL');
        $toName_admin = (string)Configuration::get('PS_SHOP_NAME');
        $toEmail_admin = (string)Configuration::get('PS_SHOP_EMAIL');
        $toEmail_other = (string)Configuration::get('JET_EMAIL');
        $file_attachment = null;
        $mode_smtp = null;
        $die = false;
        $id_shop = null;
        $bcc = null;
        $replyTo = $jet_email;
        $replyToName = $jet_name . ' ' . $jet_lastname;

        $toml = explode(",", str_replace(' ', '', $toEmail_other));

        $resultMail = Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')),
            'ordersend',
            $subject,
            ['{message}' => $body],
            $toEmail_admin,
            $toName_admin,
            $fromEmail,
            $fromName,
            $file_attachment,
            $mode_smtp,
            _PS_MODULE_DIR_ . 'creditjet/mails',
            $die,
            $id_shop,
            $bcc,
            $replyTo,
            $replyToName
        );

        foreach ($toml as $mail_address) {
            $resultMail = Mail::Send(
                (int)(Configuration::get('PS_LANG_DEFAULT')),
                'ordersend',
                $subject,
                ['{message}' => $body],
                $mail_address,
                null,
                $fromEmail,
                $fromName,
                $file_attachment,
                $mode_smtp,
                _PS_MODULE_DIR_ . 'creditjet/mails',
                $die,
                $id_shop,
                null,
                null,
                null
            );
        }
        Configuration::updateValue('PS_MAIL_TYPE', $psMailType);
        Configuration::updateValue('PS_MAIL_SUBJECT_PREFIX', $psMailSubjectPrefix);

        if ($resultMail) {
            //create order
            $jetCustomer = $this->context->customer;
            if ($jetCustomer->isLogged()) {
                $cart = new Cart();
                $cart->id_customer = (int)$jetCustomer->id;
                $cart->id_address_delivery = (int)$jetCustomer->getAddresses($this->context->language->id)[0]['id_address'];
                $cart->id_address_invoice = (int)$jetCustomer->getAddresses($this->context->language->id)[0]['id_address'];
                $cart->id_currency = (int)Currency::getIdByIsoCode($jet_order_currency);
                $cart->id_carrier = 1;
                $cart->recyclable = 0;
                $cart->gift = 0;
                $cart->add();

                for ($index = 0; $index < sizeof($_product); $index++) {
                    if (isset($product_q[$index]) && (int)$product_q[$index] != 0) {
                        $jet_quantity = (int)$product_q[$index];
                    } else {
                        $jet_quantity = 1;
                    }
                    if (isset($product_v[$index]) && (int)$product_v[$index] != 0) {
                        $jet_product_attribute_id = (int)$product_v[$index];
                    } else {
                        $jet_product_attribute_id = 0;
                    }
                    $cart->updateQty($jet_quantity, (int)$_product[$index], $jet_product_attribute_id);
                }

                $this->context->cart = $cart;
                $this->context->customer = $jetCustomer;
                $this->context->currency = new Currency($cart->id_currency);
                $this->context->language = new Language($cart->id_lang);
                $this->context->country = new Country($this->context->country->id);

                $payment_module = \Module::getInstanceByName('creditjet');
                if (!$payment_module) {
                    die('Payment module not found');
                }

                $payment_module->validateOrder(
                    (int)$cart->id,
                    Configuration::get('PS_OS_PAYMENT'),
                    (float)$cart->getOrderTotal(true, Cart::BOTH),
                    $jet_card == 1 ? $payment_module->displayName_card : $payment_module->displayName,
                    null,
                    [],
                    (int)$cart->id_currency,
                    false,
                    $jetCustomer->secure_key
                );

                $order = Order::getByCartId((int)$cart->id);
                if (!$order) {
                    $json['success'] = 'error_order_not_created';
                }
            }
            $this->context->cart->delete();
            $this->context->cookie->id_cart = 0;

            $json['success'] = 'success';
        } else {
            $json['success'] = 'unsuccess';
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($json));
    }
}
