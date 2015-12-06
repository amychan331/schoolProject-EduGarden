<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    class dashboardView {
    private $model;

    public function __construct(Dashboard $model) {
        $this->model = $model;
    }

    public function output(){
        //
    }

}
?>