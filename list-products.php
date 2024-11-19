<?php

require "init.php";

try {
    $products = $stripe->products->all();

    // Build the product data array
    $product_data = [];
    foreach ($products->data as $product) {
        $price = null;
        if (!empty($product->default_price)) {
            $price = $stripe->prices->retrieve($product->default_price);
        }

        $product_data[] = [
            'name' => $product->name,
            'image' => !empty($product->images) ? $product->images[0] : 'https://via.placeholder.com/300x300',
            'price' => $price ? strtoupper($price->currency) . ' ' . number_format($price->unit_amount / 100, 2) : 'Not available',
        ];
    }
} catch (Exception $e) {
    die('Error fetching products: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 100%; /* Ensures the image scales with the card */
            height: 200px; /* Fixed height for all images */
            object-fit: cover; /* Ensures the image fits without distortion */
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4 fw-bold">Product List</h1>
        <div class="row g-4">
            <?php foreach ($product_data as $product): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= $product['image'] ?>" class="product-image card-img-top" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h5>
                            <p class="card-text"><?= $product['price'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
