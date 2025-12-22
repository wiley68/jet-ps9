<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CreditJet Configuration Controller
 *
 * Handles the admin configuration interface for the CreditJet module.
 * Manages form display, processing, and credit scheme management through
 * the PrestaShop admin panel.
 *
 * @extends PrestaShopAdminController
 */
class CreditJetConfigurationController extends PrestaShopAdminController
{
    /**
     * Constructor
     *
     * @param Handler $configurationFormHandler Form handler for processing configuration forms
     */
    public function __construct(
        private readonly Handler $configurationFormHandler
    ) {
        // empty constructor
    }

    /**
     * Display and process the CreditJet configuration form
     *
     * Handles both GET requests (display form) and POST requests (process form submission).
     * Manages form validation, saving, and provides links for credit scheme management.
     * Also retrieves existing credit schemes for display in the admin interface.
     *
     * @param Request $request The HTTP request
     *
     * @return Response The rendered template response
     */
    public function index(Request $request): Response
    {
        $textForm = $this->configurationFormHandler->getForm();
        $textForm->handleRequest($request);

        if ($textForm->isSubmitted() && $textForm->isValid()) {
            $errors = $this->configurationFormHandler->save($textForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Modules.Creditjet.Admin'));
                return $this->redirectToRoute('credit_jet_configuration_form');
            }

            $this->flashErrors($errors);
        }

        $link_to_create_schema_creditjet = $this->generateUrl('creditjet_admin_create_schema');
        $link_to_delete_schema_creditjet = $this->generateUrl('creditjet_admin_delete_schema');
        $creditjet_schemes = $this->getAllJetKopRows();

        // Get JavaScript file modification time for cache busting
        $jsFilePath = _PS_MODULE_DIR_ . 'creditjet/views/js/creditjetForms.js';
        $jsVersion = file_exists($jsFilePath) ? filemtime($jsFilePath) : time();

        return $this->render('@Modules/creditjet/views/templates/admin/form.html.twig', [
            'creditJetConfigurationForm' => $textForm->createView(),
            'link_to_create_schema_creditjet' => $link_to_create_schema_creditjet,
            'link_to_delete_schema_creditjet' => $link_to_delete_schema_creditjet,
            'creditjet_schemes' => $creditjet_schemes,
            'js_version' => $jsVersion,
        ]);
    }

    /**
     * Get all credit scheme rows from jet_kop table
     *
     * Retrieves all credit configuration records from the database
     * for display in the admin interface.
     *
     * @return array Array of credit scheme records or empty array if none found
     */
    private function getAllJetKopRows(): array
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('jet_kop');

        $results = Db::getInstance()->executeS($sql);
        return is_array($results) ? $results : [];
    }
}
