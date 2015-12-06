// Codes & Notes From http://www.wikihow.com/Create-a-Secure-Session-Managment-System-in-PHP-and-MySQL

class session {

	function __construct() {
	    //session_set_save_handler sets user-level session storage functions for storing & retrieving data associated with a session.
	    //The argument order is: (callable $open, callable $close, callable $read, callable $write, callable $destroy, callable $gc [, callable $create_sid]).
	    //register_shutdown is one of session_set's prototype. It register a function for execution on shutdown.
	    //In this case, session_write_close, which write session data and end session.
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'gc'));
		register_shutdown_function('session_write_close');
    }

    function start_session($session_name, $secure) {
        $httponly = true;
        $session_hash = 'sha512';
        //ini_set sets the value of configuration option
        if (in_array($session_hash, hash_algos())) {   //hash_algos return a list of registered hashing algorithms.
            ini_set('session.hash_function', $session_hash);   //session.hash_function allows one to specify the hash algorithm used to generate the session IDs.
        }
        ini_set('session.hash_bits_per_character', 5); //session.hash_bits_per_character defines how many bits are stored in each char when converting the binary hash data to readable.
        init_set('session.use_only_cookies', 1); //session.use_only_cookies with 1(true) specify module will only use cookies to store the session id on client side, preventing attacks of passing session ids in URL.

        $cookieParams = session_get_cookie_params(); //This get the session cookie parameters. Below sets it.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); //$secure here means cookie should only be sent over secure connections. Httponly means cookie can only be accessed via HTTP protocol.
        session_name($session_name);
        session_start();
        session_regenerate_id(true); //update the current session id with a newly generated one.
    }

    function open() {
        $host = 'localhost';
        $user = 'sec_user';
        $pass = 'noissesRandom';
        $name = 'secure_sessions';
        $mysqli = new mysqli($host, $user, $pass, name);
        $this->db = $mysqli;
        return true;
    }

    function close() {
        $this->db->close();
        return true;
    }

    function read($id) {
        if(!isset($this->read_stmt)) {
            //Prepares a statement for execution and returns a statment object.
        	$this->read_stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ? LIMIT 1");
        }
        $this->read_stmt->bind_params('s', $id); //binds variable to a prepared statment (read_stmt) as parameters.
        $this->read_stmt->execute(); //executes a prepared query
        $this->read_stmt->store_result(); //transfer a result set from the last query.
        $this->read_stmt->bind_result($data); //binds variables to a prepared statment for result storage.
        $this->this_stmt->fetch(); //fetches the next row from a result set.
        $key = $this->getkey($id);
        $data = $this->decrypt($data, $key);
        return $data;
    }

    function write($id, $data) {
        $key = $this->getkey($id); //getkey() is a function to be created, returning a key to be encrypted.
        $data = $this->encrypt($data, $key); //encrypt() is function to be created, returning an encrypted data.

        $time = time(); //current time in seconds since Unix Epoch (01/02/1970 00:00:00 GMT).
        if(!isset($this->w_stmt)) {
            $this->w_stmt = $this->db->prepare("REPLACE INTO session (id, set_time, data, session_key) VALUES (?, ?, ?, ?)");
        }

        $this->w_stmt->bind_param('siss', $id, $time, $data, $key);
        $this->w_stmt->execute();
        return true;
    }

    function destroy($id) {
        if(!isset($this->delete_stmt)) {
            $this->delete_stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        }
        $this->delete_stmt->bind_param('s', $id);
        $this->delete_stmt->execute();
        return true;
    }

    function gc($max) {
        if(!isset($this->gc_stmt)) {
            $this->gc_stmt = $this->db->prepare("DELETE FROM sessions WHERE set_time < ?");
        }
        $old = time() - $max;
        $this->gc_stmt->bind_param('s', $old);
        $this->gc_stmt->execute();
        return true;
    }

    private function getkey($id) {
        if(!isset($this->key_stmt)) {
            $this->key_stmt = $this->db->prepare("SELECT session_key FROM sessions WHERE id = ? LIMIT 1");
        }
        $this->key_stmt->bind_param('s', $id);
        $this->key_stmt->execute();
        $this->key_stmt->store_result();
        if($this->key_stmt->num_rows == 1) { //num_rows gets the number of rows in a result.
            $this->key_stmt->bind_result($key); //binds variables to a prepared statement for result storage.
            $this->key_stmt->fetch();
            return $key;
        } else {
            // hash() generates a hash value. Format is hash(selected algorithm, data, true for raw binary output & false for lowercase hexits output).
            $random_key = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
            return $random_key;
        }
    }

    private function encrypt($data, $key) {
        $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
        $key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
        // substr here returns the hashed string, starting at poisiton 0, length 32.
        // the hash() selected algorithm sha256 & applied it to data set, $salt.$key.$salt.
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        // IV stands for Initialization Vector, which is an unpredictable random number used to make sure when msg is encrypted twice, the ciphertext is always different.
        // This returns size of the iv belonging to cipher/mode combo where cipher is MCRYPT_RIJNDAEL_256 & mode is MCRYPT_MODE_ECB.
        // Cipher is an algorithm for performing encryption or decryption.
        // MCRYPT_RIJNDAEL_256 is a variant of Rijndael block cipher that use AES and 256 block size.
        // Constant ECB(Electronic CodeBook)'s data is short and random, suitable for random data like encrypting other keys.
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        // Creates an IV from a random source.
        // 1st parameter determines size of resulted iv.
        // 2nd parameter determines source. Here is from MCRYPT_RAND - a system random number generator.
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCYRPT_MODE_ECB, $iv));
        // base64_encode encodes given binary data with base64, taking about 33% more space than original data.
        // mcrypt_encrypt encrypts plaintext with given parameters.
        // Format is: mycrypt_encrypt(cipher choice, key, data, mcrypt_mode [, iv])
        return $encrypted;
	}

	private function decrypt($data, $key) {
	    $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
	    $key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
	    return $decrypted;
	}
}