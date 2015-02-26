<?php
/**
 * FuelPHP RunTasks Packages
 *
 * @author    dimgraycat
 * @copyright dimgraycat
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 * @package   Fuel
 */

Autoloader::add_core_namespace('RunTasks');

Autoloader::add_classes(array(
    'RunTasks_Runner'  => __DIR__.'/classes/runner.php',
    'RunTasks_Cli'     => __DIR__.'/classes/cli.php',
    'RunTasks_Config'  => __DIR__.'/classes/config.php',
));
