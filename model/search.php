<?php 
    require_once('siteInfo.php');

    class Search implements siteInfo {
        private $term;
        private $db;
        private $result;
        private $search;
        protected $display = array();
        private $pN;
        private $sN;

        public function __construct($term, $db) {
            $this->term = "%{$term}%";
            $this->db = $db;
        }

        public function getContent() {
            $this->result = $this->db->prepare("SELECT productId, productName, sciName FROM `products` WHERE productName LIKE ? or sciName LIKE ?");
            $this->result->bind_param("ss", $this->term, $this->term);
            $this->result->execute();
            $this->result->bind_result($pid, $pN, $sN);
            while ($this->result->fetch()) {
                $this->display[] = [
                    "pid" => $pid,
                    "productName" => $pN,
                    "sciName" => $sN,
                ];
            }
            return $this->display;
        }

        public function setContent() {
            $this->getContent();
            echo json_encode($this->display);
            $this->result->close();
        }
    }

    if (! empty($_GET['q'])) {
        require_once('../../private/dbvar.inc');
        $db = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase) or die("Database not connecting.");
        unset($dbuser, $dbpass);
        $search = new Search($_GET['q'], $db);
        $search->setContent();
        $db->close();
    }
?>