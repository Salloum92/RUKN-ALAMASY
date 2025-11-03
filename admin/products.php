<?php
include 'check.php';

// Fetch products
$products = $query->select('products', '*');
$categories = $query->eQuery('SELECT * FROM category');

// Product deletion process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        
        try {
            // Delete images from database and filesystem
            $imagesUrl = $query->select('product_images', '*', "WHERE product_id = $delete_id");
            foreach ($imagesUrl as $image) {
                $imageUrl = "../assets/img/product/" . $image['image_url'];
                if (file_exists($imageUrl) && is_file($imageUrl)) {
                    if (!unlink($imageUrl)) {
                        error_log("Failed to delete image: " . $imageUrl);
                    }
                }
            }
            
            // Delete image records from database
            $query->eQuery('DELETE FROM product_images WHERE product_id = ?', [$delete_id]);
            
            // Delete the product
            $deleteResult = $query->eQuery('DELETE FROM products WHERE id = ?', [$delete_id]);
            
            if ($deleteResult) {
                echo 'success';
            } else {
                echo 'error: delete failed';
            }
            
        } catch (Exception $e) {
            echo 'error: ' . $e->getMessage();
        }
        exit;
    }
    
    // Product update process
    if ($_POST['action'] === 'update' && isset($_POST['edit_id'])) {
        $edit_id = intval($_POST['edit_id']);
        $product_name = trim($_POST['product_name']);
        $price = floatval($_POST['price']);
        $description = trim($_POST['description']);
        $category_id = intval($_POST['category_id']);

        try {
            // Update product data
            $query->eQuery('UPDATE products SET product_name = ?, price = ?, description = ?, category_id = ? WHERE id = ?', 
                          [$product_name, $price, $description, $category_id, $edit_id]);

            // Handle image updates if new images are uploaded
            if (!empty($_FILES['image']['name'][0])) {
                // Delete old images
                $oldImages = $query->select('product_images', '*', "WHERE product_id = $edit_id");
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = "../assets/img/product/" . $oldImage['image_url'];
                    if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Delete old image records
                $query->eQuery('DELETE FROM product_images WHERE product_id = ?', [$edit_id]);

                // Upload new images
                $uploadedImages = [];
                $totalFiles = count($_FILES['image']['name']);
                
                if ($totalFiles <= 10) {
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['image']['error'][$i] == 0) {
                            $image_name = basename($_FILES['image']['name'][$i]);
                            $encrypted_name = md5(time() . $image_name . $i) . "." . pathinfo($image_name, PATHINFO_EXTENSION);
                            $target_dir = "../assets/img/product/";
                            $target_file = $target_dir . $encrypted_name;

                            if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $target_file)) {
                                $uploadedImages[] = $encrypted_name;
                            }
                        }
                    }

                    // Insert new image records
                    foreach ($uploadedImages as $uploadedImage) {
                        $query->eQuery('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)', [$edit_id, $uploadedImage]);
                    }
                }
            }

            header("Location: " . $_SERVER['PHP_SELF'] . "?updated=true");
            exit;
            
        } catch (Exception $e) {
            echo "Error updating product: " . $e->getMessage();
        }
    }
}

// Product addition process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);

    try {
        // Image upload process
        $uploadedImages = [];
        $totalFiles = count($_FILES['image']['name']);
        
        if ($totalFiles <= 10 && $totalFiles > 0) {
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['image']['error'][$i] == 0) {
                    $image_name = basename($_FILES['image']['name'][$i]);
                    $encrypted_name = md5(time() . $image_name . $i) . "." . pathinfo($image_name, PATHINFO_EXTENSION);
                    $target_dir = "../assets/img/product/";
                    $target_file = $target_dir . $encrypted_name;

                    if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $target_file)) {
                        $uploadedImages[] = $encrypted_name;
                    }
                }
            }

            if (!empty($uploadedImages)) {
                // Insert product
                $query->eQuery('INSERT INTO products (product_name, description, price, category_id) VALUES (?, ?, ?, ?)', 
                              [$product_name, $description, $price, $category_id]);

                $product_id = $query->lastInsertId();

                // Insert images
                foreach ($uploadedImages as $uploadedImage) {
                    $query->eQuery('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)', [$product_id, $uploadedImage]);
                }

                header("Location: " . $_SERVER['PHP_SELF'] . "?added=true");
                exit;
            } else {
                echo "Error: No images were uploaded successfully.";
            }
        } else {
            echo "Please upload at least 1 image and no more than 10 images.";
        }
    } catch (Exception $e) {
        echo "Error adding product: " . $e->getMessage();
    }
}

