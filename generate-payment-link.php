<?php
require "init.php";
include('navbar.php');

$products = $stripe->products->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_products = $_POST['products'];

    try {
        $line_items = [];
        foreach ($selected_products as $product_id) {
            $price = $stripe->prices->retrieve($product_id);
            $line_items[] = [
                'price' => $price->id,
                'quantity' => 1,
            ];
        }

        $payment_link = $stripe->paymentLinks->create([
            'line_items' => $line_items,
        ]);

        header("Location: " . $payment_link->url);
        exit;
    } catch (Exception $e) {
        echo "Error creating payment link: " . $e->getMessage();
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payment Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1>Generate Payment Link</h1>
    <form method="POST" class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Select Products</label>
            <?php foreach ($products->data as $product): ?>
                <div class="form-check">
                    <input type="checkbox" name="products[]" value="<?= $product->default_price ?>" class="form-check-input" id="product-<?= $product->id ?>">
                    <label class="form-check-label" for="product-<?= $product->id ?>">
                        <?= $product->name ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Generate Payment Link</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>
