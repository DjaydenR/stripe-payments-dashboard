<?php

require '../vendor/autoload.php';


session_start();

if (!isset($_SESSION['user'])) {
  header('Location: ./login.php');
  exit();
}

$dotenv = Dotenv\Dotenv::createImmutable('..');
$dotenv->load();

function insertIntoDatabase($pdo, $table, $data) {
  $keys = implode(',', array_keys($data));
  $values = ':' . implode(', :', array_keys($data));

  $sql = "INSERT INTO {$table} ({$keys}) VALUES ({$values})";

  $stmt = $pdo->prepare($sql);

  foreach ($data as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
  }

  $stmt->execute();

}

function generateRandomNumber($min = 100000, $max = 999999) {
  return rand($min, $max);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $host = $_ENV['DBHOST'];
  $dbname = $_ENV['DBNAME'];
  $username = $_ENV['DBUSER'];
  $password = $_ENV['DBPASS'];
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

  $randomNumber = generateRandomNumber();

  $data = [
    'amount' => $_POST['amount'] * 100,
    'paymentname' => $_POST['paymentname'],
    'payment_id' => $randomNumber,
    'paid' => "unpaid",
  ];

  insertIntoDatabase($pdo, 'payments', $data);

  echo '<p style="color: green; font-weight: bold;">Payment ID: ' . $randomNumber . '</p>';
}

$host = $_ENV['DBHOST'];
$dbname = $_ENV['DBNAME'];
$username = $_ENV['DBUSER'];
$password = $_ENV['DBPASS'];

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$sql = "SELECT * FROM payments";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// In your PHP code
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Dashboard</title>
    <style>
    .payments {
        display: flex;
        flex-direction: column;
        width: 50%;
        margin: 0 auto;
    }

    .payment {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .payment p {
        margin: 0;
        padding: 0;
    }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            width: 100%;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .logout-button {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payments Dashboard</h1>
        <form method="post">
            <div class="form-group">
                <label for="amount">Amount of payment:</label>
                <input type="text" id="amount" name="amount">
            </div>
            <div class="form-group">
                <label for="amount">Name of payment:</label>
                <input type="text" id="paymentname" name="paymentname">
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
        <a class="logout-button" href="../logout.php">Click here to logout</a>
    </div>
    <br><br>
    <h3 style="text-align: center;">All payments:</h3>
    <?php
        echo "<div class='payments'>";
        foreach ($payments as $payment) {
            $newamount = (int) $payment['amount'] / 100;
            echo "<div class='payment'>";
            echo "<p>Payment ID: " . $payment['payment_id'] . "</p>";
            echo "<p>Amount: €" . $newamount . "</p>"; 
            echo "<p>Payment Name: " . $payment['paymentname'] . "</p>";
            echo "<p>Payment Status: " . $payment['paid'] . "</p>";
            echo "</div>";
        }
        echo "</div>";
    ?>
</body>
</html>