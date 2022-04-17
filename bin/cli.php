<?php

use Pavelkrauchuk\Testtask\Entity\MoneyPrize;

require 'vendor/autoload.php';

if ($argv[1] === 'transfer' && isset($argv[2]) && is_numeric($argv[2])) {
    $moneyPrize = new MoneyPrize();

    while ($arMoneyPrize = $moneyPrize->repository->getNotTransferredCollection($argv[2])) {

        $arMoneyPrizeId = array();
        foreach ($arMoneyPrize as $item) {
            $arMoneyPrizeId[] = $item['id'];
        }

        $dataEncoded = json_encode($arMoneyPrize);
        $curl = curl_init('http://test/api.php');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataEncoded);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataEncoded),
        ));

        $result = curl_exec($curl);

        $moneyPrize->repository->updateTransferredCollection($arMoneyPrizeId);
    }
} else {
    echo "Требуется указать количество отправляемых за одну итерацию записей";
}
