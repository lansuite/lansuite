<?php

class signon
{
    public $birthday;
    public $perso;

        
    public function SplitStreet($input)
    {
        $pieces = explode(" ", $input);
        $res["nr"] = array_pop($pieces);
        $res["street"] = implode(" ", $pieces);
        return $res;
    }


    public function SplitCity($input)
    {
        $pieces = explode(" ", $input);
        $res["plz"] = array_shift($pieces);
        $res["city"] = implode(" ", $pieces);
        return $res;
    }
}
