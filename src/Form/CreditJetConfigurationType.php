<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreditJetConfigurationType extends TranslatorAwareType
{
    private const DOMAIN = 'Modules.Creditjet.Admin';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jet_status_in', SwitchType::class, [
                'label' => $this->trans('Enable module', self::DOMAIN),
                'help'  => $this->trans('Show credit purchase buttons through PB Personal Finance', self::DOMAIN),
            ])

            ->add('jet_email', TextType::class, [
                'label'    => $this->trans('Select email for sending', self::DOMAIN),
                'help'     => $this->trans(
                    'Select email address where customer requests will be sent. Use "online.shop@pbpf.bg" to send requests to PB Personal Finance. You can enter multiple email addresses separated by commas. (If left empty, the system default email will be used. It is recommended to use an email outside your domain to avoid auto spam protection.)',
                    self::DOMAIN
                ),
                'required' => true,
            ])

            ->add('jet_id', TextType::class, [
                'label'    => $this->trans('Select store identifier for sending', self::DOMAIN),
                'help'     => $this->trans(
                    'Select store identifier to be sent to the Bank along with the leasing request. The Bank will identify your requests by this identifier',
                    self::DOMAIN
                ),
                'required' => true,
            ])

            ->add('jet_purcent', ChoiceType::class, [
                'label'      => $this->trans('Interest rate percentage selection', self::DOMAIN),
                'help'       => $this->trans('Interest rate percentage selection', self::DOMAIN),
                'required'   => true,
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
                'label'      => $this->trans('Default number of installments selection', self::DOMAIN),
                'help'       => $this->trans('Default number of installments selection on product page', self::DOMAIN),
                'required'   => true,
                'choices'    => [
                    $this->trans('3 months', self::DOMAIN)  => 3,
                    $this->trans('6 months', self::DOMAIN)  => 6,
                    $this->trans('9 months', self::DOMAIN)  => 9,
                    $this->trans('12 months', self::DOMAIN) => 12,
                    $this->trans('15 months', self::DOMAIN) => 15,
                    $this->trans('18 months', self::DOMAIN) => 18,
                    $this->trans('24 months', self::DOMAIN) => 24,
                    $this->trans('30 months', self::DOMAIN) => 30,
                    $this->trans('36 months', self::DOMAIN) => 36,
                ],
                'empty_data' => 12,
            ])

            ->add('jet_cart_show', SwitchType::class, [
                'label' => $this->trans('Show button in cart', self::DOMAIN),
                'help'  => $this->trans('Show credit purchase button through PB Personal Finance in cart', self::DOMAIN),
            ])

            ->add('jet_card_in', SwitchType::class, [
                'label' => $this->trans('Show button for sending via credit card', self::DOMAIN),
                'help'  => $this->trans(
                    'Show credit purchase button through PB Personal Finance with leasing request made via credit card method',
                    self::DOMAIN
                ),
            ])

            ->add('jet_purcent_card', ChoiceType::class, [
                'label'      => $this->trans('Interest rate percentage via credit card', self::DOMAIN),
                'help'       => $this->trans(
                    'Selection of interest rate percentage table for leasing request made via credit card method',
                    self::DOMAIN
                ),
                'required'   => true,
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
                'label'    => $this->trans('Order №', self::DOMAIN),
                'help'     => $this->trans('Current leasing order number', self::DOMAIN),
                'disabled' => true,
                'required' => false,
            ])

            ->add('jet_gap', NumberType::class, [
                'label'      => $this->trans('Empty space above button', self::DOMAIN),
                'help'       => $this->trans(
                    'Empty space above button in px. Used for button arrangement when there are more than one',
                    self::DOMAIN
                ),
                'required'   => false,
                'empty_data' => 0,
            ])

            ->add('jet_z1', TextType::class, [
                'label'    => $this->trans('Block title', self::DOMAIN),
                'help'     => $this->trans(
                    'Bold text above the button. If left empty, the text will not be displayed',
                    self::DOMAIN
                ),
                'required' => false,
            ])

            ->add('jet_vnoska', SwitchType::class, [
                'label' => $this->trans('Show installment', self::DOMAIN),
                'help'  => $this->trans(
                    'Whether to show text to the right of the button indicating the monthly installment for the selected leasing period?',
                    self::DOMAIN
                ),
            ])

            ->add('jet_minprice', NumberType::class, [
                'label'      => $this->trans('Minimum amount', self::DOMAIN),
                'help'       => $this->trans(
                    'Minimum possible amount of goods for credit purchase through PB Personal Finance',
                    self::DOMAIN
                ),
                'required'   => true,
                'unit'       => $this->trans('BGN', self::DOMAIN),
                'empty_data' => 150,
            ])

            ->add('jet_eur', ChoiceType::class, [
                'label'      => $this->trans('Currency operation mode selection', self::DOMAIN),
                'help'       => $this->trans(
                    'Currency operation mode selection. Option to display in euro or leva. Sending requests in euro or leva with currency conversion if necessary',
                    self::DOMAIN
                ),
                'required'   => true,
                'choices'    => [
                    $this->trans('Single visualization in leva and sending requests in leva', self::DOMAIN) => 0,
                    $this->trans('Double visualization leva/euro and sending requests in leva', self::DOMAIN) => 1,
                    $this->trans('Double visualization euro/leva and sending requests in euro', self::DOMAIN) => 2,
                    $this->trans('Single visualization in euro and sending requests in euro', self::DOMAIN) => 3,
                ],
                'empty_data' => 0,
            ]);
    }
}
