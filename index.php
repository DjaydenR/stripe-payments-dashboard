<?php

require_once __DIR__ . '/vendor/autoload.php';


session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['payment_id'])) {
        $error = 'The payment ID is required';
    } else {
        $payment_id = $_POST['payment_id'];

        $host = $_ENV['DBHOST'];
        $dbname = $_ENV['DBNAME'];
        $username = $_ENV['DBUSER'];
        $password = $_ENV['DBPASS'];
        
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $stmt = $db->prepare('SELECT * FROM payments WHERE payment_id = ?');
        $stmt->execute([$payment_id]);
        $stmt2 = $db->prepare('SELECT paid FROM payments WHERE payment_id = ?');
        $stmt2->execute([$payment_id]);
        $paid = $stmt2->fetch();
        $user = $stmt->fetch();
    
        if ($user) {

            if ($paid['paid'] === "paid") {
                $error = 'This payment is already paid.';
            } else {            
          $_SESSION['payment_id'] = $payment_id;
    
          header('Location: pay.php');
          exit();
            }
        } else {
          $error = 'Payment ID doenst exist';
        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>
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
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
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
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="payment_id">Payment ID:</label>
                <input type="text" id="payment_id" name="payment_id">
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>