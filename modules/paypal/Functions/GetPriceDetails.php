<?php

/**
 * @param int $priceID
 */
function GetPriceDetails($priceID): array|bool|null
{
    global $database, $auth;

    $result = $database->queryWithOnlyFirstRow('
      SELECT
        pu.party_id,
        pu.user_id,
        pu.price_id,
        p.name,
        price_text,
        price.price
      FROM %prefix%party_user AS pu
      LEFT JOIN %prefix%partys AS p USING(party_id)
      LEFT JOIN %prefix%party_prices AS price ON price.price_id = pu.price_id
      WHERE
        user_id = ?
        AND pu.price_id = ?', [$auth['userid'], $priceID]);

    // Make sure that we only get the value, not the currency items
    $result['price'] = SanitizeVal($result['price']);

    return $result;
}
