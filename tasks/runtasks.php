<?php
/**
 * RunTasks Tasks
 *
 * @author      dimgraycat
 * @copyright   dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 * @package     Fuel
 */
namespace Fuel\Tasks;

use \RunTasks_Cli;

class RunTasks {

    public static function run($group) {
        RunTasks_Cli::run($group);
    }

    public static function help() {
        RunTasks_Cli::help();
    }
}
