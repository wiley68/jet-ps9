<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use PrestaShop\Module\CreditJet\Model\JetCreditModel;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;

/**
 * Controller for handling AJAX requests for creating credit schemes
 */
class CreateSchemaController extends PrestaShopAdminController
{
    private const DOMAIN = 'Modules.Creditjet.Admin';

    /**
     * Handle AJAX request for creating credit scheme
     */
    public function create(Request $request): Response
    {
        // Check if request is POST
        if (!$request->isMethod('POST')) {
            return new Response(json_encode([
                'success' => 'error',
                'text' => $this->trans('Invalid request method', [], self::DOMAIN)
            ]), 200, ['Content-Type' => 'application/json']);
        }

        try {
            // Get POST data with sanitization
            $jet_product_id = filter_var($request->request->get('jet_product_id', ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $jet_product_percent = filter_var($request->request->get('jet_product_percent', -1), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $jet_product_meseci = filter_var($request->request->get('jet_product_meseci', ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $jet_product_price = filter_var($request->request->get('jet_product_price', 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $jet_product_start = filter_var($request->request->get('jet_product_start', ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $jet_product_end = filter_var($request->request->get('jet_product_end', ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Validate required fields
            if (empty($jet_product_id) || empty($jet_product_meseci) || $jet_product_price === '' || empty($jet_product_start) || empty($jet_product_end)) {
                return new Response(json_encode([
                    'success' => 'unsuccess',
                    'text' => $this->trans('All fields are required', [], self::DOMAIN)
                ]), 200, ['Content-Type' => 'application/json']);
            }

            // Validate data types
            if (!is_numeric($jet_product_percent) || !is_numeric($jet_product_price)) {
                return new Response(json_encode([
                    'success' => 'unsuccess',
                    'text' => $this->trans('Invalid numeric values', [], self::DOMAIN)
                ]), 200, ['Content-Type' => 'application/json']);
            }

            // Try to create the scheme using JetCreditModel
            $result = JetCreditModel::insertIfNotExists(
                $jet_product_id,
                $jet_product_percent,
                $jet_product_meseci,
                $jet_product_price,
                $jet_product_start,
                $jet_product_end
            );

            if ((int)$result === -1) {
                // Scheme already exists
                return new Response(json_encode([
                    'success' => 'success',
                    'text' => $this->trans('You already have such a filter in place!', [], self::DOMAIN)
                ]), 200, ['Content-Type' => 'application/json']);
            } elseif ((int)$result === 0) {
                // Database error
                return new Response(json_encode([
                    'success' => 'unsuccess',
                    'text' => $this->trans('You cannot create the filter.', [], self::DOMAIN)
                ]), 200, ['Content-Type' => 'application/json']);
            } else {
                // Success
                return new Response(json_encode([
                    'success' => 'success',
                    'text' => $this->trans('You have successfully written the filter.', [], self::DOMAIN)
                ]), 200, ['Content-Type' => 'application/json']);
            }
        } catch (\Exception $e) {
            return new Response(json_encode([
                'success' => 'unsuccess',
                'text' => $this->trans('An error occurred: %error%', ['%error%' => $e->getMessage()], self::DOMAIN)
            ]), 200, ['Content-Type' => 'application/json']);
        }
    }
}
