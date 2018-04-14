<?php

$dsp->NewContent(t('Kalkulation'), t('Zur aktuellen Lanparty zum derzeitigen Stand'));

$account = new \LanSuite\Module\CashMgr\Accounting($party->party_id);
$account->booking(3, 'testbooking1', 3);
