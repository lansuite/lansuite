<?php

namespace LanSuite\Module\Tournament2;

class TournamentFunction extends TournamentLegacy
{

//function to construct derived class per tournament mode
public static function create($mode){
	switch $mode:
	case 'SE':
		return new SETournament();
		break;
	case 'DE':
		return new DETournament();
		break;
	case 'Group':
		return new GroupTournament();
		break;
	case 'AIO'
		return new AiOTournament();
		break;
	case 'League'
		return new LeagueTournament();
		break;
	case 'Swiss'
		return new SwissTournament();
		break;
	default: 
		return false;
}

	//This is for backwards-compatibility and will be extended in the per-mode subclases
}
