<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

use Afonya\CustomersIP\DataTable;
use Afonya\CustomersIP\SendingEmail;
use Bitrix\Main\Event;

class EventHandlers
{
    public function onSaleOrderSavedHandler($order, $values, $isNew) {


        if ($isNew) {
            $ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
file_put_contents(__DIR__ . '/values', print_r($values, true));
            DataTable::add([
                'ORDER_ID' => $order->getId(),
                'IP' => $ip,
            ]);
        }
    }

    public function onIPInformGetHandler($data) {
        SendingEmail::sendEmailAboutIP($data);
    }
}