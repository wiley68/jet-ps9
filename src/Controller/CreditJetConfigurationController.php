<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use Context;
use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreditJetConfigurationController extends PrestaShopAdminController
{
    private const DOMAIN = 'Modules.Creditjet.Admin';

    public function __construct(
        private readonly Handler $configurationFormHandler
    ) {
        // empty constructor
    }

    public function index(Request $request): Response
    {
        $textForm = $this->configurationFormHandler->getForm();
        $textForm->handleRequest($request);

        if ($textForm->isSubmitted() && $textForm->isValid()) {
            $errors = $this->configurationFormHandler->save($textForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update', [], self::DOMAIN));
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

    private function getAllJetKopRows(): array
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('jet_kop');

        $results = Db::getInstance()->executeS($sql);
        return is_array($results) ? $results : [];
    }
}
