<?php

use PrestaShop\Module\CreditJet\Model\JetCreditModel;
use Creditjet;

/**
 * @method static \Module getInstanceByName(string $module_name)
 */
class CreditJetJetCalculateModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Security check - only allow AJAX requests
        if (!$this->isXmlHttpRequest()) {
            die('Forbidden - AJAX requests only');
        }

        $json = [];

        $jet_priceall = filter_var(Tools::getValue('jet_priceall', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_parva = filter_var(Tools::getValue('jet_parva', 0.00), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jet_vnoski = filter_var(Tools::getValue('jet_vnoski', (int)Configuration::get("JET_VNOSKI_DEFAULT")), FILTER_SANITIZE_NUMBER_INT);
        $jet_product_id = filter_var(Tools::getValue('jet_product_id', ''), FILTER_SANITIZE_STRING);

        $jet_card_in = (int)Configuration::get("JET_CARD_IN");

        $jet_eur = (int)Configuration::get("JET_EUR");
        $jet_min_250 = Creditjet::JET_MIN_250;
        $jet_currency_code = $this->context->currency->iso_code;

        switch ($jet_eur) {
            case 0:
                break;
            case 1:
                if ($jet_currency_code == "EUR") {
                    $jet_priceall = number_format($jet_priceall * 1.95583, 2, ".", "");
                }
                break;
            case 2:
            case 3:
                if ($jet_currency_code == "BGN") {
                    $jet_priceall = number_format($jet_priceall / 1.95583, 2, ".", "");
                }
                $jet_min_250 = Creditjet::JET_MIN_250_EUR;
                break;
        }

        $jet_total_credit_price = (float)$jet_priceall - (float)$jet_parva;

        $jet_purcent = (float)Configuration::get("JET_PURCENT");
        if ($jet_card_in == 1) {
            $jet_purcent_card = (float)Configuration::get("JET_PURCENT_CARD");
        }
        $jet_show_button = true;
        $jet_promo = JetCreditModel::getPromo($jet_product_id, $jet_vnoski, $jet_total_credit_price);
        $jet_show_button = (bool) $jet_promo['jet_show_button'];
        $jet_purcent = (float) $jet_promo['jet_purcent'];
        $jet_purcent_card = (float) $jet_promo['jet_purcent_card'];
        $_minprice = (float)Configuration::get("JET_MINPRICE");
        if ($_minprice > $jet_total_credit_price) {
            $jet_show_button = false;
        }

        $jet_vnoska = (($jet_total_credit_price / $jet_vnoski) * (1 + ($jet_vnoski * $jet_purcent) / 100));
        if ($jet_card_in == 1) {
            $jet_vnoska_card = (($jet_total_credit_price / $jet_vnoski) * (1 + ($jet_vnoski * $jet_purcent_card) / 100));
        }

        $jet_gprm = $this->RATE($jet_vnoski, $jet_vnoska, -1 * $jet_total_credit_price) * 12;
        $jet_glp = ($this->RATE($jet_vnoski, $jet_vnoska, -1 * $jet_total_credit_price) * 12) * 100;
        $jet_gpr = (pow((1 + $jet_gprm / 12), 12) - 1) * 100;
        $jet_obshto = $jet_vnoska * $jet_vnoski;
        if ($jet_card_in == 1) {
            $jet_gprm_card = $this->RATE($jet_vnoski, $jet_vnoska_card, -1 * $jet_total_credit_price) * 12;
            $jet_glp_card = ($this->RATE($jet_vnoski, $jet_vnoska_card, -1 * $jet_total_credit_price) * 12) * 100;
            $jet_gpr_card = (pow((1 + $jet_gprm_card / 12), 12) - 1) * 100;
            $jet_obshto_card = $jet_vnoska_card * $jet_vnoski;
        }

        $jet_vnoska_second = 0;
        $jet_priceall_second = $jet_priceall;
        $jet_total_credit_price_second = $jet_total_credit_price;
        $jet_obshto_second = $jet_obshto;
        if ($jet_card_in == 1) {
            $jet_vnoska_card_second = 0;
            $jet_obshto_card_second = $jet_obshto_card;
        }
        switch ($jet_eur) {
            case 0:
                $jet_vnoska_second = 0;
                $jet_priceall_second = $jet_priceall;
                $jet_total_credit_price_second = $jet_total_credit_price;
                $jet_obshto_second = $jet_obshto;
                if ($jet_card_in == 1) {
                    $jet_vnoska_card_second = 0;
                    $jet_obshto_card_second = $jet_obshto_card;
                }
                break;
            case 1:
                $jet_vnoska_second = number_format($jet_vnoska / 1.95583, 2, ".", "");
                $jet_priceall_second = number_format($jet_priceall / 1.95583, 2, ".", "");
                $jet_total_credit_price_second = number_format($jet_total_credit_price_second / 1.95583, 2, ".", "");
                $jet_obshto_second = number_format($jet_obshto_second / 1.95583, 2, ".", "");
                if ($jet_card_in == 1) {
                    $jet_vnoska_card_second = number_format($jet_vnoska_card / 1.95583, 2, ".", "");
                    $jet_obshto_card_second = number_format($jet_obshto_card_second / 1.95583, 2, ".", "");
                }
                break;
            case 2:
                $jet_vnoska_second = number_format($jet_vnoska * 1.95583, 2, ".", "");
                $jet_priceall_second = number_format($jet_priceall * 1.95583, 2, ".", "");
                $jet_total_credit_price_second = number_format($jet_total_credit_price_second * 1.95583, 2, ".", "");
                $jet_obshto_second = number_format($jet_obshto_second * 1.95583, 2, ".", "");
                if ($jet_card_in == 1) {
                    $jet_vnoska_card_second = number_format($jet_vnoska_card / 1.95583, 2, ".", "");
                    $jet_obshto_card_second = number_format($jet_obshto_card_second / 1.95583, 2, ".", "");
                }
                break;
            case 3:
                $jet_vnoska_second = 0;
                $jet_priceall_second = $jet_priceall;
                $jet_total_credit_price_second = $jet_total_credit_price;
                $jet_obshto_second = $jet_obshto;
                if ($jet_card_in == 1) {
                    $jet_vnoska_card_second = 0;
                    $jet_obshto_card_second = $jet_obshto_card;
                }
                break;
        }

        $json['success'] = 'success';
        $json['jet_show_button'] = $jet_show_button;
        $json['jet_vnoska'] = number_format($jet_vnoska, 2, ".", "");
        $json['jet_vnoska_second'] = number_format($jet_vnoska_second, 2, ".", "");
        $json['jet_priceall'] = number_format($jet_priceall, 2, ".", "");
        $json['jet_priceall_second'] = number_format($jet_priceall_second, 2, ".", "");
        $json['jet_total_credit_price'] = number_format($jet_total_credit_price, 2, ".", "");
        $json['jet_total_credit_price_second'] = number_format($jet_total_credit_price_second, 2, ".", "");
        $json['jet_gpr'] = number_format($jet_gpr, 2, ".", "");
        $json['jet_glp'] = number_format($jet_glp, 2, ".", "");
        $json['jet_obshto'] = number_format($jet_obshto, 2, ".", "");
        $json['jet_obshto_second'] = number_format($jet_obshto_second, 2, ".", "");
        if ($jet_card_in == 1) {
            $json['jet_vnoska_card'] = number_format($jet_vnoska_card, 2, ".", "");
            $json['jet_vnoska_card_second'] = number_format($jet_vnoska_card_second, 2, ".", "");
            $json['jet_gpr_card'] = number_format($jet_gpr_card, 2, ".", "");
            $json['jet_glp_card'] = number_format($jet_glp_card, 2, ".", "");
            $json['jet_obshto_card'] = number_format($jet_obshto_card, 2, ".", "");
            $json['jet_obshto_card_second'] = number_format($jet_obshto_card_second, 2, ".", "");
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($json));
    }

    public function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1)
    {
        $rate = $guess;
        if (abs($rate) < Creditjet::JETCREDIT_FINANCIAL_PRECISION) {
            $y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
        } else {
            $f = exp($nper * log(1 + $rate));
            $y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
        }
        $y0 = $pv + $pmt * $nper + $fv;
        $y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
        $i  = $x0 = 0.0;
        $x1 = $rate;
        while ((abs($y0 - $y1) > Creditjet::JETCREDIT_FINANCIAL_PRECISION) && ($i < Creditjet::JETCREDIT_FINANCIAL_MAX_ITERATIONS)) {
            $rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
            $x0 = $x1;
            $x1 = $rate;
            if (abs($rate) < Creditjet::JETCREDIT_FINANCIAL_PRECISION) {
                $y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
            } else {
                $f = exp($nper * log(1 + $rate));
                $y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
            }
            $y0 = $y1;
            $y1 = $y;
            ++$i;
        }
        return $rate;
    }
}
