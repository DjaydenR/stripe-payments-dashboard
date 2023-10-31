<?php

require_once __DIR__ . '/vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$stripeSecret = $_ENV['STRIPE_SECRET'];

$stripe = new \Stripe\StripeClient($stripeSecret);

$csession = $stripe->checkout->sessions->retrieve(
  $_GET['session_id'],
  []
);

$lineItem = $stripe->checkout->sessions->allLineItems(
  $_GET['session_id'],
  ['limit' => 1]
);

$priceId = $lineItem->data[0]->price->id;

$price = $stripe->prices->retrieve(
  $priceId,
  []
);

$productid = $price->product;

$product = $stripe->products->retrieve(
  $productid,
  []
);

$splitted = explode("Payment ID: ", $product->description);

$completedsplitted = $splitted[1];

if ($csession->payment_status === 'paid'){
  $host = $_ENV['DBHOST'];
  $dbname = $_ENV['DBNAME'];
  $username = $_ENV['DBUSER'];
  $password = $_ENV['DBPASS'];

  $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $stmt = $db->prepare('UPDATE payments SET paid = "paid" WHERE payment_id = ?');
  $stmt->execute([$completedsplitted]);
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Payment Successful</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    div {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    h1 {
      font-size: 48px;
      font-weight: bold;
      color: #4CAF50;
      margin-bottom: 20px;
    }

    p {
      color: #333;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div>
    <h1>Payment Successful</h1>
    <p>Thank you for your payment!</p>
  </div>
</body>
</html>