<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Database Query</title>
    <link rel="stylesheet" href="css/dbquery.css">
  </head>
  <body>


    <!-- Before loading the page, check if the form is already submitted before -->
    <?php
    //Include functions:
    include 'functions.php';

    // Error array that might get populated if there is error
    $errors = array();

    $orderNumber = "";
    $dateFrom = "";
    $dateTo = "";

    //Check if the form has been submitted before
    if(isset($_POST['submit'])){

      //Check Order Number if exist,
      if (isset($_POST['orderNumber'])) {
        //Check if empty
        if (!empty($_POST['orderNumber'])) {
          //Assign the trimmed number to orderNumber
        	$orderNumber = trim($_POST['orderNumber']);
          //Validate if it is numeric
        	if (!is_numeric($orderNumber)) {
        		$errors['orderNumber'] = "Order Number must be numeric value!";
        	}
        }
      }

      //Check if dataFrom Exist
      if (isset($_POST['dateFrom'])) {
        //Check if empty
        if (!empty($_POST['dateFrom'])) {
        	$dateFrom = trim($_POST['dateFrom']);
          //Using Date Format Check Function from https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
          if (!validateDate($dateFrom, $format = 'Y-m-d')) {
            $errors['dateFrom'] = "Please use the specified format (YYYY-MM-DD)";
          }
        }
      }

      //Check if dataTo Exist
      if (isset($_POST['dateTo'])) {
        //Check if empty
        if (!empty($_POST['dateTo'])) {
        	$dateTo = trim($_POST['dateTo']);
          //Using Date Format Check Function from https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
          if (!validateDate($dateTo, $format = 'Y-m-d')) {
            $errors['dateTo'] = "Please use the specified format (YYYY-MM-DD)";
          }
        }
      }

      //Check if no checkBoxes are checked
      if (!isset($_POST['chkOrderNumber']) &&
      !isset($_POST['chkOrderDate']) &&
      !isset($_POST['chkShippedDate']) &&
      !isset($_POST['chkProductName']) &&
      !isset($_POST['chkProductDescription']) &&
      !isset($_POST['chkQuantityOrdered']) &&
      !isset($_POST['chkPriceEach'])) {
        //There is nothing to select :/
        $errors['fields'] = "Please select some fields to display!";
      }
    }
    ?>

    <!-- page content -->

    <div class="container">
      <div class="row">
        <h2>Query</h2>
      </div>

      <!-- Start Of Form -->
      <!-- Form that direct to itself -->
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

        <div class="row">
          <!-- Left Column -->
          <div class="column">
            <h3>Select Order Parameters</h3>
            <label>Order Number:</label>
            <!-- If field exist, it would be restored back to the input -->
            <input name="orderNumber" value="<?php if (isset($_POST['orderNumber'])) echo htmlspecialchars($_POST['orderNumber']); ?>" type="text" size="20" />
            <label>or</label>
            <br>
            <!-- span for error message -->
            <span id='orderNumber_error' class='error'>
      								<?php if (isset($errors['orderNumber'])){
      										echo $errors['orderNumber'];
      									}
      								?></span>
            <?php if (isset($errors['orderNumber'])) echo "<br>"; ?>
            <br>
            <label>Order Date (YYYY-MM-DD)</label>
            <br>
            <label>from:</label>
            <!-- If field exist, it would be restored back to the input -->
            <input name="dateFrom" value="<?php if (isset($_POST['dateFrom'])) echo htmlspecialchars($_POST['dateFrom']); ?>" type="text" size="20" />
            <label>to:</label>
            <!-- If field exist, it would be restored back to the input -->
            <input name="dateTo" value="<?php if (isset($_POST['dateTo'])) echo htmlspecialchars($_POST['dateTo']); ?>" type="text" size="20" />
            <br>
            <!-- span for error message -->
            <span id='date_error' class='error'>
      								<?php
                      //If there is error, display it below
                      if (isset($errors['dateFrom']) && isset($errors['dateTo'])){
                        //If both fields got wrong, only display one
                        echo @$errors['dateFrom'];
                      } else if (isset($errors['dateFrom']) || isset($errors['dateTo'])){
                          //Suppress the error if the error doesn't exist
      										echo @$errors['dateFrom'];
                          echo @$errors['dateTo'];
    									}
      								?></span>
            <?php if (isset($errors['dateFrom']) || isset($errors['dateTo'])) echo "<br>"; ?>
          </div>

          <!-- Right Column -->
          <!-- Contains all checkBoxes for displaying fields -->
          <!-- Each of these box if checked before, would remain check after submit -->
          <div class="column" id="rightColumn">
            <h3>Select Columns to Display</h3>
            <input type="checkbox" name="chkOrderNumber" value="chkOrderNumber" <?php if (isset($_POST['chkOrderNumber'])) echo "checked"; ?>>
            <label> Order Number</label>
            <br>
            <input type="checkbox" name="chkOrderDate" value="chkOrderDate" <?php if (isset($_POST['chkOrderDate'])) echo "checked"; ?>>
            <label> Order Date</label>
            <br>
            <input type="checkbox" name="chkShippedDate" value="chkShippedDate" <?php if (isset($_POST['chkShippedDate'])) echo "checked"; ?>>
            <label> Shipped Date</label>
            <br>
            <input type="checkbox" name="chkProductName" value="chkProductName" <?php if (isset($_POST['chkProductName'])) echo "checked"; ?>>
            <label> Product Name</label>
            <br>
            <input type="checkbox" name="chkProductDescription" value="chkProductDescription" <?php if (isset($_POST['chkProductDescription'])) echo "checked"; ?>>
            <label> Product Description</label>
            <br>
            <input type="checkbox" name="chkQuantityOrdered" value="chkQuantityOrdered" <?php if (isset($_POST['chkQuantityOrdered'])) echo "checked"; ?>>
            <label> Quantity Ordered</label>
            <br>
            <input type="checkbox" name="chkPriceEach" value="chkPriceEach" <?php if (isset($_POST['chkPriceEach'])) echo "checked"; ?>>
            <label> Price Each</label>
            <br>
            <!-- span for error message -->
            <span id='chkBox_error' class='error'>
      								<?php
                      //If there is error, display it below
                      if (isset($errors['fields'])) {
      										echo $errors['fields'];
    									}
      								?></span>
          </div>
        </div>

        <!-- Submit Button -->
        <input class="button" type="submit" name="submit" value="Search Orders"/>

        <!-- End Of Form -->
      </form>

      <!-- Start of Query Statement Construction -->
      <?php

      //If submission exist and no error
      if(isset($_POST['submit']) && count($errors) == 0){

        //Start the curry(query, sorry, I am super hungry when coding this) with SELECT
        $temp = "SELECT ";

        //If the checkboxes are checked, add an extra part behind the query statement
        if (isset($_POST['chkOrderNumber'])) $temp .= "orders.orderNumber, ";
        if (isset($_POST['chkOrderDate'])) $temp .= "orders.orderDate, ";
        if (isset($_POST['chkShippedDate'])) $temp .= "orders.shippedDate, ";
        if (isset($_POST['chkProductName'])) $temp .= "products.productName, ";
        if (isset($_POST['chkProductDescription'])) $temp .= "products.productDescription, ";
        if (isset($_POST['chkQuantityOrdered'])) $temp .= "orderdetails.quantityOrdered, ";
        if (isset($_POST['chkPriceEach'])) $temp .= "orderdetails.priceEach, ";

        //Used substr to limit the length of the string, from 0 (start) to -2 (2 digits before the end), therefore the , " at the end is gone
        $query = substr($temp, 0, -2);
        //Add back the space at the end
        $query .= " ";

        //Continue to add the next parts FROM
        $query .= "FROM orders ";

        //Only Join orderdetails If related checkBox are checked
        // if (isset($_POST['chkQuantityOrdered']) || isset($_POST['chkPriceEach'])) {

          //Start First Inner Join with orderdetails table
          $query .= "INNER JOIN orderdetails ";

          //Start defining the join conditions with ON
          $query .= "ON orders.orderNumber = orderdetails.orderNumber ";

        // }

        //Only if the products table related fields are selected, then join the products table
        if (isset($_POST['chkProductName']) || isset($_POST['chkProductDescription'])) {

          //Joining second table
          $query .= "INNER JOIN products ";

          //Condition for second table
          $query .= "ON products.productCode = orderdetails.productCode ";

        }

        //Starting Last Part of the Query - WHERE Clause
        //If either of the data was filled, that specify order number and date, add the WHERE statement
        if (isset($_POST['orderNumber']) && !empty($_POST['orderNumber']) || isset($_POST['dateFrom']) && !empty($_POST['dateFrom']) || isset($_POST['dateTo']) && !empty($_POST['dateTo'])) {
          $query .= "WHERE ";
        }

        //If order Number is filled, add this query
        if (isset($_POST['orderNumber']) && !empty($_POST['orderNumber'])) {
          $query .= "orders.orderNumber = '{$orderNumber}' ";
        }

        //If order Date From is filled, add this part
        if (isset($_POST['dateFrom']) && !empty($_POST['dateFrom'])) {
          //If orderNumber is filled before this,  need the AND clause
          if (isset($_POST['orderNumber']) && !empty($_POST['orderNumber'])) {
            $query .= "AND ";
          }
          //Add the order Date >= clause with dateForm value
          $query .= "orders.orderDate >= '{$dateFrom}' ";
        }

        //If order Date To is filled, add this part
        if (isset($_POST['dateTo']) && !empty($_POST['dateTo'])) {
          //If orderNumber or dateFrom is filled before this,  need the AND clause
          if (isset($_POST['orderNumber']) && !empty($_POST['orderNumber']) || isset($_POST['dateFrom']) && !empty($_POST['dateFrom'])) {
            $query .= "AND ";
          }
          //Add the order Date <= clause with dateTo value
          $query .= "orders.orderDate <= '{$dateTo}' ";
        }

        //Finally, echo the query out
        echo "<h4>SQL Query</h4>";
        echo $query;

      }
      ?>


      <!-- Start of Connecting and executing queries to database -->
      <?php
      // create new connection
      $dbhost = "localhost";
      $dbuser = "root";
      $dbpass = "";
      $dbname = "classicmodels";
      // Suppress if connection failed
      $connection = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

      // Test if connection succeeded
      if(mysqli_connect_errno()) {
        echo '<br>';
        die("Database connection failed: " .
             mysqli_connect_error() .
             " (" . mysqli_connect_errno() . ")"
        );
      }
      //Else it wouldn't run any of the code below

      //Execute Query and get $result
      //
      if (isset($_POST['submit']) && count($errors) == 0) {
        //Although there wouldn't be any error theoretically, I put @ suppression here to handle exception
        $result = @mysqli_query($connection, $query);

        //If executing query failed, print and stop the page
        if (!$result) {
      		die("Database query failed.");
      	} else {
          //Recreate Result header
          echo '<div class="row">';
            echo "<h2>Result</h2>";
          echo "</div>";
          echo '<table class="table">';

          //Giving the first row as table headers
          echo '<tr class="header">';
          //Tried to use column name as header name, automating the process, failed
          //Just hard coding the header values in
            // if (isset($_POST['chkOrderNumber'])) echo "<td>{$row->name}</td>";
            // if (isset($_POST['chkOrderNumber'])) echo '<td>'.mysql_field_name($result, 0).'</td>';
            if (isset($_POST['chkOrderNumber'])) echo "<td>orderNumber</td>";
            if (isset($_POST['chkOrderDate'])) echo "<td>orderDate</td>";
            if (isset($_POST['chkShippedDate'])) echo "<td>shippedDate</td>";
            if (isset($_POST['chkProductName'])) echo "<td>productName</td>";
            if (isset($_POST['chkProductDescription'])) echo "<td>productDescription</td>";
            if (isset($_POST['chkQuantityOrdered'])) echo "<td>quantityOrdered</td>";
            if (isset($_POST['chkPriceEach'])) echo "<td>priceEach</td>";
          echo '</tr>';

          //Each Loop of fetching data
        while ($row = @mysqli_fetch_assoc($result)) {

          //Make a new table row
          echo "<tr>";
          //For each field, if checkBox checked, display the fields
          if (isset($_POST['chkOrderNumber'])) echo "<td>".$row["orderNumber"]."</td>";
          if (isset($_POST['chkOrderDate'])) echo "<td>".$row["orderDate"]."</td>";
          if (isset($_POST['chkShippedDate'])) echo "<td>".$row["shippedDate"]."</td>";
          if (isset($_POST['chkProductName'])) echo "<td>".$row["productName"]."</td>";
          if (isset($_POST['chkProductDescription'])) echo "<td>".$row["productDescription"]."</td>";
          if (isset($_POST['chkQuantityOrdered'])) echo "<td>".$row["quantityOrdered"]."</td>";
          if (isset($_POST['chkPriceEach'])) echo "<td>".$row["priceEach"]."</td>";
          echo "</tr>";
        }
        echo "</table>";
        }
      }
      ?>

      <?php
      //If submission exist and no error
      if(isset($_POST['submit']) && count($errors) == 0){
        //Free $result from memory at the end
        mysqli_free_result($result);
        // Close database connection
        mysqli_close($connection);
      }
      ?>

      <!-- Printing all the errors for debugging -->
      <?php
    	// print_r($errors);
      ?>

    </div>


  </body>
</html>
