<?php

/**
 * CreditJet Send Front Controller
 *
 * Handles AJAX requests for sending single product credit applications.
 * This controller processes credit requests for individual products from the frontend,
 * validates input, generates email notifications, and creates orders for credit applications.
 *
 * Similar to JetSendCart but focused on single product purchases rather than entire carts.
 * Supports both regular credit and credit card payment requests.
 *
 * @property \Cart $cart
 * @property \Currency $currency
 * @property \Language $language
 * @property \Country $country
 * @property \Order $order
 *
 * @method static \Module getInstanceByName(string $module_name)
 *
 * @extends ModuleFrontController
 */
class CreditJetJetSendModuleFrontController extends ModuleFrontController
{

    /**
     * Process AJAX request for single product credit application
     *
     * Handles credit application submission for individual products including:
     * - Security validation (AJAX requests only, CSRF protection)
     * - Input sanitization for customer and credit data
     * - Product information retrieval and formatting
     * - Email content generation with customer and single product details
     * - Email sending to administrators and PB Personal Finance
     * - Cart creation and order processing for successful applications
     * - JSON response generation for frontend feedback
     *
     * Supports both regular credit and credit card applications.
     * Creates a cart with a single product for order processing.
     *
     * Expected input parameters include customer info, credit details,
     * and single product information (ID, quantity, attributes).
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
        $jet_product_id = filter_var(Tools::getValue('jet_product_id', 0), FILTER_SANITIZE_NUMBER_INT);
        $jet_quantity = filter_var(Tools::getValue('jet_quantity', 1), FILTER_SANITIZE_NUMBER_INT);
        $jet_product_attribute_id = filter_var(Tools::getValue('jet_product_attribute_id', 0), FILTER_SANITIZE_NUMBER_INT);

        $product = new Product($jet_product_id, false, $this->context->language->id);
        $product_c_id = (int)$product->id_category_default;
        $cat = new Category($product_c_id, $this->context->language->id);
        if (isset($cat) && $cat->name) {
            $product_c_txt = $cat->name;
        } else {
            $product_c_txt = ' - ';
        }
        $product_m_txt = $product->name;
        if ($product_m_txt == '') {
            $product_m_txt = ' - ';
        }
        $product_p_txt = $jet_priceall / $jet_quantity;

        $sql = 'SELECT al.name AS attribute_name, pa.id_product_attribute
        FROM ' . _DB_PREFIX_ . 'product_attribute pa
        JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
        JOIN ' . _DB_PREFIX_ . 'attribute a ON pac.id_attribute = a.id_attribute
        JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON a.id_attribute = al.id_attribute
        WHERE pa.id_product_attribute = ' . (int)$jet_product_attribute_id . '
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

        $body .= "Тип стока: " . $product_c_txt . ";\r\n";
        $body .= "Марка: " . "(" . $jet_product_id . ") " . $product_m_txt .  $jet_product_vp . ";\r\n";
        $body .= "Единична цена с ДДС: " . number_format($product_p_txt, 2, ".", "") . ";\r\n";
        $body .= "Брой стоки: " . $jet_quantity . ";\r\n";
        $body .= "Обща сума с ДДС: " . number_format($jet_priceall, 2, ".", "") . ";\r\n\r\n";

        if ($jet_card == 1) {
            $body .= "Тип стока: Кредитна Карта;\r\n";
            $body .= "Марка: -;\r\n";
            $body .= "Единична цена с ДДС: 0.00;\r\n";
            $body .= "Брой стоки: 1;\r\n";
            $body .= "Обща сума с ДДС: 0.00;\r\n\r\n";
        }

        $body .= "Данни за кредита:\r\n";

        $body .= "Размер на кредита: " . number_format($jet_priceall - $jet_parva, 2, '.', '') . ";\r\n";
        $body .= "Срок на изплащане в месеца: $jet_vnoski;\r\n";
        $body .= "Месечна вноска: $jet_vnoska;\r\n";
        $body .= "Първоначална вноска: " . number_format(floatval($jet_parva), 2, ".", "") . ";\r\n";

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
                $cart = new \Cart();
                $cart->id_customer = (int)$jetCustomer->id;
                $cart->id_address_delivery = (int)$jetCustomer->getAddresses($this->context->language->id)[0]['id_address'];
                $cart->id_address_invoice = (int)$jetCustomer->getAddresses($this->context->language->id)[0]['id_address'];
                $cart->id_currency = (int)Currency::getIdByIsoCode($jet_order_currency);
                $cart->id_carrier = 1;
                $cart->recyclable = 0;
                $cart->gift = 0;
                $cart->add();

                $product = new Product($jet_product_id);
                $cart->updateQty($jet_quantity, (int)$product->id, $jet_product_attribute_id);

                $this->context->cart = $cart;
                $this->context->customer = $jetCustomer;
                $this->context->currency = new Currency($cart->id_currency);
                $this->context->language = new Language($cart->id_lang);
                $this->context->country = new Country($this->context->country->id);

                /** @var \Creditjet $payment_module */
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

            $json['success'] = 'success';
        } else {
            $json['success'] = 'unsuccess';
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($json));
    }
}
