<?php

/**
 * CreditJet Validation Controller
 * Handles payment validation and processing
 */

class CreditJetValidationModuleFrontController extends ModuleFrontController
{
    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();

        // Check if cart exists
        if (!$this->context->cart->id) {
            Tools::redirect('index.php?controller=order');
        }

        // Check if customer is logged in
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('creditjet', 'validation')));
        }

        // Check if module is active and configured
        if (!$this->module->active || !$this->module->isConfigured()) {
            $this->errors[] = $this->module->l('Модулът за плащане не е активен или конфигуриран.');
            $this->redirectWithNotifications($this->context->link->getPageLink('order', true, null, ['step' => 3]));
        }

        // Validate cart
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check currency
        if (!$this->module->checkCurrency($cart->id_currency)) {
            $this->errors[] = $this->module->l('Валутата не се поддържа от този метод на плащане.');
            $this->redirectWithNotifications($this->context->link->getPageLink('order', true, null, ['step' => 3]));
        }

        // Get order total
        $total = $cart->getOrderTotal(true, Cart::BOTH);

        // Check amount limits
        $minAmount = Configuration::get('CREDITJET_MIN_AMOUNT');
        $maxAmount = Configuration::get('CREDITJET_MAX_AMOUNT');

        if ($total < $minAmount || $total > $maxAmount) {
            $this->errors[] = sprintf(
                $this->module->l('Сумата %.2f %s е извън позволените лимити (%.2f - %.2f %s).'),
                $total,
                $this->context->currency->iso_code,
                $minAmount,
                $maxAmount,
                $this->context->currency->iso_code
            );
            $this->redirectWithNotifications($this->context->link->getPageLink('order', true, null, ['step' => 3]));
        }

        // Process payment
        $this->processPayment();
    }

    /**
     * Process the payment
     */
    protected function processPayment()
    {
        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);

        // Validate order
        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_PREPARATION'), // Order status: Preparation in progress
            $cart->getOrderTotal(true, Cart::BOTH),
            $this->module->displayName,
            null,
            [],
            (int)$this->context->currency->id,
            false,
            $customer->secure_key
        );

        // Redirect to order confirmation
        Tools::redirect(
            $this->context->link->getPageLink(
                'order-confirmation',
                true,
                null,
                [
                    'id_cart' => $cart->id,
                    'id_module' => $this->module->id,
                    'id_order' => $this->module->currentOrder,
                    'key' => $customer->secure_key
                ]
            )
        );
    }

    /**
     * Set media (CSS/JS)
     */
    public function setMedia()
    {
        parent::setMedia();

        // Add custom CSS if needed
        $this->addCSS(_PS_MODULE_DIR_ . 'creditjet/views/css/front.css');

        // Add custom JS if needed
        $this->addJS(_PS_MODULE_DIR_ . 'creditjet/views/js/front.js');
    }
}
