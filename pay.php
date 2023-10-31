<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();

if (!isset($_SESSION['payment_id'])) {
    header('Location: index.php');
    exit();
  }

$payment_id = $_SESSION['payment_id'];

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$stripeSecret = $_ENV['STRIPE_SECRET'];


$host = $_ENV['DBHOST'];
$dbname = $_ENV['DBNAME'];
$username = $_ENV['DBUSER'];
$password = $_ENV['DBPASS'];

$successurl = $_ENV['SUCCESS_URL'];
$cancelurl = $_ENV['CANCEL_URL'];


$db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$stmt = $db->prepare('SELECT amount FROM payments WHERE payment_id = ?');
$stmt2 = $db->prepare('SELECT paymentname FROM payments WHERE payment_id = ?');
$stmt->execute([$payment_id]);
$stmt2->execute([$payment_id]);
$amount = $stmt->fetch();
$paymentname = $stmt2->fetch();


$stripe = new \Stripe\StripeClient($stripeSecret);



 $checkoutsession = $stripe->checkout->sessions->create([
    'payment_method_types' => ['card', 'ideal', 'bancontact', 'giropay', 'sofort', 'p24', 'eps', 'paypal'],
    'line_items' => [[
      'price_data' => [
        'currency' => 'eur',
        'unit_amount' => $amount['amount'],
        'product_data' => [
          'name' => $paymentname['paymentname'],
          'description' => "Payment ID: " . $payment_id,
        ],
      ],
      'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $successurl . "?session_id={CHECKOUT_SESSION_ID}",
    'cancel_url' => $cancelurl,
    ]);





$newamount = $amount['amount'] / 100;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pay</title>
  <link rel="stylesheet" href="./style/pay.css">
</head>
<body>
  <div>
  <h2 id="paylabel">Name of payment: <?php echo $paymentname['paymentname'] ?></h2>
  <h1 id="paylabel">Amount of payment: €<?php echo $newamount ?></h1>
  <a id="pay-button" href="<?php echo $checkoutsession->url ?>">Pay Now</a>
  <a id="paylabel2" href="./logout.php">Wrong payment click here</a>
  </div>
</body>
</html>