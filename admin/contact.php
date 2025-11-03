<?php 
include 'check.php'; 
?>

<?php

// Fetch contact information
$contact = $query->select('contact', "*")[0] ?? [];
$contact_box = $query->select('contact_box', "*");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new social media
    if (isset($_POST['add_social']) && isset($_POST['new_social_type']) && isset($_POST['new_social_link'])) {
        $social_type = $_POST['new_social_type'];
        $social_link = $_POST['new_social_link'];
        
        // Check if social media type already exists
        if (!isset($contact[$social_type]) || empty($contact[$social_type])) {
            $sql = "UPDATE contact SET $social_type=? WHERE id=1";
            $query->eQuery($sql, [$social_link]);
            
            $_SESSION['success_message'] = 'Social media added successfully!';
        } else {
            $_SESSION['error_message'] = 'This social media type already exists!';
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Delete social media
    if (isset($_POST['delete_social']) && isset($_POST['social_type'])) {
        $social_type = $_POST['social_type'];
        
        $sql = "UPDATE contact SET $social_type=NULL WHERE id=1";
        $query->eQuery($sql, []);
        
        $_SESSION['success_message'] = 'Social media deleted successfully!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Add new contact box
    if (isset($_POST['add_contact_box']) && isset($_POST['new_box_title']) && isset($_POST['new_box_value']) && isset($_POST['new_box_icon'])) {
        $title = $_POST['new_box_title'];
        $value = $_POST['new_box_value'];
        $icon = $_POST['new_box_icon'];
        
        $sql = "INSERT INTO contact_box (title, value, icon) VALUES (?, ?, ?)";
        $query->eQuery($sql, [$title, $value, $icon]);
        
        $_SESSION['success_message'] = 'Contact box added successfully!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Delete contact box
    if (isset($_POST['delete_contact_box']) && isset($_POST['box_id'])) {
        $box_id = $_POST['box_id'];
        
        $sql = "DELETE FROM contact_box WHERE id=?";
        $query->eQuery($sql, [$box_id]);
        
        $_SESSION['success_message'] = 'Contact box deleted successfully!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Update existing social media
    if (isset($_POST['twitter'])) {
        $twitter = $_POST['twitter'];
        $sql = "UPDATE contact SET twitter=? WHERE id=1";
        $query->eQuery($sql, [$twitter]);
        $_SESSION['success_message'] = 'Twitter link has been updated!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['facebook'])) {
        $facebook = $_POST['facebook'];
        $sql = "UPDATE contact SET facebook=? WHERE id=1";
        $query->eQuery($sql, [$facebook]);
        $_SESSION['success_message'] = 'Facebook link has been updated!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['instagram'])) {
        $instagram = $_POST['instagram'];
        $sql = "UPDATE contact SET instagram=? WHERE id=1";
        $query->eQuery($sql, [$instagram]);
        $_SESSION['success_message'] = 'Instagram link has been updated!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['linkedin'])) {
        $linkedin = $_POST['linkedin'];
        $sql = "UPDATE contact SET linkedin=? WHERE id=1";
        $query->eQuery($sql, [$linkedin]);
        $_SESSION['success_message'] = 'LinkedIn link has been updated!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Update Contact Box information
    foreach ($contact_box as $box) {
        $title_field = 'title-' . $box['id'];
        $value_field = 'value-' . $box['id'];
        $icon_field = 'icon-' . $box['id'];

        if (isset($_POST[$title_field]) && isset($_POST[$value_field]) && isset($_POST[$icon_field])) {
            $title = $_POST[$title_field];
            $value = $_POST[$value_field];
            $icon = $_POST[$icon_field];

            $sql = "UPDATE contact_box SET title=?, value=?, icon=? WHERE id=?";
            $query->eQuery($sql, [$title, $value, $icon, $box['id']]);
        }
    }

    $_SESSION['success_message'] = 'Data updated successfully!';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Contact Management - Rukn Alamasy</title>
    <link href="../favicon.ico" rel="icon">
    <?php include 'includes/css.php'; ?>
    <style>
        :root {
            --accent-color: #e76a04;
            --dark-background: #2c3e50;
        }
        
        .btn-group-sm .btn {
            margin: 0 2px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 2px solid #e9ecef;
        }
        .section-title {
            margin: 0;
            color: var(--dark-background);
            font-weight: 600;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .card-header {
            background-color: var(--dark-background);
            color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        .table th {
            background-color: var(--dark-background);
            color: white;
            border: none;
        }
        .social-icon {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
            color: white;
            font-size: 14px;
        }
        .twitter-bg { background-color: #1DA1F2; }
        .facebook-bg { background-color: #1877F2; }
        .instagram-bg { background-color: #E4405F; }
        .linkedin-bg { background-color: #0A66C2; }
        .youtube-bg { background-color: #FF0000; }
        .whatsapp-bg { background-color: #25D366; }
        .telegram-bg { background-color: #0088CC; }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        .btn-success:hover {
            background-color: #d45a04;
            border-color: #d45a04;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .contact-box-icon {
            color: var(--accent-color);
            margin-right: 8px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/header.php' ?>
        <div class="content-wrapper">

            <section class="content">
                <div class="container-fluid">

                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Social Media Section -->
                    <div class="card">
                        <div class="card-header">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-share-alt contact-box-icon"></i>
                                    Social Media Links
                                </h3>
                                <button type="button" class="btn btn-success" data-toggle="modal" 
                                        data-target="#addSocialModal">
                                    <i class="fas fa-plus"></i> Add Social Media
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">№</th>
                                            <th width="20%">Social Media</th>
                                            <th width="50%">Link</th>
                                            <th width="25%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $socials = ['twitter', 'facebook', 'instagram', 'linkedin', 'youtube', 'whatsapp', 'telegram'];
                                        $counter = 1;
                                        foreach ($socials as $index => $social) {
                                            $socialLink = $contact[$social] ?? '';
                                            if (!empty($socialLink)) {
                                                $socialIcons = [
                                                    'X' => 'bi bi-twitter-x',
                                                    'facebook' => 'bi bi-facebook',
                                                    'instagram' => 'bi bi-instagram', 
                                                    'linkedin' => 'bi bi-linkedin',
                                                    'youtube' => 'bi bi-youtube',
                                                    'whatsapp' => 'bi bi-whatsapp',
                                                    'telegram' => 'bi bi-telegram'
                                                ];
                                                ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td>
                                                        <span class="social-icon <?= $social ?>-bg">
                                                            <i class="<?= $socialIcons[$social] ?? 'bi bi-link' ?>"></i>
                                                        </span>
                                                        <?php echo ucfirst($social); ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo $socialLink; ?>" target="_blank" class="text-break">
                                                            <?php echo $socialLink; ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button class='btn btn-warning btn-sm' data-toggle='modal'
                                                                data-target='#contactModal-<?php echo $social; ?>'>
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class='btn btn-danger btn-sm' 
                                                                onclick="deleteSocialMedia('<?php echo $social; ?>')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Edit Modal for each social media -->
                                                <div class='modal fade' id='contactModal-<?php echo $social; ?>' tabindex='-1'
                                                    aria-labelledby='contactModalLabel-<?php echo $social; ?>' aria-hidden='true'>
                                                    <div class='modal-dialog'>
                                                        <div class='modal-content'>
                                                            <div class='modal-header'>
                                                                <h5 class='modal-title'>
                                                                    <i class="fas fa-edit contact-box-icon"></i>
                                                                    Edit <?php echo ucfirst($social); ?>
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class='modal-body'>
                                                                <form action='' method='POST'>
                                                                    <div class='form-group'>
                                                                        <label><?php echo ucfirst($social); ?> Link</label>
                                                                        <input type='text' name='<?php echo $social; ?>'
                                                                            class='form-control'
                                                                            value='<?php echo htmlspecialchars($socialLink); ?>'
                                                                            maxlength='255' required>
                                                                    </div>
                                                                    <button type='submit' class='btn btn-primary'>
                                                                        <i class="fas fa-save"></i> Save Changes
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        } ?>
                                        <?php if ($counter === 1): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-info-circle"></i> No social media links added yet.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Box Section -->
                    <div class="card">
                        <div class="card-header">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-address-book contact-box-icon"></i>
                                    Contact Information
                                </h3>
                                <button type="button" class="btn btn-success" data-toggle="modal" 
                                        data-target="#addContactBoxModal">
                                    <i class="fas fa-plus"></i> Add Contact Info
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">№</th>
                                            <th width="20%">Title</th>
                                            <th width="20%">Icon</th>
                                            <th width="40%">Value</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($contact_box)): ?>
                                            <?php foreach ($contact_box as $index => $box): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($box['title']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($box['icon'])): ?>
                                                            <i class="<?= htmlspecialchars($box['icon']) ?> contact-box-icon"></i>
                                                            <small class="text-muted"><?= htmlspecialchars($box['icon']) ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">No icon</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($box['value']) ?>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button class='btn btn-warning btn-sm' data-toggle='modal'
                                                                data-target='#contactBoxModal-<?= $box['id'] ?>'>
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class='btn btn-danger btn-sm' 
                                                                onclick="deleteContactBox(<?= $box['id'] ?>)">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Edit Modal for each contact box -->
                                                <div class='modal fade' id='contactBoxModal-<?= $box['id'] ?>' tabindex='-1'
                                                    aria-labelledby='contactBoxModalLabel-<?= $box['id'] ?>' aria-hidden='true'>
                                                    <div class='modal-dialog'>
                                                        <div class='modal-content'>
                                                            <div class='modal-header'>
                                                                <h5 class='modal-title'>
                                                                    <i class="fas fa-edit contact-box-icon"></i>
                                                                    Edit <?= htmlspecialchars($box['title']) ?>
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class='modal-body'>
                                                                <form action='' method='POST'>
                                                                    <div class='form-group'>
                                                                        <label>Title</label>
                                                                        <input type='text' name='title-<?= $box['id'] ?>'
                                                                            class='form-control'
                                                                            value='<?= htmlspecialchars($box['title']) ?>' 
                                                                            maxlength='255' required>
                                                                    </div>
                                                                    <div class='form-group'>
                                                                        <label>Icon Class</label>
                                                                        <input type='text' name='icon-<?= $box['id'] ?>'
                                                                            class='form-control'
                                                                            value='<?= htmlspecialchars($box['icon']) ?>' 
                                                                            maxlength='255'
                                                                            placeholder="bi bi-telephone">
                                                                        <small class="form-text text-muted">
                                                                            Use Bootstrap Icons class names (e.g., bi bi-telephone, bi bi-envelope)
                                                                        </small>
                                                                    </div>
                                                                    <div class='form-group'>
                                                                        <label>Value</label>
                                                                        <input type='text' name='value-<?= $box['id'] ?>'
                                                                            class='form-control'
                                                                            value='<?= htmlspecialchars($box['value']) ?>' 
                                                                            maxlength='255' required>
                                                                    </div>
                                                                    <button type='submit' class='btn btn-primary'>
                                                                        <i class="fas fa-save"></i> Save Changes
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="fas fa-info-circle"></i> No contact information added yet.
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

        <!-- Add Social Media Modal -->
        <div class="modal fade" id="addSocialModal" tabindex="-1" aria-labelledby="addSocialModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus contact-box-icon"></i>
                            Add Social Media
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="add_social" value="1">
                            <div class="form-group">
                                <label for="new_social_type">Social Media Type</label>
                                <select class="form-control" name="new_social_type" id="new_social_type" required>
                                    <option value="">Select Social Media</option>
                                    <option value="twitter">X</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="telegram">Telegram</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="new_social_link">Link</label>
                                <input type="text" class="form-control" name="new_social_link" id="new_social_link" 
                                       placeholder="https://example.com/username" maxlength="255" required>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Social Media
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Contact Box Modal -->
        <div class="modal fade" id="addContactBoxModal" tabindex="-1" aria-labelledby="addContactBoxModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus contact-box-icon"></i>
                            Add Contact Information
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="add_contact_box" value="1">
                            <div class="form-group">
                                <label for="new_box_title">Title</label>
                                <input type="text" class="form-control" name="new_box_title" id="new_box_title" 
                                       placeholder="e.g., Phone, Email, Address, Location" maxlength="255" required>
                                <small class="form-text text-muted">
                                    Use clear titles like "Phone", "Email", "Address", "Location" for proper display in footer.
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="new_box_icon">Icon Class</label>
                                <input type="text" class="form-control" name="new_box_icon" id="new_box_icon" 
                                       placeholder="e.g., bi bi-telephone, bi bi-envelope, bi bi-geo-alt" maxlength="255" required>
                                <small class="form-text text-muted">Use Bootstrap Icons class names</small>
                            </div>
                            <div class="form-group">
                                <label for="new_box_value">Value</label>
                                <input type="text" class="form-control" name="new_box_value" id="new_box_value" 
                                       placeholder="e.g., +123456789, info@example.com, Your Address" maxlength="255" required>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Contact Info
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/js.php'; ?>

    <script>
        function deleteSocialMedia(socialType) {
            if (confirm('Are you sure you want to delete this social media link?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const input1 = document.createElement('input');
                input1.type = 'hidden';
                input1.name = 'delete_social';
                input1.value = '1';
                
                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'social_type';
                input2.value = socialType;
                
                form.appendChild(input1);
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteContactBox(boxId) {
            if (confirm('Are you sure you want to delete this contact information?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const input1 = document.createElement('input');
                input1.type = 'hidden';
                input1.name = 'delete_contact_box';
                input1.value = '1';
                
                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'box_id';
                input2.value = boxId;
                
                form.appendChild(input1);
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-close alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>

</body>

</html>