// Get product data for editing
$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_product = $query->select('products', '*', "WHERE id = $edit_id")[0] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Products Management - Admin Panel</title>
    <link href="../favicon.ico" rel="icon">
    <?php include 'includes/css.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
            border: none;
            border-radius: 8px;
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            border: none;
            border-radius: 8px;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--dark-color), #34495e);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transform: scale(1.01);
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f8f9fa;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .product-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #f8f9fa;
        }
        
        .badge-category {
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #ecf0f1;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border-left-color: var(--success-color);
            color: #27ae60;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .table-responsive {
                border-radius: 10px;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'includes/header.php' ?>
        
        <div class="content-wrapper" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0" style="color: var(--dark-color); font-weight: 700;">Products Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-color);">Dashboard</a></li>
                                <li class="breadcrumb-item active" style="color: var(--dark-color);">Products</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo count($products); ?></div>
                                <div class="stats-label">Total Products</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="stats-number"><?php echo count($categories); ?></div>
                                <div class="stats-label">Categories</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="stats-number"><?php echo count($products) > 0 ? array_sum(array_column($products, 'price')) / count($products) : 0; ?></div>
                                <div class="stats-label">Avg Price</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="stats-number"><?php echo count($products); ?></div>
                                <div class="stats-label">Active</div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Messages -->
                    <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check-circle"></i> <strong>Success!</strong> Product added successfully!
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true'): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check-circle"></i> <strong>Success!</strong> Product updated successfully!
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" style="color: white;">
                                        <i class="fas fa-boxes mr-2"></i>Products List
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-light" data-toggle="modal"
                                            data-target="#productModal" style="border-radius: 20px;">
                                            <i class="fas fa-plus-circle mr-2"></i>Add New Product
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($products)): ?>
                                        <div class="empty-state">
                                            <i class="fas fa-box-open"></i>
                                            <h3>No Products Found</h3>
                                            <p>Get started by adding your first product to the catalog.</p>
                                            <button type="button" class="btn btn-primary mt-3" data-toggle="modal"
                                                data-target="#productModal">
                                                <i class="fas fa-plus mr-2"></i>Add First Product
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="60">#</th>
                                                        <th>Product</th>
                                                        <th width="100">Image</th>
                                                        <th width="120">Price</th>
                                                        <th>Description</th>
                                                        <th width="140">Category</th>
                                                        <th width="180">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="productTable">
                                                    <?php foreach ($products as $i => $product): ?>
                                                        <tr id="product<?php echo $product['id']; ?>" style="transition: all 0.3s ease;">
                                                            <?php
                                                            $productid = $product['id'];
                                                            $product_images = $query->select('product_images', '*', "WHERE product_id = $productid");
                                                            $first_image = $product_images[0]['image_url'] ?? 'default-product.jpg';
                                                            $product_image = "../assets/img/product/" . $first_image;
                                                            ?>
                                                            <td>
                                                                <span class="badge badge-primary" style="background: var(--primary-color); padding: 8px 12px; border-radius: 10px;">
                                                                    <?php echo $i + 1; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <strong style="color: var(--dark-color);"><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($product_images)): ?>
                                                                    <img src="<?= $product_image ?>"
                                                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                                        class="product-image"
                                                                        onerror="this.src='../assets/img/default-product.jpg'">
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary" style="padding: 8px;">No Image</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span style="color: var(--success-color); font-weight: 700; font-size: 1.1rem;">
                                                                    $<?php echo number_format($product['price'], 2); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span title="<?php echo htmlspecialchars($product['description']); ?>">
                                                                    <?php echo htmlspecialchars(substr($product['description'], 0, 60)) . (strlen($product['description']) > 60 ? '...' : ''); ?>
                                                                </span>
                                                            </td>
                                                            <?php $category_id = $product['category_id'] ?>
                                                            <td>
                                                                <?php 
                                                                $category = $query->select('category', '*', "WHERE id = $category_id")[0] ?? null;
                                                                echo $category ? '<span class="badge-category">' . htmlspecialchars($category['category_name']) . '</span>' : '<span class="badge badge-warning">Uncategorized</span>';
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <button type="button" class="btn btn-warning action-btn" 
                                                                            onclick="editProduct(<?php echo $productid; ?>)"
                                                                            title="Edit Product">
                                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger action-btn"
                                                                        onclick="deleteProduct(<?php echo $productid; ?>)"
                                                                        title="Delete Product">
                                                                        <i class="fas fa-trash mr-1"></i> Delete
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Product Modal (Add/Edit) -->
        <div class="modal fade" id="productModal" tabindex="-1" role="dialog"
            aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">
                            <i class="fas <?php echo isset($edit_product) ? 'fa-edit' : 'fa-plus-circle'; ?> mr-2"></i>
                            <?php echo isset($edit_product) ? 'Edit Product' : 'Add New Product'; ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="<?php echo isset($edit_product) ? 'update' : 'add'; ?>">
                            <?php if (isset($edit_product)): ?>
                                <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name" class="font-weight-bold">Product Name *</label>
                                        <input type="text" class="form-control" name="product_name"
                                            id="productName" maxlength="255" 
                                            value="<?php echo isset($edit_product) ? htmlspecialchars($edit_product['product_name']) : ''; ?>" 
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="font-weight-bold">Price ($) *</label>
                                        <input type="number" class="form-control" name="price"
                                            id="productPrice" step="0.01" min="0"
                                            value="<?php echo isset($edit_product) ? htmlspecialchars($edit_product['price']) : ''; ?>" 
                                            required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Description *</label>
                                <textarea class="form-control" name="description"
                                    id="productDescription" rows="4" required><?php echo isset($edit_product) ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_id" class="font-weight-bold">Category *</label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo (isset($edit_product) && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="image" class="font-weight-bold">Product Images *</label>
                                <input type="file" class="form-control" name="image[]"
                                    id="productImage" accept="image/*" multiple 
                                    <?php echo !isset($edit_product) ? 'required' : ''; ?>>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Maximum 10 images. Supported formats: JPG, PNG, GIF. 
                                    <?php if (isset($edit_product)): ?>
                                        Leave empty to keep current images.
                                    <?php endif; ?>
                                </small>
                            </div>
                            
                            <?php if (isset($edit_product)): ?>
                                <div class="form-group">
                                    <label class="font-weight-bold">Current Images</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php 
                                        $current_images = $query->select('product_images', '*', "WHERE product_id = " . $edit_product['id']);
                                        if (!empty($current_images)): 
                                        ?>
                                            <?php foreach ($current_images as $img): ?>
                                                <img src="../assets/img/product/<?php echo $img['image_url']; ?>" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;"
                                                     onerror="this.src='../assets/img/default-product.jpg'">
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-image mr-1"></i>No images available</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                                <i class="fas fa-save mr-1"></i> 
                                <?php echo isset($edit_product) ? 'Update Product' : 'Add Product'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/js.php'; ?>

    <script>
        function deleteProduct(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to recover this product!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3498db',
                confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'POST',
                            url: window.location.href,
                            data: {
                                action: 'delete',
                                delete_id: id
                            },
                            success: function (response) {
                                resolve(response);
                            },
                            error: function (xhr, status, error) {
                                reject(error);
                            }
                        });
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value === 'success') {
                        // Remove the row with animation
                        $('#product' + id).fadeOut(400, function() {
                            $(this).remove();
                            
                            // Update serial numbers
                            updateSerialNumbers();
                            
                            // Check if table is empty
                            if ($('#productTable tr').length === 0) {
                                location.reload();
                            }
                            
                            Swal.fire({
                                title: "Deleted!",
                                text: "Product has been deleted successfully.",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to delete product: " + result.value,
                            icon: "error"
                        });
                    }
                }
            });
        }

        function updateSerialNumbers() {
            $('#productTable tr').each(function(index) {
                $(this).find('td:first .badge').text(index + 1);
            });
        }

        function editProduct(id) {
            window.location.href = '?edit_id=' + id;
        }

        // Auto-open edit modal if edit_id is present
        $(document).ready(function() {
            <?php if (isset($_GET['edit_id'])): ?>
                $('#productModal').modal('show');
            <?php endif; ?>

            // Reset form when modal is closed
            $('#productModal').on('hidden.bs.modal', function () {
                <?php if (isset($_GET['edit_id'])): ?>
                    window.location.href = window.location.pathname;
                <?php endif; ?>
            });

            // Form validation
            $('#productForm').on('submit', function(e) {
                const productName = $('#productName').val().trim();
                const price = $('#productPrice').val();
                const description = $('#productDescription').val().trim();
                const category = $('#category_id').val();
                const isEdit = <?php echo isset($edit_product) ? 'true' : 'false'; ?>;
                const hasFiles = $('#productImage')[0].files.length > 0;

                if (!productName || !price || !description || !category) {
                    e.preventDefault();
                    Swal.fire("Error!", "Please fill all required fields!", "error");
                    return false;
                }

                if (!isEdit && !hasFiles) {
                    e.preventDefault();
                    Swal.fire("Error!", "Please upload at least one image for new products!", "error");
                    return false;
                }

                if (hasFiles && $('#productImage')[0].files.length > 10) {
                    e.preventDefault();
                    Swal.fire("Error!", "You can upload maximum 10 images!", "error");
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                return true;
            });

            // File input change event
            $('#productImage').on('change', function() {
                const files = this.files;
                if (files.length > 10) {
                    Swal.fire("Error!", "You can select maximum 10 files!", "error");
                    this.value = '';
                }
            });
        });
    </script>

</body>
</html>