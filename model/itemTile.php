<?php 

    require_once('siteInfo.php');

    class itemTile implements siteInfo {
        private $term;
        private $session;
        private $result;
        public $display = array();
        private $counter = 0;

        public function __construct($session) {
            $this->session = $session;
        }

        public function getContent() {
            $this->result = $this->session->db->prepare("SELECT p.productId, p.productName, p.sciName, p.price FROM products p
                WHERE p.productId IN (SELECT i.productId FROM inventory i )
            ");
            $this->result->execute();
            $this->result->bind_result($pid, $pN, $sN, $pz);
            while ($this->result->fetch()) {
                if (array_key_exists($pN, $this->display)) {
                    $this->display[$pN]["price"][] = $pz;
                } else {
                    $this->display += [
                        $pN => [         //Product Name was used to id array because not all item have sciName,
                            "sciName" => [$sN],  //and not all item have only 1 sku (1 item with different size = multiple pid).
                            "price" => [$pz]
                        ]
                    ];
                }
            }
            $this->result->close();
        }

        public function setContent() {
            $this->getContent();
            return $this->display;
        }
    }

?>