<?php
declare(strict_types=1);

namespace Afonya\CustomersIP;

class SendingEmail
{
    public static function sendEmailAboutIP($data)
    {
        $dataForEmail = self::prepareIPInformation($data['IP_INFORM']);
        file_put_contents(__DIR__ . '/data', print_r($dataForEmail, true));
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