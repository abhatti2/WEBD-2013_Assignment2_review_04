<?php

/*******w******** 
    
    Name:Dawson Zorn
    Date:2024-05-27
    Description: Using php Server-Side User Input Validation

****************/
function validate_input(){
    $errorFlag = false;

    //email validation
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if(!$email){
        $errorFlag = true;
    }

    //postalcode validation
    $postalCode = filter_input(INPUT_POST, 'postal', FILTER_VALIDATE_REGEXP, array(
        'options' => array(
            'regexp' => '/^[ABCEGHJ-NPRSTVXY][0-9][ABCEGHJ-NPRSTV-Z][ ]?[0-9][ABCEGHJ-NPRSTV-Z][0-9]$/'
        )
    ));
    if(!$postalCode){
        $errorFlag = true;
    }
    
    //credit card number sanitization
    $creditCardNum = filter_input(INPUT_POST, 'cardnumber', FILTER_SANITIZE_NUMBER_INT);

    //credit card  length validation
    $creditCardLength = strlen($_POST['cardnumber']);
    if($creditCardLength > 10 || $creditCardLength < 1){
        $errorFlag = true;
    }

    //credit card month sanitization
    $creditCardMonth = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_NUMBER_INT);
    if($creditCardMonth < 1 || $creditCardMonth > 12){
        $errorFlag = true;
    }

    //card year validation
    $creditCardYear = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT);
    $currentDate = date('Y');
    if($creditCardYear < $currentDate || $creditCardYear > (int)$currentDate + 5){
        $errorFlag = true;
    }

    //card type validation
    $cardType = isset($_POST['cardtype']);
    if(!$cardType){
        $errorFlag = true;
    }

     // Information validation
     $requiredFields = ['fullname', 'cardname', 'address', 'city'];
     foreach ($requiredFields as $field) {
         if(empty($_POST[$field])){
            $errorFlag = true;
         }
     }

    //province validation
    $province = $_POST['province'];
    $provinceArray = ['AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'ON', 'PE', 'QC', 'SK', 'NT', 'NU', 'YT'];
    if(!in_array($province, $provinceArray)){
        $errorFlag = true;
    }

     // Quantity validation
     $quantities = ['qty1', 'qty2', 'qty3', 'qty4', 'qty5'];
     foreach ($quantities as $quantityField) {
         $quantity = filter_input(INPUT_POST, $quantityField, FILTER_VALIDATE_INT);
         if ($quantity === false && $_POST[$quantityField] !== '') {
             $errorFlag = true;
         }
     }
    
    return $errorFlag;
}


function get_order_summary(){
    $items = [
        'qty1' => ['name' => 'MacBook', 'price' => 1899.99],
        'qty2' => ['name' => 'Razer Mouse', 'price' => 79.99],
        'qty3' => ['name' => 'WD Hard Drive', 'price' => 179.99],
        'qty4' => ['name' => 'Nexus 7', 'price' => 249.99],
        'qty5' => ['name' => 'Yamaha DD-45', 'price' => 119.99]
    ];
    $order_summary = [];

    foreach ($items as $key => $value) {
        $quantity = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
        if ($quantity && $quantity > 0) {
            $order_summary[] = [
                'name' => $value['name'],
                'quantity' => $quantity,
                'total' => $quantity * $value['price']
            ];
        }
    }

    return $order_summary;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Thanks for your order!</title>
</head>
<body>
    <?php if(!validate_input()): ?>
    
    <!-- Remember that alternative syntax is good and html inside php is bad --> 
    <div class="invoice">
  <h2>Thanks for your order <?= $_POST['fullname'] ?></h2>
  <h3>Here's a summary of your order:</h3>
  <table>
    <tbody>
    <tr>
      <td colspan="4"><h3>Address Information</h3>
      </td>
    </tr>
    <tr>
      <td class="alignright"><span class="bold">Address:</span>
      </td>
      <td> <?= $_POST['address']?></td>
      <td class="alignright"><span class="bold">City:</span>
      </td>
      <td><?= $_POST['city']?></td>
    </tr>
    <tr>
      <td class="alignright"><span class="bold">Province:</span>
      </td>
      <td><?= $_POST['province']?></td>
      <td class="alignright"><span class="bold">Postal Code:</span>
      </td>
      <td><?= $_POST['postal']?></td>
    </tr>
    <tr>
      <td colspan="2" class="alignright"><span class="bold">Email:</span>
      </td>
      <td colspan="2"><?= $_POST['email']?></td>
    </tr>
    </tbody>
  </table>
  <table>
    <tbody>
    <tr>
      <td colspan="3"><h3>Order Information</h3>
      </td>
    </tr>
    <tr>
      <td><span class="bold">Quantity</span>
      </td>
      <td><span class="bold">Description</span>
      </td>
      <td><span class="bold">Cost</span>
      </td>
    </tr>
    <?php
    $order_summary = get_order_summary();
        $totalCost = 0; //initialize the total cost
        //loop through each item in array
        foreach($order_summary as $item):
            $totalCost += $item['total']; //add item total to total cost
    ?>
    <tr>
        <td><?= $item['quantity'] ?></td> <!-- display quantity-->
        <td><?= $item['name'] ?></td> <!-- display name of item-->
        <td class="alignright">$<?= $item['total']?></td>
    </tr>
        <?php endforeach; ?>
    <tr>
        <td colspan="2" class="alignright"><span class="bold">Totals</span></td>
        <td class="alignright">
            <span class="bold">$<?= $totalCost ?></span></td>
        </tr>
    </tbody>
    </table>
    <?php else:
    ?>
        <h4>The form could not be processed</h4>
    <?php endif?>
</div>
</html>