<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

use Afonya\CustomersIP\DataTable;

class AfonyaCustomersIP

{
    const URL_IP_INFORM = 'https://rest.db.ripe.net/search.json?query-string=';

    public function onOrderAddHandler($id, $arFields) {
        $ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();

        DataTable::add([
            'ORDER_ID' => $id,
            'IP' => $ip,
        ]);
    }

    public function onIPInformGetHandler($data) {
        $dataForEmail = self::prepareDataForEmail($data['IP_INFORM']);

        $rsSites = \Bitrix\Main\SiteTable::getList(['select' => ['LID'], 'filter' => ['ACTIVE' => 'Y']]);
        $arSites = $rsSites->fetchAll();
        $sitesList = [];

        foreach ($arSites as $site) {
            $sitesList[] = $site['LID'];
        }

        \Bitrix\Main\Mail\Event::send([
            'EVENT_NAME' => 'NEW_IP_INFORM',
            'LID' => $sitesList,
            'C_FIELDS' => [
                'ORDER_ID' => $data['ORDER_ID'],
                'IP' => $data['IP'],
                'IP_INFORM' => $dataForEmail
            ]
        ]);
    }

    private function prepareDataForEmail($data) {
        $table = '<table>';
        $table .= '<tr>';
        $table .= '<th>Название параметра</th>';
        $table .= '<th>Значение параметра</th>';
        $table .= '</tr>';

        foreach ($data as $item) {
            $table .= '<tr>';
            $table .= '<td colspan="2" style="text-align: center"><b>' . $item['type'] . '</b></td>';
            $table .= '</tr>';

            foreach ($item['attributes']['attribute'] as $attribute) {
                $table .= '<tr>';
                $table .= '<td>' . $attribute['name'] . '</td>';
                $table .= '<td>' . $attribute['value'] . '</td>';
                $table .= '</tr>';
            }
        }

        $table .= '</table>';

        return $table;
    }

    public static function checkIPTableAgent()
    {
        $newIPs = DataTable::getList(['filter' => ['DATA' => NULL]])->fetchAll();

        if (!empty($newIPs)) {
            foreach ($newIPs as $IP) {
                $url = self::URL_IP_INFORM . $IP['IP'];
                $HTTPClient = new \Bitrix\Main\Web\HttpClient();
                $response = $HTTPClient->get($url);

                if ($response) {
                    $arData = json_decode($response, true);
                    $ipInform = $arData['objects']['object'];

                    DataTable::update($IP['ID'], [
                        'DATA' => serialize($ipInform)
                    ]);

                    $eventData = [
                        'IP_INFORM' => $ipInform,
                        'IP' => $IP['ID'],
                        'ORDER_ID' => $IP['ORDER_ID']
                    ];
                    $event = new \Bitrix\Main\Event('afonya.customersip', 'OnIPInformGet', [$eventData]);
                    $event->send();
                }
            }
        }

        return "\\Afonya\\CustomersIP\\AfonyaCustomersIP::checkIPTableAgent();";
    }
}