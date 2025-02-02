<?php
    /**
     * @param
     */
    
    session_start(); 
    require 'budget_report.php';
    require 'budget_calc.php';

    if (!isset($_SESSION['purchases'])) {
        $_SESSION['purchases'] = [];
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_name'])) {
        $itemName = trim($_POST['item_name']);
        $itemPrice = intval($_POST['item_price']);
        $itemType = trim($_POST['item_type']);
        $itemLink = !empty($_POST['item_link']) ? trim($_POST['item_link']) : null;
    
        // Validate inputs
        if (empty($itemName) || $itemPrice <= 0 || empty($itemType)) {
            echo "All required fields must be filled correctly.<br>";
        } else {
   
            $_SESSION['purchases'][] = [
                "item_name" => $itemName,
                "item_price" => $itemPrice,
                "item_type" => $itemType,
                "item_link" => $itemLink,
            ];   
            // echo(count($_SESSION['purchases']));        
        }
    }
    if(isset($_POST['element'])){
        $p = $_SESSION['purchases'];
        $i = intval($_POST['element']);
        //print_r($_SESSION['purchases']);
        removePurchase($i);
        //print_r($_SESSION['purchases']);

    }

    if (!isset($_SESSION['finalized_purchases'])) {
        $_SESSION['finalized_purchases'] = [];
    }
    
    if (isset($_POST['addtodb'])) {
        $i = intval($_POST['addtodb']);
        $p = $_SESSION['purchases'][$i];
        $_SESSION['finalized_purchases'][] = $p;
        if (isset($p['item_link'])) {
            insertNewPurchase_Link($pdo, $p['item_name'], $p['item_price'], $p['item_type'], $p['item_link'], $_SESSION['username']);
        } else {
            insertNewPurchase_NoLink($pdo, $p['item_name'], $p['item_price'], $p['item_type'], $_SESSION['username']);
        }
    
        removePurchase($i);
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Budget Calculator</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="./ui_styles.css">
    <script>
        //function to validate file input
        function validateFileInput(event) {
            const fileInput = document.getElementById('textfile');
            if (fileInput.value === '') {
                alert('Please choose a file before submitting.');
                event.preventDefault(); // Prevent the form from submitting
            }
        }
    </script>
</head>
<body>
    <h1>
        Add purchase:
</h1>
<form action="./purchase_interface.php" method="post"> <!--Form action shoud be directed to budget_index-->
    <label for="item_name">Item name: </label><br>
    <input type="text" id="item_name" name="item_name" required><br>

    <label for="price">Item price: </label><br>
    $<input type="number" id="item_price" name="item_price" required><br>

    <label for="item_type">Item type:</label>
    <select select name="item_type" id="item_type">
        <option value="Housing">Housing</option>
        <option value="Utilities">Utilities</option>
        <option value="Groceries">Groceries</option>
        <option value="Other">Other</option>
        <option value="Wants">Wants</option>
  </select><br>
    
    <label for="link">Link to product (optional): </label><br>
    <input type="text" id="link" name="item_link"><br>

    <button type="submit">Add purchase</button>
</form>

<form action="./read_file.php" method="post" enctype="multipart/form-data" onsubmit="validateFileInput(event)"> <!-- -->
    <label for="textfile">Choose a product file: </label>
    <br>
    <input type="file" id="textfile" name="textfile" accept=".txt, .csv, .docx" /> <!--accept attribute controls what files are allowed to be put in-->
    <br>
    <input type="submit" name="submitFile" value="Submit file">
</form>

<br>

<?php 
    $your_purchases = displayPurchases();
    echo $your_purchases;   
?>
<br>
<a href="budget_index.php"><button>Back to Budget Index</button></a>  
</body>
