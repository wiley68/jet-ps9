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
    public function __construct(
        private readonly Handler $configurationFormHandler
    ) {
        // empty
    }

    public function index(Request $request): Response
    {
        $textForm = $this->configurationFormHandler->getForm();
        $textForm->handleRequest($request);

        if ($textForm->isSubmitted() && $textForm->isValid()) {
            $errors = $this->configurationFormHandler->save($textForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Modules.Creditjet.Admin'));
                return $this->redirectToRoute('creditjet_configuration');
            }

            $this->flashErrors($errors);
        }

        $context = Context::getContext();
        $link_to_create_schema_creditjet = $this->generateUrl('credit_jet_create_schema');
        $link_to_delete_schema_creditjet = $this->generateUrl('credit_jet_delete_schema');
        $creditjet_schemes = $this->getAllJetKopRows();

        return $this->render('@Modules/creditjet/views/templates/admin/form.html.twig', [
            'creditJetConfigurationForm' => $textForm->createView(),
            'link_to_create_schema_creditjet' => $link_to_create_schema_creditjet,
            'link_to_delete_schema_creditjet' => $link_to_delete_schema_creditjet,
            'creditjet_schemes' => $creditjet_schemes,
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
