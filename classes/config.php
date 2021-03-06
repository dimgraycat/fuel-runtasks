<?php
/**
 * FuelPHP RunTasks Packages
 *
 * @author      dimgraycat
 * @copyright   dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 * @package     Fuel
 */

use \Config;

class RunTasks_Config {

    /**
     * get
     * @param string $include_dir
     * @param string $load_group
     */
    public static function get($include_dir, $load_group) {
        $config = Config::get("runtasks", array());
        $groups = $config['groups'];
        foreach($groups as $group => $tasks) {
            preg_match('/^\+.*/', $group, $matches);
            if(!empty($matches)) {
                $conf = self::load($include_dir, $group, $tasks);
                $groups = array_merge($groups, $conf);
                unset($groups[$matches[0]]);
            }
        }
        return array_key_exists($load_group, $groups) ? $groups[$load_group] : array();
    }

    /**
     * load config
     * @param string $include_dir
     * @param string $group
     * @param string $file
     */
    private static function load($include_dir, $group, $file) {
        return Config::load(sprintf('%s/%s', $include_dir, $file), 'runtasks_include', true);
    }

}
