<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use PrestaShop\Module\CreditJet\Model\JetCreditModel;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Delete Schema Controller
 *
 * Handles AJAX requests for deleting credit schemes from the CreditJet module.
 * This controller provides endpoints for removing product-specific credit configurations
 * and promotional settings through the admin interface.
 *
 * @extends PrestaShopAdminController
 */
class DeleteSchemaController extends PrestaShopAdminController
{
    /**
     * Handle AJAX request for deleting credit scheme
     *
     * Processes POST requests to delete credit schemes for specific products.
     * Validates the request method, sanitizes input data, and uses JetCreditModel
     * to remove credit configurations from the database.
     *
     * Expected POST parameters:
     * - jet_product_id: The product identifier for which to delete credit schemes
     *
     * Returns JSON response with success status and message.
     *
     * @param Request $request The HTTP request containing POST data
     *
     * @return Response JSON response with operation result
     *
     * @throws \Exception If database operation fails
     */
    public function delete(Request $request): Response
    {
        // Check if request is POST
        if (!$request->isMethod('POST')) {
            return new Response(json_encode([
                'success' => 'unsuccess',
                'text' => $this->trans('Invalid request method', [], 'Modules.Creditjet.Admin')
            ]), 200, ['Content-Type' => 'application/json']);
        }

        try {
            // Get POST data with sanitization
            $jet_product_id = filter_var($request->request->get('jet_product_id', ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Validate required fields
            if (empty($jet_product_id)) {
                return new Response(json_encode([
                    'success' => 'unsuccess',
                    'text' => $this->trans('Product ID is required', [], 'Modules.Creditjet.Admin')
                ]), 200, ['Content-Type' => 'application/json']);
            }

            // Try to delete the scheme using JetCreditModel
            $result = JetCreditModel::deleteByJetProductId($jet_product_id);

            if ($result) {
                return new Response(json_encode([
                    'success' => 'success',
                    'text' => $this->trans('Credit scheme deleted successfully', [], 'Modules.Creditjet.Admin')
                ]), 200, ['Content-Type' => 'application/json']);
            } else {
                return new Response(json_encode([
                    'success' => 'unsuccess',
                    'text' => $this->trans('Failed to delete credit scheme', [], 'Modules.Creditjet.Admin')
                ]), 200, ['Content-Type' => 'application/json']);
            }
        } catch (\Exception $e) {
            return new Response(json_encode([
                'success' => 'unsuccess',
                'text' => $this->trans('An error occurred: %error%', ['%error%' => $e->getMessage()], 'Modules.Creditjet.Admin')
            ]), 200, ['Content-Type' => 'application/json']);
        }
    }
}
