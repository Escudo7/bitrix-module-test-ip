<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

use Bitrix\Main\Service\GeoIp\Manager;

class EventHandlers
{
    public static function onSaleOrderSavedHandler($order, $values, $isNew) {

        if (!$isNew) {
            return;
        }

        DataTable::add([
            'ORDER_ID' => $order->getId(),
            'IP' => Manager::getRealIp(),
        ]);
    }

    public static function onIPInformGetHandler($data) {
        SendingEmail::sendEmailAboutIP($data);
    }
}