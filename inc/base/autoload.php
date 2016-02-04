<?php

/**
 * Define a standard autoloader that tries to load the class file from the module directory 
 * @param string $class The class to be loaded
 */
function ls_module_autoload($class){
    //replace backslash with system dependend directory separator
    $namespace_parts = explode('\\', __NAMESPACE__);
    $namespace_path = implode(DIRECTORY_SEPARATOR, $namespace_parts);
    
    $file_path = 'modules'. DIRECTORY_SEPARATOR . $namespace_path . DIRECTORY_SEPARATOR . strtolower($class) . '_class.php';
   if (file_exists($file_path)){
       include $file_path;
       return true;
   }
   else {return false;}
}

//and now register this module...
spl_autoload_register("ls_module_autoload");