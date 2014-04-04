<?php
namespace Fuel\Tasks;

class Example {
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
        print 'FUEL_ENV: '.\Fuel::$env."\n";
    }

    public function test4($args) {
        print 'FUEL_ENV: '.\Fuel::$env."\n";
    }
}
