<?php
/**
 * FuelPHP RunTasks Packages
 *
 * @package   RunTasks
 * @author    dimgraycat
 * @copyright dimgraycat
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */

Autoloader::add_core_namespace('RunTasks');

Autoloader::add_classes(array(
    'RunTasks\\Runner'  => __DIR__.'/classes/runner.php',
));
