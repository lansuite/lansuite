<?php

class PartyLocation {

    private bool $smokingAllowed = False;
    private bool $eCigAllowed = False;
    private bool $showersAvailable = True;
    private bool $parkingAvailable = True;
    private bool $wifiAvailable = True;
    private bool $separateSleepArea = True;
    private int $inetBandwidth = 0;
    private int $postCode = -1;
    private array $coords = ['longitude' => 0, 'latitude' => 0];
    private int $infoPageId = -1;

public function add(array $locationDetails) {

    global $database;

    $insertResult = $database->query(
        'INSERT INTO %prefix%party_location( 
            locationinfo_id,
            location_name,
            postcode,
    VALUES')

}


}