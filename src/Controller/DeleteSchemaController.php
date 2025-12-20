<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use PrestaShop\Module\CreditJet\Model\JetCreditModel;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling AJAX requests for deleting credit schemes
 */
class DeleteSchemaController extends PrestaShopAdminController
{
    /**
     * Handle AJAX request for deleting credit scheme
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
