<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

class Agents
{
    const URL_IP_INFORM = 'https://rest.db.ripe.net/search.json?query-string=';

    public static function checkIPTableAgent()
    {
        $newIPs = DataTable::getList(['filter' => ['DATA' => NULL]])->fetchAll();

        if (!empty($newIPs)) {
            $HTTPClient = new \Bitrix\Main\Web\HttpClient();

            foreach ($newIPs as $IP) {
                $url = self::URL_IP_INFORM . $IP['IP'];
                $response = $HTTPClient->get($url);

                if ($response) {
                    $arData = json_decode($response, true);
                    $ipInform = $arData['objects']['object'];

                    DataTable::update($IP['ID'], [
                        'DATA' => serialize($ipInform)
                    ]);

                    $eventData = [
                        'IP_INFORM' => $ipInform,
                        'IP' => $IP['IP'],
                        'ORDER_ID' => $IP['ORDER_ID']
                    ];
                    $event = new \Bitrix\Main\Event('afonya.customersip', 'OnIPInformGet', [$eventData]);
                    $event->send();
                }
            }
        }

        return "\\Afonya\\CustomersIP\\Agents::checkIPTableAgent();";
    }
}