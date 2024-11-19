<?php
require "init.php";
include('navbar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address_line1 = $_POST['address_line1'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    try {
        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $address_line1,
                'city' => $city,
                'state' => $state,
                'postal_code' => $postal_code,
                'country' => $country,
            ],
        ]);

        echo "Customer created successfully. Customer ID: " . $customer->id;
    } catch (Exception $e) {
        echo "Error creating customer: " . $e->getMessage();
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1>Create Customer</h1>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="tel" id="phone" name="phone" class="form-control">
        </div>
        <div class="col-md-12">
            <label for="address_line1" class="form-label">Address</label>
            <input type="text" id="address_line1" name="address_line1" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="state" class="form-label">State</label>
            <input type="text" id="state" name="state" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="postal_code" class="form-label">Postal Code</label>
            <input type="text" id="postal_code" name="postal_code" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <input type="text" id="country" name="country" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Create Customer</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>
