<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Model;

use Db;
use DbQuery;
use ObjectModel;
use Configuration;

/**
 * JetCreditModel - Model for working with jet_kop table
 * Handles credit schemes and calculations
 */
class JetCreditModel extends ObjectModel
{
    /**
     * Table name
     */
    public const TABLE_NAME = 'jet_kop';

    public static $definition = [
        'table' => self::TABLE_NAME,
        'primary' => 'id',
        'fields' => [
            'jet_product_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 20],
            'jet_product_percent' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPercentage', 'required' => true],
            'jet_product_meseci' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 50],
            'jet_product_price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'jet_product_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'jet_product_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    public static function insertIfNotExists(
        $jet_product_id,
        $jet_product_percent,
        $jet_product_meseci,
        $jet_product_price,
        $jet_product_start,
        $jet_product_end
    ): bool|int|string {
        $sql = new DbQuery();
        $sql->select('COUNT(*)');
        $sql->from(self::TABLE_NAME);
        $sql->where('jet_product_id = \'' . pSQL($jet_product_id) . '\'');

        if (Db::getInstance()->getValue($sql->build()) == 0) {
            $row = [
                'jet_product_id' => pSQL($jet_product_id),
                'jet_product_percent' => (float)$jet_product_percent,
                'jet_product_meseci' => pSQL($jet_product_meseci),
                'jet_product_price' => (float)$jet_product_price,
                'jet_product_start' => pSQL($jet_product_start),
                'jet_product_end' => pSQL($jet_product_end),
            ];
            return Db::getInstance()->insert(self::TABLE_NAME, $row);
        } else {
            return -1;
        }
    }

    public static function deleteByJetProductId($jet_product_id): bool
    {
        return Db::getInstance()->delete(
            self::TABLE_NAME,
            'jet_product_id = \'' . pSQL($jet_product_id) . '\''
        );
    }

    public static function getPromo($jet_product_id, $jet_vnoski, $jet_total_credit_price): array
    {
        $result = array();
        $jet_card_in = (int) Configuration::get('JET_CARD_IN');
        $jet_purcent = (float) Configuration::get('JET_PURCENT');

        if ($jet_card_in === 1) {
            $jet_purcent_card = (float) Configuration::get('JET_PURCENT_CARD');
        }

        $jet_show_button = true;

        $sql = new DbQuery();
        $sql->select('jet_product_percent');
        $sql->from(self::TABLE_NAME);
        $sql->where('(jet_product_id = \'' . pSQL($jet_product_id) . '\' OR jet_product_id = \'*\')');
        $sql->where('FIND_IN_SET(' . (int)$jet_vnoski . ', REPLACE(jet_product_meseci, "_", ","))');
        $sql->where((float)$jet_total_credit_price . ' >= jet_product_price');
        $sql->where('DATE(jet_product_start) <= CURDATE()');
        $sql->where('DATE(jet_product_end) >= CURDATE()');

        $jet_product_percent = Db::getInstance()->getValue($sql->build());

        if (false !== $jet_product_percent) {
            if ((float) -1.00 === (float) $jet_product_percent) {
                $jet_show_button = false;
            } else {
                $jet_purcent = (float) $jet_product_percent;
                if ($jet_card_in === 1) {
                    $jet_purcent_card = (float) $jet_product_percent;
                }
            }
        }

        $result = [
            'jet_show_button' => $jet_show_button,
            'jet_purcent' => $jet_purcent,
            'jet_purcent_card' => $jet_purcent_card ?? null,
        ];

        return $result;
    }
}
