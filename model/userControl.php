<?php
	/*
	* useControl.php holds abstract class for classes
	* that allow users to actively interact with the database,
	* whether by viewing or modifying it.
	*/

    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
          exit;
    }
	abstract class userControl {
		abstract protected function input($item, $quantity);
		abstract public function display();
		abstract protected function add();
		abstract protected function remove();
	}
?>