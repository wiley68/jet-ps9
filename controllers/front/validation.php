<?php

/**
 * CreditJet Validation Front Controller
 *
 * Handles payment validation and order processing for the CreditJet payment module.
 * This controller processes credit payment requests, validates customer and cart data,
 * creates orders, and sends notification emails to administrators and PB Personal Finance.
 *
 * @property \Creditjet $module Instance of this payment module (validateOrder, currentOrder, …)
 *
 * @extends ModuleFrontController
 */
class CreditJetValidationModuleFrontController extends ModuleFrontController
{
    /**
     * Process credit payment validation and order creation
     *
     * Handles the complete credit payment flow including:
     * - Input sanitization from POST data (regular or card payments)
     * - Context and payment option validation
     * - Customer validation
     * - Order creation through module
     * - Email notification generation with customer and product details
     * - Email sending to administrators and PB Personal Finance
     * - Redirect to order confirmation page
     *
     * Supports both regular credit payments and credit card payments with different
     * input parameters and processing logic.
     *
     * @return void Redirects to order confirmation or back to order step 1 on errors
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {

        $jet_card = filter_var(Tools::getValue('jet_card', 0), FILTER_SANITIZE_NUMBER_INT);
        $jet_egn = filter_var(Tools::getValue('jet_egn', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_priceall = filter_var(Tools::getValue('jet_priceall_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_parva = filter_var(Tools::getValue('jet_parva_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_vnoska = filter_var(Tools::getValue('jet_vnoska_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_products_id = filter_var(Tools::getValue('jet_products_id', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_qt = filter_var(Tools::getValue('jet_products_qt', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_pr = filter_var(Tools::getValue('jet_products_pr', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_ct = filter_var(Tools::getValue('jet_products_ct', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_products_vr = filter_var(Tools::getValue('jet_products_vr', ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $jet_vnoski = filter_var(Tools::getValue('jet_vnoski_input', (int)Configuration::get("JET_VNOSKI_DEFAULT")), FILTER_SANITIZE_NUMBER_INT);

        $jet_module_name = 'jetcredit';
        if ($jet_card == 1) {
            $jet_module_name = 'jetcreditcard';
            $jet_egn = filter_var(Tools::getValue('jet_card_egn', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_priceall = filter_var(Tools::getValue('jet_card_priceall_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $jet_parva = filter_var(Tools::getValue('jet_card_parva_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $jet_vnoska = filter_var(Tools::getValue('jet_card_vnoska_input', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $jet_products_id = filter_var(Tools::getValue('jet_card_products_id', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_products_qt = filter_var(Tools::getValue('jet_card_products_qt', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_products_pr = filter_var(Tools::getValue('jet_card_products_pr', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_products_ct = filter_var(Tools::getValue('jet_card_products_ct', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_products_vr = filter_var(Tools::getValue('jet_card_products_vr', ''), FILTER_SANITIZE_SPECIAL_CHARS);
            $jet_vnoski = filter_var(Tools::getValue('jet_card_vnoski_input', (int)Configuration::get("JET_VNOSKI_DEFAULT")), FILTER_SANITIZE_NUMBER_INT);
        }

        if (false === $this->checkIfContextIsValid() || false === $this->checkIfPaymentOptionIsAvailable()) {
            Tools::redirect($this->context->link->getPageLink(
                'order',
                true,
                (int) $this->context->language->id,
                [
                    'step' => 1
                ]
            ));
        }

        $customer = new Customer($this->context->cart->id_customer);
        if (false === Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink(
                'order',
                true,
                (int) $this->context->language->id,
                [
                    'step' => 1
                ]
            ));
        }

        $jet_currency = $this->context->currency;
        $mailVars = array(
            '{bankwire_owner}' => 'owner',
            '{bankwire_details}' => nl2br('details'),
            '{bankwire_address}' => nl2br('address')
        );

        $this->module->validateOrder(
            (int) $this->context->cart->id,
            Configuration::get('PS_OS_PAYMENT'),
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            $jet_card == 1 ? $this->module->displayName_card : $this->module->displayName,
            null,
            [],
            (int) $this->context->currency->id,
            false,
            $customer->secure_key
        );

        $order_id = $this->module->currentOrder;
        if ($order_id != 0) {
            $jet_eur = (int)Configuration::get("JET_EUR");
            $jet_sign = 'лева';
            $jet_order_currency = 'BGN';
            switch ($jet_eur) {
                case 0:
                    $jet_sign = 'лева';
                    $jet_order_currency = 'BGN';
                    break;
                case 1:
                    $jet_sign = 'лева';
                    $jet_order_currency = 'BGN';
                    break;
                case 2:
                    $jet_sign = 'евро';
                    $jet_order_currency = 'EUR';
                    break;
                case 3:
                    $jet_sign = 'евро';
                    $jet_order_currency = 'EUR';
                    break;
            }

            $jet_name = Creditjet::sanitizeBankField($customer->firstname);
            $jet_lastname = Creditjet::sanitizeBankField($customer->lastname);
            $jet_email = Creditjet::sanitizeBankField($customer->email);
            $jet_phone = Creditjet::sanitizeBankField($customer->getAddresses($this->context->language->id)[0]['phone']);
            $jet_egn = Creditjet::sanitizeBankField($jet_egn);

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

                $product_c_txt = Creditjet::sanitizeBankField($product_c_txt);
                $product_m_txt = Creditjet::sanitizeBankField($product_m_txt);
                $jet_product_vp = Creditjet::sanitizeBankField($jet_product_vp);

                $body .= "Тип стока: " . $product_c_txt . ";\r\n";
                $body .= "Марка: " . "(" . $jet_product_id . ") " . $product_m_txt .  $jet_product_vp . ";\r\n";
                $body .= "Единична цена с ДДС: " . number_format($product_p_txt, 2, ".", "") . ";\r\n";
                $body .= "Брой стоки: " . $product_q_txt . ";\r\n";
                $body .= "Обща сума с ДДС: " . number_format((float)$product_q_txt * (float)$product_p_txt, 2, ".", "") . ";\r\n\r\n";
            }

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
            $body .= "Месечна вноска: " . number_format(floatval($jet_vnoska), 2, '.', '') . ";\r\n";
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
        }

        Tools::redirect($this->context->link->getPageLink(
            'order-confirmation',
            true,
            (int) $this->context->language->id,
            [
                'id_cart' => (int) $this->context->cart->id,
                'id_module' => (int) $this->module->id,
                'id_order' => (int) $this->module->currentOrder,
                'key' => $customer->secure_key,
            ]
        ));
    }

    /**
     * Validate the current context for payment processing
     *
     * Checks if the cart, customer, delivery address, and invoice address are valid,
     * and ensures the cart is not virtual (contains physical products).
     *
     * @return bool True if context is valid for payment processing, false otherwise
     */
    private function checkIfContextIsValid()
    {
        return true === Validate::isLoadedObject($this->context->cart)
            && true === Validate::isUnsignedInt($this->context->cart->id_customer)
            && true === Validate::isUnsignedInt($this->context->cart->id_address_delivery)
            && true === Validate::isUnsignedInt($this->context->cart->id_address_invoice)
            && false === $this->context->cart->isVirtualCart();
    }

    /**
     * Check if CreditJet payment option is available
     *
     * Verifies that the creditjet module is active and available
     * among the installed payment modules.
     *
     * @return bool True if CreditJet payment is available, false otherwise
     */
    private function checkIfPaymentOptionIsAvailable()
    {
        $modules = Module::getPaymentModules();

        if (empty($modules)) {
            return false;
        }

        foreach ($modules as $module) {
            if (isset($module['name']) && 'creditjet' === $module['name']) {
                return true;
            }
        }

        return false;
    }
}
