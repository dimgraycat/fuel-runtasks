<?php
/**
 * RunTasks Tasks
 * @author      dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 * @copyright   2015 dimgraycat
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
