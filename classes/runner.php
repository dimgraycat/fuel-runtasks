<?php
/**
 * FuelPHP RunTasks Packages
 *
 * @package     RunTasks
 * @version     0.1.0
 * @author      dimgraycat
 * @copyright   dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 */
namespace RunTasks;

use \Config;
use \Fuel;

class Runner {
    /**
     * Default Properties
     * @var array
     */
    private $_properties = array();

    /**
     * proc_opne descriptorspec
     * @var array
     */
    protected $_descriptorspec = array(
        0 => array('pipe', "r"),
        1 => array('pipe', "w"),
        2 => array('pipe', "w"),
    );

    /**
     * Constructor
     * @param array $properties
     */
    public function __construct(array $properties = array()) {
        Config::load('runtasks.yml', true);

        $default_properties = Config::get('runtasks.default', array());
        $properties = array_merge($default_properties, $properties);
        $this->set_properties($properties);

        $this->oil_refine = sprintf('%s%s refine', DOCROOT, 'oil');
    }

    /**
     * Set a property setting.
     * @param mixed $properties the new property key and value
     */
    public function set_properties($properties) {
        $properties = is_array($properties) ? $properties : array( $properties => null );
        foreach($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * run
     *
     * @param string $group
     */
    public function run($group) {
        $tasks = Config::get("runtasks.groups.$group", null);
        foreach($tasks as $task) {
            $command = $this->command($task);
            $this->_logger('info', '----');
            $this->_logger('info', sprintf('starting: %s', $command));
            $time_start = microtime(true);
            $exit_code = null;
            try {
                $process = proc_open($command, $this->_descriptorspec, $pipes);
                if(is_resource($process)) {
                    fclose($pipes[0]);

                    $stdout = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    $this->_std_logger('info', $stdout);
                    unset($stdout);

                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);
                    $this->_std_logger('warning', $stderr);
                    unset($stderr);

                    $exit_code = proc_close($process);
                }
            }
            catch(\Exception $e) {
                $this->_logger('warning', $e->getMessage());
            }
            $this->_logger('info', sprintf('command exited with code: %s time: %f', $exit_code, microtime(true) - $time_start));
            unset($output);
            if($exit_code !== 0) break;
        }
    }

    /**
     * command
     *
     * @param string $task
     * @return string the run command
     */
    public function command($task) {
        if(is_array($task)) {
            $task = $task['command'];
        }
        return sprintf('env FUEL_ENV=%s %s %s %s',
            Fuel::$env,
            $this->php_path,
            $this->oil_refine,
            $task
        );
    }

    /**
     * _std_logger
     *
     * @param string $method \Log::$method
     * @param string $message the stdXXX message
     */
    protected function _std_logger($method, &$message) {
        if($this->is_logging !== true) return;

        $lines = preg_split("/\R/", $message);
        $lines = array_filter($lines, 'strlen');
        foreach($lines as $line) $this->_logger($method, $line);
    }

    /**
     * _logger
     *
     * @param string $method \Log::$method
     * @param string $message
     */
    protected function _logger($method, $message = null) {
        if($this->is_logging !== true) return;
        if($this->is_stdout === true)  print "$message\n";
        \Log::$method($message, $this->prefix_message);
    }

    /* magic method __get */
    public function __get($name) {
        if (array_key_exists($name, $this->_properties)) {
            return $this->_properties[$name];
        }
        return null;
    }

    /* magic method __set */
    public function __set($name, $value) {
        $this->_properties[$name] = $value;
    }
}
