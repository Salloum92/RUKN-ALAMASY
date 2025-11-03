<?php
include 'check.php';
$services = $query->select('services', '*');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Services Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../favicon.ico" rel="icon">
  <?php include 'includes/css.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php include 'includes/header.php'; ?>

    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid">

          <!-- Add Service Button -->
          <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addModal">Add Service</button>

          <!-- Services Table -->
          <div class="row">
            <div class="col-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>№</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($services as $service): ?>
                    <tr>
                      <td><?= $service['id']; ?></td>
                      <td><?= htmlspecialchars($service['title']); ?></td>
                      <td><?= htmlspecialchars($service['description']); ?></td>
                      <td>
                        <!-- Edit Button -->
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?= $service['id']; ?>">Edit</button>
                        <!-- Delete Button -->
                        <a href="delete_service.php?id=<?= $service['id']; ?>" onclick="return confirm('Are you sure you want to delete this service?')" class="btn btn-danger">Delete</a>
                      </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $service['id']; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <form action="update_services.php" method="POST">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Service</h5>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="id" value="<?= $service['id']; ?>">
                              <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($service['title']); ?>" required>
                              </div>
                              <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" required><?= htmlspecialchars($service['description']); ?></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Add Modal -->
          <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
              <form action="add_service.php" method="POST">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Add New Service</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label>Title</label>
                      <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Description</label>
                      <textarea name="description" class="form-control" required></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
  </div>

  <?php include 'includes/js.php'; ?>
</body>
</html>
