<?php // phgstats factory class
/*
* Copyright (c) 2004-2006, woah-projekt.de
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
*   notice, this list of conditions and the following disclaimer.
* * Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer
*   in the documentation and/or other materials provided with the
*   distribution.
* * Neither the name of the phgstats project (woah-projekt.de)
*   nor the names of its contributors may be used to endorse or
*   promote products derived from this software without specific
*   prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*/

class phgstats
{ 
    function &query($srv_type)
    {
        switch ($srv_type)
	{
            case 'aa': // Army Ops
	        include_once('aa.class.php');
                $query =& new aa;
		return $query;
	        break;
	    case 'bf': // Battlefield 1942
	        include_once('bf.class.php');
		$query =& new bf;
		return $query;
		break;
	    case 'bfv': // Battlefield Vietnam		   
	        include_once('bfv.class.php');
                $query =& new bfv;
		return $query;
		break;
	    case 'bf2':    // Battlefield 2
	    case 'bf2142': // Battlefield 2142
	        include_once('bf2.class.php');
	        $query =& new bf2;
		return $query;
		break;
	    case 'cod': // Call Of Duty / UO
	        include_once('cod.class.php');
		$query =& new cod;
	        return $query;
		break;
	    case 'cod2': // Call Of Duty 2
	        include_once('cod2.class.php');
		$query =& new cod2;
		return $query;
		break;
	    case 'des3gs': // Descent 3 (Gamespy)
	        include_once('des3gs.class.php');
                $query =& new des3gs;
	        return $query;
		break;
	    case 'descent3': // Descent 3
	        include_once('des3.class.php');
                $query =& new descent3;
	        return $query;
		break;
	    case 'fear': // FEAR
	        include_once('fear.class.php');
                $query =& new fear;
	        return $query;
	        break;
	    case 'et': // Enemy Territory
	        include_once('et.class.php');
		$query =& new et;
		return $query;
		break;
	    case 'halo': // HALO
	        include_once('halo.class.php');
		$query =& new halo;
		return $query;
		break;
	    case 'hd2': // Hidden & Dangerous 2
	        include_once('hd2.class.php');
		$query =& new hd2;
		return $query;
		break;
	    case 'hl':  // Half-Life / CS
	    case 'hl2': // Half-Life 2 / CS:S
	        include_once('hl.class.php');
	        $query =& new hl;
	        return $query;
		break;
	    case 'hl_old': // Half-Life (without Steam)
	        include_once('hl_old.class.php');
                $query =& new hl_old;
	        return $query;
		break;
	    case 'jedi':  // Jedi Knight: Jedi Academy
	    case 'jedi2': // Jedi Knight II
	        include_once('jedi.class.php');
		$query =& new jedi;
		return $query;
		break;
	    case 'mohaa': // Medal Of Honor
	        include_once('mohaa.class.php');
	        $query =& new mohaa;
                return $query;
		break;
	    case 'nolf': // No One Lives Forever
                include_once('nolf.class.php');
		$query =& new nolf;
		return $query;
		break;						
	    case 'pk': // Painkiller
	        include_once('pk.class.php');
                $query =& new pk;
	        return $query;
		break;
	    case 'rtcw': // Return to Castle Wolfenstein
	        include_once('rtcw.class.php');
                $query =& new rtcw;
	        return $query;
		break;
	    case 'rune': // Rune
	        include_once('rune.class.php');
                $query =& new rune;
                return $query;
		break;
	    case 'swat': // SWAT
	        include_once('swat.class.php');
	        $query =& new swat;
                return $query;
		break;
	    case 'ut': // Unreal Tournament
	        include_once('ut.class.php');
                $query =& new ut;
                return $query;
                break;
	    case 'ut2003': // Unreal Tournament 2003
	    case 'ut2004': // Unreal Tournament 2004
	    case 'ro':     // Red Orchestra
	        include_once('ut2004.class.php');
	        $query =& new ut2004;
	        return $query;
		break;
	    case 'q1': // Quake 1
	    case 'qw': // Quakeworld
	        include_once('q1.class.php');
	        $query =& new q1;
	        return $query;
		break;
	    case 'q2': // Quake 2
	        include_once('q2.class.php');
		$query =& new q2;
	        return $query;
		break;
	    case 'q3': // Quake 3
	        include_once('q3a.class.php');
	        $query =& new q3;
	        return $query;
		break;
	    case 'q4': // Quake 4
	        include_once('q4.class.php');
                $query =& new q4;
	        return $query;
		break;
	    case 'sof2': // Soldier of Fortune 2
	        include_once('sof2.class.php');
	        $query =& new sof2;
	        return $query;
	        break;
	    case 'atron': // Amagetronad
	        include_once('atron.class.php');
                $query =& new atron;
	        return $query;
	        break;
	    case 'd3': // Doom 3
	        include_once('d3.class.php');
	        $query =& new d3;
	        return $query;
		break;
	    case 'ts': // Teamspeak 2
	        include_once('ts.class.php');
                $query =& new ts;
	        return $query;
		break;
	    case 'warsow': // Warsow
	        include_once('warsow.class.php');
                $query =& new warsow;
	        return $query;
	        break;
	    default: // No Class found
	        die ("$srv_type: Server type unkown");
	  }
    }
}
?>
