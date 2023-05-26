<?php

namespace LanSuite;

use LanSuite\XML;

class Configuration
{
    private array $configuration = [];

    /**
     * @param string $file XML file to parse
     * @return void
     */
    public function loadConfigurationFromXML($file) {
        /**
         * This code is mostly based on \LanSuite\Module\Install\Install::InsertModules.
         */
        $handle = fopen($file, 'r');
        $xml_file = fread($handle, filesize($file));
        fclose($handle);

        $xml = new XML();
        $xml_config = $xml->get_tag_content('config', $xml_file);
        
        // Read settings
        $xml_groups = $xml->get_tag_content_array('group', $xml_config);
        if ($xml_groups) {
            while ($xml_group = array_shift($xml_groups)) {
                $xml_items = $xml->get_tag_content_array('item', $xml_group);
                if ($xml_items) {
                    while ($xml_item = array_shift($xml_items)) {
                        $name = $xml->get_tag_content('name', $xml_item);
                        $type = $xml->get_tag_content('type', $xml_item);
                        $default = $xml->get_tag_content('default', $xml_item);

                        switch ($type) {
                            case 'integer':
                            case 'int':
                                $val = (int) $default;
                                $this->addConfigurationValue($name, $val);
                                break;
                            case 'boolean':
                            case 'bool':
                                $val = (bool) $default;
                                $this->addConfigurationValue($name, $val);
                                break;
                            case 'float':
                                $val = (float) $default;
                                $this->addConfigurationValue($name, $val);
                                break;
                            default:
                                $this->addConfigurationValue($name, $default);
                                break;
                        }
                    }
                }
            }
        }        
    }
    
    public function getConfigurationAsArray() {
        return $this->configuration;
    }

    protected function addConfigurationValue($key, $value) {
        $this->configuration[$key] = $value;
    }
}