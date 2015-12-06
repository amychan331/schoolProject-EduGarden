<html>
<head><title>Assignment 2: Creating a Working PHP Class</title></head>
<body>
<?php
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
        exit;
    }

    // This code will convert my procedural code for CS130A assignment 5 to classes.
    // It will create 2 classes.
    // One will set a property in its constructor, which will contain the input form html.
    // Its method, content(), will return the property.
    // Another will take the inputs from the form and set 2 properties in its constructor,
    // Its method, changeColor(), will echo the html for the color table.
    
    class formCreater {
        public $form;

        public function __construct(){
            $this->form = <<< EOD
        <form name='selectionList' method='GET' action='a02.php'>  
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
        <p><input type="submit" name="submit" value="Submit background and text color selections"></p>
        </form>
EOD;
        }

        public function content() {
            return $this->form;
        }
    }

    class colorChanger {
        public $backgroundColor;
        public $textColor;

        public function __construct($bgColor, $txtColor) {
            $this->backgroundColor = $bgColor;
            $this->textColor = $txtColor;
        }

        public function changeColor() {
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
    }


    if(isset($_GET['submit'])) {
        if(empty($_GET['backgroundColor']) || empty($_GET['textColor'])) {
            echo "Error: You have not selected a text or background color. Please reselect.";            
        } else {
            $newColor = new colorChanger($_GET['backgroundColor'], $_GET['textColor']);
            $newColor -> changeColor();
        }
    } else {
        $defaultForm = new formCreater;
        echo $defaultForm->content();
    }
?>
</body>
</html>