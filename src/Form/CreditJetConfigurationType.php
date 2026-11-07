<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * CreditJet Configuration Form Type
 *
 * Defines the form structure for CreditJet module configuration in the admin panel.
 * This form includes settings for enabling/disabling the module, configuring interest rates,
 * setting up email addresses, store identifiers, and various display options.
 */
class CreditJetConfigurationType extends TranslatorAwareType
{
    /**
     * Build the CreditJet configuration form
     *
     * Creates all form fields for the CreditJet module configuration including:
     * - Module enable/disable switch
     * - Email and store identifier settings
     * - Interest rate configurations for regular and card payments
     * - Default installment period settings
     * - Display options (cart button, card payment button)
     * - UI customization (spacing, labels, minimum amounts)
     * - Currency operation mode settings
     *
     * @param FormBuilderInterface $builder Form builder instance
     * @param array $options Form options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jet_status_in', SwitchType::class, [
                'label' => $this->trans('Enable module', 'Modules.Creditjet.Admin'),
                'help'  => $this->trans('Show credit purchase buttons through PB Personal Finance', 'Modules.Creditjet.Admin'),
            ])

            ->add('jet_email', TextType::class, [
                'label'    => $this->trans('Select email for sending', 'Modules.Creditjet.Admin'),
                'help'     => $this->trans(
                    'Select email address where customer requests will be sent. Use "pf-online.shop@postbank.bg" to send requests to PB Personal Finance. You can enter multiple email addresses separated by commas. (If left empty, the system default email will be used. It is recommended to use an email outside your domain to avoid auto spam protection.)',
                    'Modules.Creditjet.Admin'
                ),
                'required' => true,
            ])

            ->add('jet_id', TextType::class, [
                'label'    => $this->trans('Select store identifier for sending', 'Modules.Creditjet.Admin'),
                'help'     => $this->trans(
                    'Select store identifier to be sent to the Bank along with the leasing request. The Bank will identify your requests by this identifier',
                    'Modules.Creditjet.Admin'
                ),
                'required' => true,
            ])

            ->add('jet_purcent', ChoiceType::class, [
                'label'      => $this->trans('Interest rate percentage selection', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans('Interest rate percentage selection', 'Modules.Creditjet.Admin'),
                'required'   => true,
                'choice_translation_domain' => false,
                'choices'    => [
                    '0.00%' => 0.00,
                    '0.80%' => 0.80,
                    '0.99%' => 0.99,
                    '1.00%' => 1.00,
                    '1.10%' => 1.10,
                    '1.20%' => 1.20,
                    '1.40%' => 1.40,
                ],
                'empty_data' => 1.40,
            ])

            ->add('jet_vnoski_default', ChoiceType::class, [
                'label'      => $this->trans('Default number of installments selection', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans('Default number of installments selection on product page', 'Modules.Creditjet.Admin'),
                'required'   => true,
                'choices'    => [
                    $this->trans('3 months', 'Modules.Creditjet.Admin')  => 3,
                    $this->trans('6 months', 'Modules.Creditjet.Admin')  => 6,
                    $this->trans('9 months', 'Modules.Creditjet.Admin')  => 9,
                    $this->trans('10 months', 'Modules.Creditjet.Admin') => 10,
                    $this->trans('12 months', 'Modules.Creditjet.Admin') => 12,
                    $this->trans('15 months', 'Modules.Creditjet.Admin') => 15,
                    $this->trans('18 months', 'Modules.Creditjet.Admin') => 18,
                    $this->trans('24 months', 'Modules.Creditjet.Admin') => 24,
                    $this->trans('30 months', 'Modules.Creditjet.Admin') => 30,
                    $this->trans('36 months', 'Modules.Creditjet.Admin') => 36,
                ],
                'empty_data' => 12,
            ])

            ->add('jet_cart_show', SwitchType::class, [
                'label' => $this->trans('Show button in cart', 'Modules.Creditjet.Admin'),
                'help'  => $this->trans('Show credit purchase button through PB Personal Finance in cart', 'Modules.Creditjet.Admin'),
            ])

            ->add('jet_card_in', SwitchType::class, [
                'label' => $this->trans('Show button for sending via credit card', 'Modules.Creditjet.Admin'),
                'help'  => $this->trans(
                    'Show credit purchase button through PB Personal Finance with leasing request made via credit card method',
                    'Modules.Creditjet.Admin'
                ),
            ])

            ->add('jet_purcent_card', ChoiceType::class, [
                'label'      => $this->trans('Interest rate percentage via credit card', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans(
                    'Selection of interest rate percentage table for leasing request made via credit card method',
                    'Modules.Creditjet.Admin'
                ),
                'required'   => true,
                'choice_translation_domain' => false,
                'choices'    => [
                    '0.00%' => 0.00,
                    '0.80%' => 0.80,
                    '0.90%' => 0.90,
                    '0.99%' => 0.99,
                    '1.00%' => 1.00,
                    '1.10%' => 1.10,
                    '1.20%' => 1.20,
                    '1.40%' => 1.40,
                ],
                'empty_data' => 1.00,
            ])

            ->add('jet_count', TextType::class, [
                'label'    => $this->trans('Order №', 'Modules.Creditjet.Admin'),
                'help'     => $this->trans('Current leasing order number', 'Modules.Creditjet.Admin'),
                'disabled' => true,
                'required' => false,
            ])

            ->add('jet_gap', NumberType::class, [
                'label'      => $this->trans('Empty space above button', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans(
                    'Empty space above button in px. Used for button arrangement when there are more than one',
                    'Modules.Creditjet.Admin'
                ),
                'required'   => false,
                'empty_data' => 0,
            ])

            ->add('jet_z1', TextType::class, [
                'label'    => $this->trans('Block title', 'Modules.Creditjet.Admin'),
                'help'     => $this->trans(
                    'Bold text above the button. If left empty, the text will not be displayed',
                    'Modules.Creditjet.Admin'
                ),
                'required' => false,
            ])

            ->add('jet_vnoska', SwitchType::class, [
                'label' => $this->trans('Show installment', 'Modules.Creditjet.Admin'),
                'help'  => $this->trans(
                    'Whether to show text to the right of the button indicating the monthly installment for the selected leasing period?',
                    'Modules.Creditjet.Admin'
                ),
            ])

            ->add('jet_minprice', NumberType::class, [
                'label'      => $this->trans('Minimum amount', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans(
                    'Minimum possible amount of goods for credit purchase through PB Personal Finance',
                    'Modules.Creditjet.Admin'
                ),
                'required'   => true,
                'empty_data' => 150,
            ])

            ->add('jet_eur', ChoiceType::class, [
                'label'      => $this->trans('Currency operation mode selection', 'Modules.Creditjet.Admin'),
                'help'       => $this->trans(
                    'Currency operation mode selection. Option to display in euro or leva. Sending requests in euro or leva with currency conversion if necessary',
                    'Modules.Creditjet.Admin'
                ),
                'required'   => true,
                'choices'    => [
                    $this->trans('Single visualization in leva and sending requests in leva', 'Modules.Creditjet.Admin') => 0,
                    $this->trans('Double visualization leva/euro and sending requests in leva', 'Modules.Creditjet.Admin') => 1,
                    $this->trans('Double visualization euro/leva and sending requests in euro', 'Modules.Creditjet.Admin') => 2,
                    $this->trans('Single visualization in euro and sending requests in euro', 'Modules.Creditjet.Admin') => 3,
                ],
                'empty_data' => 0,
            ]);
    }
}
