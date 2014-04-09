<?php
/**
 * FuelPHP RunTasks Packages
 *
 * @package     RunTasks
 * @version     0.2.0
 * @author      dimgraycat
 * @copyright   dimgraycat
 * @license     MIT License http://www.opensource.org/licenses/mit-license.php
 */

use \Config;
use \Fuel;

class RunTasks_Runner {
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
     * Singleton instance
     * @var Runtasks\Runner $instance Singleton master instance
     */
    protected static $instance = null;

    /**
     * An alias for Runtasks\Runner::instance()->execute();
     *
     * @param   array $properties
     * @param   array $config_file
     */
    public static function run($group, array $properties = array(), $config_file = null) {
        return static::instance($properties, $config_file)->execute($group);
    }

    /**
     * Gets a singleton instance of Runtasks
     *
     * @return  Runtasks
     */
    public static function instance(array $properties = array(), $config_file = null) {
        static::$instance or static::$instance = static::forge($properties, $config_file);
        return static::$instance;
    }

    /**
     * Forges new Runtasks.
     *
     * @param   array $properties
     * @param   array $config_file
     * @return  Runtasks
     */
    public static function forge($properties, $config_file) {
        return new static($properties, $config_file);
    }

    /**
     * Constructor
     * @param   array $properties
     * @param   array $config_file
     */
    public function __construct(array $properties = array(), $config_file = null) {
        $this->_config_load($config_file);
        $this->_init_php_ini();
        $this->_init_properties($properties);

        $this->oil_refine = sprintf('%s%s refine', DOCROOT, 'oil');
    }

    protected function _init_php_ini() {
        ini_set('memory_limit', Config::get('runtasks.php_ini.memory_limit', '128M'));
        set_time_limit(Config::get('runtasks.php_ini.time_limit', 30));
    }

    protected function _init_properties(array $properties) {
        $default_properties = Config::get('runtasks.default', array());
        $properties = array_merge($default_properties, $properties);
        $this->set_properties($properties);
    }

    protected function _config_load($config_file) {
        $file = ($config_file !== null) ? $config_file : 'runtasks.yml';
        Config::load($file, 'runtasks', true);
    }

    /**
     * Set a property setting.
     * @param mixed $properties the new property key and value
     */
    public function set_properties($properties) {
        is_array($properties) or $properties = array($properties => null);
        foreach($properties as $name => $value) {
            $value or $value = false;
            $this->$name = $value;
        }
    }

    /**
     * execute
     *
     * @param string $group
     */
    public function execute($group) {
        $tasks = Config::get("runtasks.groups.$group", array());
        $exit_code = -1;
        foreach($tasks as $task) {
            $command = $this->command($task);
            $this->_logger('info', '----');
            $this->_logger('info', sprintf('starting: task:[%s]', $task));
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
                    $this->_std_logger('error', $stderr);
                    unset($stderr);

                    $exit_code = proc_close($process);
                }
            }
            catch(\Exception $e) {
                $this->_logger('error', $e->getMessage());
            }
            $this->_logger('info', sprintf(
                'command exited with code:[%s] task:[%s] time:[%f sec]',
                $exit_code, $task, microtime(true) - $time_start
            ));
            if($exit_code !== 0 && !$this->is_continue) break;
        }
        return $exit_code;
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
        if($this->is_stdout === true)  fwrite(STDOUT, "$message\n");
        if($this->is_logging !== true) return;
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
