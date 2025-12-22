<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Model;

use Db;
use DbQuery;
use ObjectModel;
use Configuration;

/**
 * JetCreditModel - Model for working with jet_kop table
 *
 * This model handles credit schemes and promotional calculations for the CreditJet module.
 * It manages product-specific credit configurations, interest rates, and promotional offers.
 * The model interacts with the jet_kop database table to store and retrieve credit settings.
 */
class JetCreditModel extends ObjectModel
{
    /**
     * Database table name for credit configurations
     *
     * @var string
     */
    public const TABLE_NAME = 'jet_kop';

    /**
     * ObjectModel definition for the jet_kop table
     *
     * Defines the database table structure, primary key, and field validations
     * for the JetCreditModel ObjectModel.
     *
     * @var array
     */
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

    /**
     * Insert credit configuration if it doesn't already exist
     *
     * Checks if a credit configuration for the given product ID already exists.
     * If not, inserts a new record. Returns -1 if the record already exists.
     *
     * @param string $jet_product_id Product identifier
     * @param float $jet_product_percent Interest rate percentage
     * @param string $jet_product_meseci Available installment months (comma-separated)
     * @param float $jet_product_price Minimum price for credit
     * @param string $jet_product_start Start date for the promotion
     * @param string $jet_product_end End date for the promotion
     *
     * @return bool|int|string Database insert result or -1 if already exists
     */
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

    /**
     * Delete credit configuration by product ID
     *
     * Removes all credit configurations associated with the specified product ID.
     *
     * @param string $jet_product_id Product identifier to delete configurations for
     *
     * @return bool True if deletion was successful, false otherwise
     */
    public static function deleteByJetProductId($jet_product_id): bool
    {
        return Db::getInstance()->delete(
            self::TABLE_NAME,
            'jet_product_id = \'' . pSQL($jet_product_id) . '\''
        );
    }

    /**
     * Get promotional credit settings for a product
     *
     * Retrieves promotional interest rates and settings for a specific product based on
     * installment count and total credit price. Checks for active promotions in the
     * database and returns appropriate rates for both regular and card payments.
     *
     * @param string $jet_product_id Product identifier
     * @param int $jet_vnoski Number of installments
     * @param float $jet_total_credit_price Total credit amount
     *
     * @return array Array containing:
     *               - 'jet_show_button' (bool): Whether to show credit button
     *               - 'jet_purcent' (float): Interest rate percentage
     *               - 'jet_purcent_card' (float|null): Card payment interest rate or null
     */
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
