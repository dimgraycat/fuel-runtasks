<?php
/**
 * @package     RunTasks
 * @author      dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 * @copyright   2014 dimgraycat
 */

use \Cli;
use \RunTasks_Runner;

class RunTasks_Cli {

    protected static function parse_options() {
        $options = array(
            'php_path'      => \Cli::option('php_path'),
            'is_logging'    => \Cli::option('logging'),
            'is_stdout'     => \Cli::option('stdout'),
            'is_continue'   => \Cli::option('continue'),
        );
        foreach ($options as $key => $value) {
            if(empty($options[$key])) unset($options[$key]);
            preg_match('/^is_\w+/', $key, $matches);
            if(array_key_exists('0', $matches)) {
                $value and $options[$key] = true;
            }
        }
        return (empty($options)) ? array() : $options;
    }

    public static function run($group) {
        if($group == 'help') {
            static::help();
            exit;
        }
        $options = static::parse_options();
        $config  = \Cli::option('config', null);
        RunTasks_Runner::run($group, $options, $config);
    }

    public static function help() {
        echo <<<HELP
    Usage:
        php oil refine runtasks <grop> [<options>]  - run grop tasks
        php oil refine runtasks:help                - show help.
        php runtasks <grop> [<options>]             - run grop tasks
        php runtasks help                           - show help.

    Default Grop Settings:
        runtasks.yml        - This setting file is called from Config::load

    Options:
        override the default settings.

        --stdout            - stdout by adding.
        --logging           - log outputs by adding.

HELP;
    }
}
