<?php
	/*
	* siteInfo.php holds interface for classes
	* that only retrieve information from databases.
	*/

    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
          exit;
    }

	interface siteInfo {
		public function setContent();
		public function getContent();
	}

?>