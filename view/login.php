<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    class loginView {
        public $form;

        public function output(){
            $this->form = "
                <fieldset><legend>Welcome To EduGarden</legend>
                    <form action = " . htmlspecialchars($_SERVER["REQUEST_URI"]) . " method='post'>
                        Name: <input type='text' name='userName'required/><br />
                        Password: <input type='password' name='passWd' required/><br />
                        <input type='submit' id='logging' name='login' class='sub-bn' value='Login'/>
                    </form>
                </fieldset>";
            echo $this->form;
        }
    }
?>