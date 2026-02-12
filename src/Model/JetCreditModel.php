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
     * Fetch the first value from a database query result
     *
     * Executes a DbQuery and extracts the first value of the specified field
     * from the first row of the result set. Supports multiple database result
     * formats (mysqli, PDO, mysql resource).
     *
     * @param DbQuery $sql The database query object to execute
     * @param string $field The field name to extract from the result row
     *
     * @return mixed|false The value of the specified field from the first row,
     *                     or false if no result found, query failed, or field doesn't exist
     */
    private static function fetchFirstValue(DbQuery $sql, $field)
    {
        $db = Db::getInstance();
        $res = $db->query((string)$sql);

        if (!$res) {
            return false;
        }

        if (is_object($res) && method_exists($res, 'fetch_assoc')) {
            $row = $res->fetch_assoc();
            return ($row && array_key_exists($field, $row)) ? $row[$field] : false;
        }

        if (is_object($res) && method_exists($res, 'fetch')) {
            $row = $res->fetch(\PDO::FETCH_ASSOC);
            return ($row && array_key_exists($field, $row)) ? $row[$field] : false;
        }

        if (is_resource($res)) {
            $row = @mysql_fetch_assoc($res);
            return ($row && array_key_exists($field, $row)) ? $row[$field] : false;
        }

        return false;
    }

    /**
     * Get promotional credit settings for a product
     *
     * Determines promotional interest rates and button visibility based on product-specific
     * configurations stored in the jet_kop table. The function uses a two-tier lookup strategy:
     * 1. First checks for a specific product ID match
     * 2. If no match found, falls back to wildcard '*' configuration for all other products
     *
     * The function filters results based on:
     * - Installment months (vnoski) must match available months
     * - Total credit price must be >= minimum configured price
     * - Current date must be within promotion start/end date range
     *
     * Special handling:
     * - If percentage is -1.00, the credit button is hidden (jet_show_button = false)
     * - If card payment is enabled (JET_CARD_IN = 1), applies the same percentage to card payments
     *
     * @param string|int $jet_product_id Product identifier to look up. Can be specific ID or '*' for wildcard
     * @param int $jet_vnoski Number of installments (months) for the credit
     * @param float $jet_total_credit_price Total price of the credit to be checked against minimum price
     *
     * @return array{
     *     jet_show_button: bool,
     *     jet_purcent: float,
     *     jet_purcent_card: float|null
     * } Associative array containing:
     *     - jet_show_button: Whether to show the credit button (false if percentage is -1.00)
     *     - jet_purcent: Interest rate percentage (from config or promotion, default from JET_PURCENT)
     *     - jet_purcent_card: Card payment interest rate (null if card payment disabled, otherwise same as jet_purcent)
     */
    public static function getPromo($jet_product_id, $jet_vnoski, $jet_total_credit_price)
    {
        $jet_card_in = (int) Configuration::get('JET_CARD_IN');
        $jet_purcent = (float) Configuration::get('JET_PURCENT');

        $jet_purcent_card = null;
        if ($jet_card_in === 1) {
            $jet_purcent_card = (float) Configuration::get('JET_PURCENT_CARD');
        }

        $jet_show_button = true;

        $commonWhere = array(
            'FIND_IN_SET(' . (int)$jet_vnoski . ", REPLACE(jet_product_meseci, '_', ','))",
            (float)$jet_total_credit_price . ' >= jet_product_price',
            'jet_product_start <= CURDATE()',
            'jet_product_end >= CURDATE()',
        );

        $sql = new DbQuery();
        $sql->select('jet_product_percent');
        $sql->from('jet_kop');
        $sql->where('jet_product_id = \'' . pSQL($jet_product_id) . '\'');
        foreach ($commonWhere as $w) {
            $sql->where($w);
        }
        $sql->orderBy('id ASC');
        $jet_product_percent = self::fetchFirstValue($sql, 'jet_product_percent');

        if ($jet_product_percent === false) {
            $sql2 = new DbQuery();
            $sql2->select('jet_product_percent');
            $sql2->from('jet_kop');
            $sql2->where('jet_product_id = \'*\'');
            foreach ($commonWhere as $w) {
                $sql2->where($w);
            }
            $sql2->orderBy('id ASC');

            $jet_product_percent = self::fetchFirstValue($sql2, 'jet_product_percent');
        }

        if ($jet_product_percent !== false) {
            if ((float)$jet_product_percent === -1.00) {
                $jet_show_button = false;
            } else {
                $jet_purcent = (float)$jet_product_percent;
                if ($jet_card_in === 1) {
                    $jet_purcent_card = (float)$jet_product_percent;
                }
            }
        }

        return [
            'jet_show_button'  => $jet_show_button,
            'jet_purcent'      => $jet_purcent,
            'jet_purcent_card' => ($jet_card_in === 1) ? $jet_purcent_card : null,
        ];
    }
}
