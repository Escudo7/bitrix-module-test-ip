<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

use Bitrix\Main\Entity;

/**
 * Class DataTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ORDER_ID int mandatory
 * <li> IP string mandatory
 * <li> DATA string optional
 * </ul>
 *
 * @package \Afonya\Data
 **/

class DataTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'afonya_castomers_ip';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
            ),
            'ORDER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => 'ORDER_ID',
            ),
            'IP' => array(
                'data_type' => 'text',
                'required' => true,
                'title' => 'IP',
            ),
            'DATA' => array(
                'data_type' => 'text',
                'required' => false,
                'title' => 'DATA',
            ),
        );
    }
}
