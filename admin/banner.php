<?php include 'check.php'; ?>

<?php
$banners = $query->select('banners', '*');

// معالجة إضافة البانر
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {

    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $originalImage = $_FILES['image']['name'];
        $extension = pathinfo($originalImage, PATHINFO_EXTENSION);
        $timestamp = date('YmdHis');
        $newImageName = uniqid('banner_', true) . '_' . $timestamp . '.' . $extension;

        $target = "../assets/img/banners/" . basename($newImageName);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $button_text = $_POST['button_text'];
            $button_link = $_POST['button_link'];

            $data = [
                'image' => $newImageName,
                'title' => $title,
                'description' => $description,
                'button_text' => $button_text,
                'button_link' => $button_link
            ];

            $query->insert('banners', $data);
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        } else {
            echo "Error occurred while uploading the image.";
        }
    } else {
        echo "No image selected.";
    }
}

// معالجة تعديل البانر
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $button_link = $_POST['button_link'];

    $data = [
        'title' => $title,
        'description' => $description,
        'button_text' => $button_text,
        'button_link' => $button_link
    ];

    // إذا تم رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $originalImage = $_FILES['image']['name'];
        $extension = pathinfo($originalImage, PATHINFO_EXTENSION);
        $timestamp = date('YmdHis');
        $newImageName = uniqid('banner_', true) . '_' . $timestamp . '.' . $extension;
        $target = "../assets/img/banners/" . basename($newImageName);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // حذف الصورة القديمة
            $old_banner = $query->select('banners', '*', "WHERE id = {$id}")[0];
            $oldImagePath = "../assets/img/banners/" . $old_banner['image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            
            $data['image'] = $newImageName;
        }
    }

    $query->update('banners', $data, "WHERE id = {$id}");
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// معالجة حذف البانر
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $banner = $query->select('banners', '*', "WHERE id = {$id}");

    if (isset($banner[0])) {
        $banner = $banner[0];
        $imagePath = "../assets/img/banners/" . $banner['image'];

        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                $query->delete('banners', "WHERE id = {$id}");
                header("Location: {$_SERVER['PHP_SELF']}");
                exit;
            } else {
                echo "An error occurred while deleting the image.";
            }
        } else {
            echo "Image not found.";
        }
    } else {
        echo "Banner not found for deletion.";
    }
}

// جلب بيانات البانر للتعديل
$edit_banner = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_banner = $query->select('banners', '*', "WHERE id = {$edit_id}")[0] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Banner Management</title>
    <!-- CSS files -->
    <?php include 'includes/css.php'; ?>
    <link href="../favicon.ico" rel="icon">
    <style>
        .btn-group-sm .btn {
            margin: 0 2px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .table img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/header.php' ?>
        <div class="content-wrapper">

            <section class="content">
                <div class="container-fluid">

                    <!-- Button to add banners -->
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal"
                        data-target="#bannerModal">
                        Add Banner
                    </button>

                    <!-- Banner Modal (Add/Edit) -->
                    <div class="modal fade" id="bannerModal" tabindex="-1" role="dialog"
                        aria-labelledby="bannerModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bannerModalLabel">
                                        <?php echo isset($edit_banner) ? 'Edit Banner' : 'Add Banner'; ?>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <?php if (isset($edit_banner)): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_banner['id']; ?>">
                                            <input type="hidden" name="update" value="1">
                                        <?php else: ?>
                                            <input type="hidden" name="add" value="1">
                                        <?php endif; ?>
                                        
                                        <div class="form-group">
                                            <label for="image">Select Image</label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                accept="image/*" <?php echo !isset($edit_banner) ? 'required' : ''; ?>>
                                            <?php if (isset($edit_banner)): ?>
                                                <small class="form-text text-muted">
                                                    Leave empty to keep current image. Upload new image to replace existing one.
                                                </small>
                                                <div class="mt-2">
                                                    <img src="../assets/img/banners/<?php echo $edit_banner['image']; ?>" 
                                                         alt="Current Image" width="100" class="img-thumbnail">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                placeholder="Title" maxlength="255" 
                                                value="<?php echo isset($edit_banner) ? htmlspecialchars($edit_banner['title']) : ''; ?>" 
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description"
                                                placeholder="Description" required><?php echo isset($edit_banner) ? htmlspecialchars($edit_banner['description']) : ''; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="button_text">Button Text</label>
                                            <input type="text" class="form-control" id="button_text" name="button_text"
                                                placeholder="Button text" maxlength="100"
                                                value="<?php echo isset($edit_banner) ? htmlspecialchars($edit_banner['button_text']) : ''; ?>" 
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="button_link">Button Link</label>
                                            <input type="text" class="form-control" id="button_link" name="button_link"
                                                placeholder="Button link" maxlength="255"
                                                value="<?php echo isset($edit_banner) ? htmlspecialchars($edit_banner['button_link']) : ''; ?>" 
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">
                                            <?php echo isset($edit_banner) ? 'Update' : 'Add'; ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Banner list -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Banners List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">№</th>
                                            <th width="15%">Image</th>
                                            <th width="15%">Title</th>
                                            <th width="25%">Description</th>
                                            <th width="10%">Button Text</th>
                                            <th width="15%">Button Link</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($banners)): ?>
                                            <?php foreach ($banners as $index => $banner): ?>
                                                <tr>
                                                    <td><?php echo $index + 1 ?></td>
                                                    <td>
                                                        <img src="../assets/img/banners/<?php echo $banner['image']; ?>"
                                                            alt="<?php echo htmlspecialchars($banner['title']); ?>" 
                                                            class="img-thumbnail" style="max-width: 100px;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($banner['description']); ?></td>
                                                    <td><?php echo htmlspecialchars($banner['button_text']); ?></td>
                                                    <td>
                                                        <a href="<?php echo htmlspecialchars($banner['button_link']); ?>" 
                                                           target="_blank" class="text-break">
                                                            <?php echo htmlspecialchars($banner['button_link']); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="?edit=<?php echo $banner['id']; ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <button type="button" 
                                                                onclick="deleteBanner(<?php echo $banner['id']; ?>)" 
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    No banners found. Click "Add Banner" to create one.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- JS files -->
    <?php include 'includes/js.php'; ?>

    <script>
        function deleteBanner(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to recover this banner!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?delete=' + id;
                }
            });
        }

        // Auto-open edit modal if edit parameter is present
        $(document).ready(function() {
            <?php if (isset($_GET['edit'])): ?>
                $('#bannerModal').modal('show');
            <?php endif; ?>

            // Reset form when modal is closed
            $('#bannerModal').on('hidden.bs.modal', function () {
                window.location.href = '<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>';
            });
        });
    </script>

</body>

</html>