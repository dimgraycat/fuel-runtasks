<?php
namespace Fuel\Tasks;

class RunTasksExample {
    public function run() {
    }

    public function test1($dt, $min) {
        print "dt: $dt\n";
        print "min: $min\n";
    }

    public function test2($args) {
        print "YmdHM: $args\n";
    }

    public function test3($args) {
        print "datetime: $args\n";
    }

    public function test4() {
        print 'FUEL_ENV: '.\Fuel::$env."\n";
    }

    public function test5() {
        throw new \Exception('test4 died.');
    }
}
