# Stripe Payments Dashboard

This is a dashboard that helps you with sending payments to clients

Preview:


# Setup:

Install composer
```
composer install
```

Fill your .env file with your data

Put these mysql querys in your database
```
CREATE TABLE `payments` (
	`payment_id` VARCHAR(50) NULL DEFAULT NULL,
	`amount` VARCHAR(50) NULL DEFAULT NULL,
	`paymentname` VARCHAR(50) NULL DEFAULT NULL,
	`paid` VARCHAR(50) NULL DEFAULT NULL
);
```
```
CREATE TABLE `users` (
	`username` VARCHAR(50) NULL DEFAULT NULL,
	`password` VARCHAR(50) NULL DEFAULT NULL
);
```

Insert a row into the users table to use the Dashboard you can do it with this query:
```
INSERT INTO `users` (username, password) VALUES ('changemeusername', 'changemepassword');
```
Put your preferred username in 'changemeusername'
Put your preferred password in 'changemepassword'

# Your installation is completed!
