<?php 
// Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
          exit;
    }
    
    require_once('siteInfo.php');

    class cartPanel implements siteInfo {
        private $cartStatus;
        private $result;
        private $display = array();

        public function __construct($user, $session) {
            $this->user= $user;
            $this->session = $session;
        }

        public function getContent() {
            // Get the cart id.
            $this->cartStatus = $this->session->db->prepare("SELECT cartId FROM user_info WHERE data = ?");
            $this->cartStatus->bind_param("s", $this->user);
            $this->cartStatus->execute();
            $this->cartStatus->store_result();
            $this->cartStatus->bind_result($cid);
            $this->cartStatus->fetch();

            // Get the product id and quantity in json form.
            $this->result = $this->session->db->prepare("SELECT productId, quantity FROM carts WHERE cartID = ?");
            $this->result->bind_param("i", $cid);
            $this->result->execute();
            $this->result->bind_result($p, $q);
            while ($this->result->fetch()) {
                $this->display[] = [
                    "pid" => $p,
                    "qty" => $q,
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
    if (! empty($_GET)) {
        $search = new cartPanel($session->name, $session);
        $search->setContent();
    }
?>