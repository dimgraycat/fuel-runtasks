<?php
/**
 * Package RunTasks tests
 *
 * @group Package
 * @group RunTasks
 */
class Test_Runner extends TestCase {
    public function test_command() {
        $runner = $this->instance();
        Fuel::$env = 'development';

        $this->assertEquals(
            "env FUEL_ENV=development {$runner->php_path} {$runner->oil_refine} run:test a b c",
            $runner->command('run:test a b c')
        );
    }

    public function test_php_path() {
        $runner = $this->instance();
        $this->assertEquals('php', $runner->php_path);

        $php_path = '/path/to/php';
        $runner = $this->instance(array('php_path' => $php_path));
        $this->assertEquals($php_path, $runner->php_path);
    }

    public function test_run_success() {
        $runner = $this->instance();
        $exit_code = $runner->run('example_group');
        $this->assertEquals(0, $exit_code);
    }

    public function test_run_failed() {
        $runner = $this->instance();
        $exit_code = $runner->run('throw_group');
        $this->assertEquals(255, $exit_code);
    }

    public function instance(array $properties = array()) {
        $runner = new RunTasks\Runner($properties, 'example.yml');
        return $runner;
    }
}
