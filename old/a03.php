<html>
<head><title>Assignment 3: Expand class to include visibility attributes</title></head>
<body>
<?php
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
        exit;
    }

    // For Week 3 Assignment, I redid Week 2 Assignment with different visibility.
    // I change the properties to either protected or private.
    // I created public getter, protected setter in formCreater class, and private setter in colorChanger class.
    // I made it so that should user select input correctly, the page would display 2 attempts:
    // First, it will try to access a public getter successfully.
    // Next, it will try to access a private setter, which should fail.
    // Should user not select the color, it will output an error page with a new form 
    // created by an extended class that will call a protected method(getInitialContent()) that uses a protected property($form).
    
    class formCreater {
        protected $form;

        public function __construct(){
            $this->form = <<< EOD
        <form name='selectionList' method='GET' action='a03.php'>  
        <p><label> Select a background color: </label>
        <select name="backgroundColor[]" size=4 multiple>
            <option value="white">White</option>
            <option value="red">Red</option>
            <option value="orange">Orange</option>
            <option value="yellow">Yellow</option>
            <option value="green">Green</option>
            <option value="blue">Blue</option>
            <option value="purple">Purple</option>
            <option value="black">Black</option>
        </select>
        <label> Select a text color: </label>
        <select name="textColor[]" size=4 multiple>
            <option value="white">White</option>
            <option value="red">Red</option>
            <option value="orange">Orange</option>
            <option value="yellow">Yellow</option>
            <option value="green">Green</option>
            <option value="blue">Blue</option>
            <option value="purple">Purple</option>
            <option value="black">Black</option>
        </select></p>
        </p>
        <p><input type="submit" id="submit" name="submit" value="Submit background and text color selections"></p>
        </form>
EOD;
        }

        protected function setInitialContent() {
            return $this->form;
        }

        public function getInitialContent() {
            return $this->setInitialContent();
        }
    }

    class reselectionForm extends formCreater {
        public function getReselectionForm() {
            echo $this->setInitialContent();
            echo "<script>document.getElementById('submit').value = 'Resubmit both background and text color.'</script>";
        }
    }

    class colorChanger {
        private $backgroundColor;
        private $textColor;

        public function __construct($bgColor, $txtColor) {
            $this->backgroundColor = $bgColor;
            $this->textColor = $txtColor;
        }

        private function setColor() {
            echo "<style>";
            echo "table { border-collapse: seperate; vertical-align: middle; }";
            echo "td, th { padding: 5px; text-align: center; }";
            echo "</style>";
            echo "<table>";
            // First, set up the table's background color.
            echo "<colgroup>";
                echo "<col span=1 style=\"background-color:white\">"; // Empty first column color.
                foreach ($this->backgroundColor AS $col)
                {
                echo "<col style=\"background-color:$col\">";
                    }
            echo "</colgroup>";
            // Second, set the first row: the header.
            echo "<tr> ";
                echo "<th> </th>"; // Empty first header cell.
                foreach ($this->backgroundColor AS $col)
                {
                    echo "<th> $col <br /> color </th>";
                }
            echo "</tr>";
            // Third, set the rows.
                foreach ($this->textColor AS $row) 
                {
                    echo "<tr>";
                        echo "<th> $row <br /> text </th>";
                        for ($i = 0; $i < count($this->backgroundColor); $i++) 
                        {
                            echo "<td style=\"color:$row\"> $row </td>";
                        }
                    echo "</tr>";
                }
            echo "</table>";
        }

        public function getColor() {
            $this->setColor();
        }

        public function getForm() {
            $this->getContent();
        }
    }


    if(isset($_GET['submit'])) {
        if(empty($_GET['backgroundColor']) || empty($_GET['textColor'])) {
            echo "Error: You have not selected a text or background color. <br />
                  Please reselect with the form below, created using an extended Class object calling a protected method:";
            $reselect = new reselectionForm();
            $reselect->getReselectionForm();            
        } else {
            $newColor = new colorChanger($_GET['backgroundColor'], $_GET['textColor']);
            echo "Begin by trying to access the color changer directly via public method getColor().<br />";
            $newColor->getColor();
            echo "Success! Let's try to access private method setColor() this time! Should output an error message:<br />";
            if ($newColor->setColor()) { 
                echo "...Success?<br />";
            }
        }
    } else {
        $defaultForm = new formCreater;
        echo $defaultForm->getInitialContent();
    }
?>
</body>
</html>