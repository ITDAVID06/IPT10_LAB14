<?php
require 'init.php';

// Fetch customers and products from Stripe
try {
    $customers = $stripe->customers->all(['limit' => 10]); 
    $products = $stripe->products->all(['limit' => 10]); 
} catch (\Stripe\Exception\ApiErrorException $e) {
    die("Error fetching data: " . htmlspecialchars($e->getMessage()));
}

$errorMessage = '';
$successMessage = '';
$invoice = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer'];
    $selected_prices = $_POST['prices'] ?? [];

    if (!$customer_id || empty($selected_prices)) {
        $errorMessage = "Please select a customer and at least one product price.";
    } else {
        try {

            $invoice = $stripe->invoices->create([
                'customer' => htmlspecialchars($customer_id),
            ]);


            foreach ($selected_prices as $price_id) {
                $stripe->invoiceItems->create([
                    'customer' => htmlspecialchars($customer_id),
                    'price' => htmlspecialchars($price_id),
                    'invoice' => $invoice->id,
                ]);
            }


            $stripe->invoices->finalizeInvoice($invoice->id);
            $invoice = $stripe->invoices->retrieve($invoice->id);

            $successMessage = "Invoice created successfully!";
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $errorMessage = "Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1>Create Invoice</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
        <div class="my-3">
            <!-- Button to Download Invoice PDF -->
            <a href="<?= htmlspecialchars($invoice->invoice_pdf) ?>" target="_blank" class="btn btn-outline-primary">
                Download Invoice PDF
            </a>
            <!-- Button to Redirect to Payment Link -->
            <a href="<?= htmlspecialchars($invoice->hosted_invoice_url) ?>" target="_blank" class="btn btn-primary">
                Pay Invoice
            </a>
        </div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <!-- Customer Dropdown -->
        <div class="mb-3">
            <label for="customer" class="form-label">Select Customer</label>
            <select id="customer" name="customer" class="form-select" required>
                <option value="">-- Choose a customer --</option>
                <?php foreach ($customers->data as $customer): ?>
                    <option value="<?= htmlspecialchars($customer->id) ?>">
                        <?= htmlspecialchars($customer->name ?: $customer->email) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Products and Prices -->
        <div>
            <h5>Select Product Prices</h5>
            <?php foreach ($products->data as $product): ?>
                <?php
                // Fetch all prices for the product
                $prices = $stripe->prices->all(['product' => $product->id, 'limit' => 5]);
                $highest_price = null;

                // Find the highest price
                foreach ($prices->data as $price) {
                    if (!$highest_price || $price->unit_amount > $highest_price->unit_amount) {
                        $highest_price = $price;
                    }
                }
                ?>
                <?php if ($highest_price): ?>
                    <fieldset class="mb-3 border p-3">
                        <legend class="small"><?= htmlspecialchars($product->name) ?></legend>
                        <div class="form-check">
                            <input type="checkbox" id="price_<?= htmlspecialchars($highest_price->id) ?>" 
                                   name="prices[]" value="<?= htmlspecialchars($highest_price->id) ?>" class="form-check-input">
                            <label for="price_<?= htmlspecialchars($highest_price->id) ?>" class="form-check-label">
                                <?= htmlspecialchars(number_format($highest_price->unit_amount / 100, 2) . " " . strtoupper($highest_price->currency)) ?>
                            </label>
                        </div>
                    </fieldset>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Generate Invoice</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
