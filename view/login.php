<?php 
    class loginView {
        public $form;

        public function output(){
            $this->form = "
                <fieldset><legend>Welcome To EduGarden</legend>
                    <form action = " . htmlspecialchars($_SERVER["REQUEST_URI"]) . " method='post'>
                        Name: <input type='text' class='inputBox' name='userName'required/><br />
                        Password: <input type='password' class='inputBox' name='passWd' required/><br />
                        <input type='submit' id='logging' name='login' class='sub-bn' value='Login'/>
                    </form>
                </fieldset>";
            echo $this->form;
        }
    }
?>