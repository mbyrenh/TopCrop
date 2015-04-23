<?php

class Logger {

    private $logName = 'test.log';
    private $handle = null;

    public function __construct () {

        $this -> handle = fopen ($this -> logName, 'a');

    }

    public function log ($msg) {
        fwrite ($this -> handle, $msg . "\n");
    }

    public function __destruct () {

        fclose ($this -> handle);

    }

}

?>
