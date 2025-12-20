<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Controller;

use Context;
use Db;
use DbQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreditJetConfigurationController extends \FrameworkBundleAdminController
{

    public function index(Request $request): Response
    {
        $textFormDataHandler = $this->get('prestashop.module.creditjet.creditjet_configuration_form_handler');

        $textForm = $textFormDataHandler->getForm();
        $textForm->handleRequest($request);

        if ($textForm->isSubmitted() && $textForm->isValid()) {
            $errors = $textFormDataHandler->save($textForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', 'Успешна актуализация');

                return $this->redirectToRoute('credit_jet_configuration_form');
            }

            $this->flashErrors($errors);
        }

        $context = Context::getContext();
        $link_to_create_schema_creditjet = $context->link->getModuleLink('creditjet', 'createschema', []);
        $link_to_delete_schema_creditjet = $context->link->getModuleLink('creditjet', 'deleteschema', []);
        $creditjet_schemes = $this->getAllJetKopRows();

        return $this->render('@Modules/creditjet/views/templates/admin/form.html.twig', [
            'creditJetConfigurationForm' => $textForm->createView(),
            'link_to_create_schema_creditjet' => $link_to_create_schema_creditjet,
            'link_to_delete_schema_creditjet' => $link_to_delete_schema_creditjet,
            'creditjet_schemes' => $creditjet_schemes
        ]);
    }

    public function getAllJetKopRows()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('jet_kop');
        $results = Db::getInstance()->executeS($sql);

        return $results;
    }
}
