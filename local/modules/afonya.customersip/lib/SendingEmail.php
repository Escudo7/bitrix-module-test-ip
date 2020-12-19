<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\SiteTable;

class SendingEmail
{
    public static function sendEmailAboutIP($data)
    {
        $dataForEmail = self::prepareIPInformation($data['IP_INFORM']);

        $sites = SiteTable::getList(['select' => ['LID'], 'filter' => ['ACTIVE' => 'Y']])->fetchAll();
        $siteList = [];

        foreach ($sites as $site) {
            $siteList[] = $site['LID'];
        }

        Event::send([
            'EVENT_NAME' => 'NEW_IP_INFORM',
            'LID' => $siteList,
            'C_FIELDS' => [
                'ORDER_ID' => $data['ORDER_ID'],
                'IP' => $data['IP'],
                'IP_INFORM' => $dataForEmail
            ]
        ]);
    }

    private static function prepareIPInformation($data) {
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
}