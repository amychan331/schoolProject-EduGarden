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
        private $search;
        protected $display = array();
        private $pN;
        private $sN;

        public function __construct($term, $session) {
            $this->term = "%{$term}%";
            $this->session = $session;
        }

        public function getContent() {
            $this->result = $this->session->db->prepare("SELECT productId, productName, sciName FROM products WHERE productName LIKE ? or sciName LIKE ?");
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

    require_once('session.php');
    $session = new Session();
    if (! empty($_GET['q'])) {
        $search = new Search($_GET['q'], $session);
        $search->setContent();
    }
?>