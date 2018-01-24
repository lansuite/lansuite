<?php
$md = new masterdelete();
$md->Delete('partylist', 'partyid', $_GET['partyid']);
