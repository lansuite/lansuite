<?php

namespace Lansuite;

class UpdateHandler{

private $currentVersion
private $targetVersion
private $newDependencies
private $dependencyHooks
private $preUpdateHooks
private $postUpdateHooks
private $preBackupHooks
private $postBackupHooks
private $preRollbackHooks
private $postRollbackHooks


public function __construct($module=''){
// Either run a full system upgrade or just a module
// @TODO: Do we just do a recursive call if a full system update is done?
}
public function checkReleaseVer(){
// Connect to GitHub, receive most current release


}
protected function getHooksFromModules(){
// Get all hooks for all cases from all modules and store them in the appropriate array (or subarray, if we do that)
}
protected function runHooks($hookType,$hookData){
	
	//Run all defined hooks in order, check their return status, fail if any of them fails
	//Must be able to store type and message of return values 
	
}

public function checkDependencies($targetVersion){
// Investigate new dependencies

//Check against them
// Check if we have anything to compress the DB image and source files

// Get sizes for DB, new source files, size of current folder

// Check if composer is available and executable
}
public function backup($includeUserData = False){
// run pre-backup hooks of NEW version(! as this could define a new requirement that was not catched before)

// Dump DB

// Archive all program files, leave userdata where it is

// Run post-backup hooks from new version

// Compress all backed up files (there could be someting additional inserted by the pre/post backup hooks

}
public function upgrade($newVersion){
	
	// Run pre-Upgrade hooks
	
	// Iterate through all available update files up to the desired version
	
	// Verify feedback, either go to rollback or continue
	
	// Run post-upgrade tasks
	
}
public function rollback($restoreUserData = False){
	
	// Run pre-rollback hooks
	
	// Run Rollback
	
	// Run post-rollback hooks
	
}
protected function createReport(){
	
	//Take all reported information and throw it at the user. Either as formatted webpage or dump it on the console
	
	
}
}