<?php 
    require_once('siteInfo.php');

    class productPage implements siteInfo {
        private $item;
        private $session;
        private $result;
        public $display = array();

        public function __construct($item, $session) {
            $item = preg_replace('/_/',' ', $item);
            $this->item = $item;
            $this->session = $session;
        }

        public function getContent() {
            $this->result = $this->session->db->prepare("SELECT productId, productName, sciName, size, price, description FROM products WHERE productName LIKE ?");
            $this->result->bind_param("s", $this->item);
            $this->result->execute();
            $this->result->bind_result($pid, $pN, $sN, $sz, $pz, $desc);
            while ($this->result->fetch()) {
                $this->display[] = [
                    "productId" => $pid,
                    "productName" => $pN,
                    "sciName" => $sN,
                    "size" => $sz,
                    "price" => $pz,
                    "description" => $desc,
                ];
            }
            $this->result->close();
        }

        public function setContent() {
            $this->getContent();
            return $this->display;
        }
    }
?>