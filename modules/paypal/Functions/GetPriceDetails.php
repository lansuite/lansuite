<?php

/**
 * @param int $priceID
 * @return array|bool|null
 */
function GetPriceDetails($priceID)
{
    global $db, $auth;

    $result = $db->qry_first('
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
        user_id=%int%
        AND pu.price_id=%int%', $auth['userid'], $priceID);

    // Make sure that we only get the value, not the currency items
    $result['price'] = SanitizeVal($result['price']);

    return $result;
}
