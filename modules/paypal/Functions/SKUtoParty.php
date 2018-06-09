<?php

/**
 * We stored the party_id and the price_id inside the PayPal SKU,
 * so we need to extract it from the string again
 *
 * @param $SKU
 * @return array|bool
 */
function SKUtoParty(string $SKU)
{
    $SKUarray = explode('-', $SKU);
    if ($SKUarray[0] == 'PARTY') {
        return [
            'party_id' => $SKUarray[1],
            'price_id' => $SKUarray[2]
        ];
    }
    return false;
}
