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

    public function test_properties() {
        $runner = $this->instance();
        $this->assertEquals('php', $runner->php_path);
        $this->assertEquals(false, $runner->is_logging);
        $this->assertEquals(false, $runner->is_stdout);
        $this->assertEquals(false, $runner->is_continue);

        $php_path = '/path/to/php';
        $runner = $this->instance(array(
            'php_path'      => $php_path,
            'is_logging'    => true,
            'is_stdout'     => true,
            'is_continue'   => true,
        ));
        $this->assertEquals($php_path, $runner->php_path);
        $this->assertEquals(true, $runner->is_logging);
        $this->assertEquals(true, $runner->is_stdout);
        $this->assertEquals(true, $runner->is_continue);

        $runner = $this->instance(array(
            'is_logging'    => true,
            'is_continue'   => true,
        ));
        $this->assertEquals('php', $runner->php_path);
        $this->assertEquals(true, $runner->is_logging);
        $this->assertEquals(false, $runner->is_stdout);
        $this->assertEquals(true, $runner->is_continue);
    }

    public function test_run_success() {
        $runner = $this->instance();
        $exit_code = $runner->execute('example_group1');
        $this->assertEquals(0, $exit_code);
    }

    public function test_run_throw() {
        $runner = $this->instance();
        $exit_code = $runner->execute('throw');
        $this->assertEquals(1, $exit_code);
    }

    public function test_run_typo() {
        $runner = $this->instance();
        $exit_code = $runner->execute('unknown');
        $this->assertEquals(255, $exit_code);
    }

    public function test_run_singleton() {
        $exit_code = RunTasks_Runner::run('example_group1', array(), 'example.yml');
        $this->assertEquals(0, $exit_code);

        $exit_code = RunTasks_Runner::run('throw', array(), 'example.yml');
        $this->assertEquals(1, $exit_code);

        $exit_code = RunTasks_Runner::run('unknown', array(), 'example.yml');
        $this->assertEquals(255, $exit_code);
    }

    public function function_run_include() {
        $runner = $this->instance();

        $exit_code = $runner->execute('example_group2');
        $this->assertEquals(0, $exit_code);

        $exit_code = $runner->execute('example_group3');
        $this->assertEquals(0, $exit_code);

        $exit_code = $runner->execute('example_group4');
        $this->assertEquals(0, $exit_code);

        $exit_code = RunTasks_Runner::run('example_group2', array(), 'example.yml');
        $this->assertEquals(0, $exit_code);

        $exit_code = RunTasks_Runner::run('example_group3', array(), 'example.yml');
        $this->assertEquals(0, $exit_code);

        $exit_code = RunTasks_Runner::run('example_group4', array(), 'example.yml');
        $this->assertEquals(0, $exit_code);
    }

    public function instance(array $properties = array()) {
        $runner = new RunTasks_Runner($properties, 'example.yml');
        return $runner;
    }
}
