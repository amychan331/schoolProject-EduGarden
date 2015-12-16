<?php 
// Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
          exit;
    }

    require_once('siteInfo.php');

    class Search implements siteInfo {
        private $term;
        private $session;
        private $result;
        public $display;

        public function __construct($term, $session) {
            $this->term = "%{$term}%";
            $this->session = $session;
        }

        public function setContent() {
            $this->result = $this->session->db->prepare("SELECT productName FROM products WHERE productName LIKE ?");
            $this->result->bind_param("s", $this->term);
            $this->result->execute();
            $this->result->store_result();
            $this->result->bind_result($p);
            while($this->result->fetch()){
                $this->display .= " " . $p;
            }
        }

        public function getContent() {
            $this->setContent();
            if ($this->display) {
                echo "Search result: " . $this->display;
            } else {
                echo "No search was found.";
            }
        }
    }

    require_once('session.php');
    $session = new Session();
    if (! empty($_GET['q'])) {
        $search = new Search($_GET['q'], $session);
        $search->getContent();
    }
?>