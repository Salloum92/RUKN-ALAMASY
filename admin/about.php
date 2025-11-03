<?php
include 'check.php';

// الحصول على الصورة القديمة
$old_image_path = "";
$about_data = $query->select("about", "*");

if (!empty($about_data)) {
    $old_image_path = "../" . $about_data[0]['image'];
}

// معالجة طلبات الحذف
if (isset($_GET['delete_about'])) {
    $id = intval($_GET['delete_about']);
    
    try {
        // جلب بيانات الصورة لحذفها
        $sql = "SELECT image FROM about WHERE id=?";
        $result = $query->eQuery($sql, [$id]);
        $image_path = "../" . $result[0]['image'];
        
        // حذف الصورة من السيرفر
        if (!empty($image_path) && file_exists($image_path) && is_file($image_path)) {
            unlink($image_path);
        }
        
        // حذف السجل من قاعدة البيانات
        $sql = "DELETE FROM about WHERE id=?";
        $query->eQuery($sql, [$id]);
        
        $_SESSION['success_message'] = "تم حذف البيانات بنجاح";
        header('Location: about.php?deleted=true');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "خطأ في الحذف: " . $e->getMessage();
        header('Location: about.php?error=true');
        exit();
    }
}

if (isset($_GET['delete_ul_item'])) {
    $id = intval($_GET['delete_ul_item']);
    
    try {
        $sql = "DELETE FROM about_ul_items WHERE id=?";
        $query->eQuery($sql, [$id]);
        
        $_SESSION['success_message'] = "تم حذف العنصر بنجاح";
        header('Location: about.php?deleted=true');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "خطأ في حذف العنصر: " . $e->getMessage();
        header('Location: about.php?error=true');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['update_about'])) {
            // ... كود التحديث الحالي (بدون updated_at) ...
            $id = intval($_POST['id']);
            $title = trim($_POST['title']);
            $p1 = trim($_POST['p1']);
            $p2 = trim($_POST['p2']);
            
            if (empty($title) || empty($p1) || empty($p2)) {
                throw new Exception("جميع الحقول مطلوبة");
            }

            $sql = "SELECT image FROM about WHERE id=?";
            $result = $query->eQuery($sql, [$id]);

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];
                
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = mime_content_type($image['tmp_name']);
                
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("نوع الملف غير مسموح به. المسموح: JPG, PNG, GIF, WEBP");
                }

                if ($image['size'] > 2 * 1024 * 1024) {
                    throw new Exception("حجم الملف كبير جداً (الحد الأقصى 2MB)");
                }

                if (!empty($old_image_path) && file_exists($old_image_path) && is_file($old_image_path)) {
                    unlink($old_image_path);
                }

                $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $new_image_name = uniqid() . '-' . time() . '.' . $file_extension;
                $target_path = "../assets/img/" . $new_image_name;

                if (!move_uploaded_file($image['tmp_name'], $target_path)) {
                    throw new Exception("فشل في رفع الملف");
                }

                $sql = "UPDATE about SET title=?, p1=?, p2=?, image=? WHERE id=?";
                $query->eQuery($sql, [$title, $p1, $p2, 'assets/img/' . $new_image_name, $id]);
                
                $_SESSION['success_message'] = "تم تحديث البيانات بنجاح";
            } else {
                $sql = "UPDATE about SET title=?, p1=?, p2=? WHERE id=?";
                $query->eQuery($sql, [$title, $p1, $p2, $id]);
                
                $_SESSION['success_message'] = "تم تحديث البيانات بنجاح";
            }

            header('Location: about.php?updated=true');
            exit();

        } elseif (isset($_POST['update_ul_items'])) {
            $ul_item_id = intval($_POST['ul_item_id']);
            $list_item = trim($_POST['list_item']);

            if (empty($list_item)) {
                throw new Exception("حقل العنصر مطلوب");
            }

            $sql = "UPDATE about_ul_items SET list_item=? WHERE id=?";
            $query->eQuery($sql, [$list_item, $ul_item_id]);
            
            $_SESSION['success_message'] = "تم تحديث العنصر بنجاح";
            
            header('Location: about.php?updated=true');
            exit();
            
        } elseif (isset($_POST['add_ul_item'])) {
            // إضافة عنصر جديد
            $list_item = trim($_POST['new_list_item']);

            if (empty($list_item)) {
                throw new Exception("حقل العنصر مطلوب");
            }

            $sql = "INSERT INTO about_ul_items (list_item) VALUES (?)";
            $query->eQuery($sql, [$list_item]);
            
            $_SESSION['success_message'] = "تم إضافة العنصر بنجاح";
            
            header('Location: about.php?added=true');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: about.php?error=true');
        exit();
    }
}

// جلب البيانات للعرض
$aboutItems = $query->select('about', '*');
$ulItems = $query->select('about_ul_items', '*');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>إدارة صفحة من نحن</title>
    <link href="../favicon.ico" rel="icon">
    <?php include 'includes/css.php'; ?>
    <style>
        .table img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .card-header .btn {
            float: left;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/header.php' ?>
        
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <!-- رسائل التنبيه -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">محتوى صفحة من نحن</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">العنوان</th>
                                                <th width="20%">التعليق</th>
                                                <th width="25%">الفقرة</th>
                                                <th width="15%">الصورة</th>
                                                <th width="20%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($aboutItems)): ?>
                                                <?php foreach ($aboutItems as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars($item['p1'])); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars($item['p2'])); ?></td>
                                                        <td>
                                                            <?php if (!empty($item['image'])): ?>
                                                                <img src="../<?php echo htmlspecialchars($item['image']); ?>" 
                                                                     alt="صورة عننا" 
                                                                     class="img-thumbnail">
                                                            <?php else: ?>
                                                                <span class="text-muted">لا توجد صورة</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class='btn btn-warning edit-about-btn' 
                                                                        data-toggle='modal' 
                                                                        data-target='#editAboutModal'
                                                                        data-id='<?php echo $item['id']; ?>'
                                                                        data-title='<?php echo htmlspecialchars($item['title']); ?>'
                                                                        data-p1='<?php echo htmlspecialchars($item['p1']); ?>'
                                                                        data-p2='<?php echo htmlspecialchars($item['p2']); ?>'>
                                                                    <i class='fas fa-edit'></i>
                                                                </button>
                                                                <a href="about.php?delete_about=<?php echo $item['id']; ?>" 
                                                                   class="btn btn-danger"
                                                                   onclick="return confirm('هل أنت متأكد من حذف هذا المحتوى؟')">
                                                                    <i class='fas fa-trash'></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">لا توجد بيانات</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">قائمة النقاط</h3>
                                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addUlItemModal">
                                        <i class="fas fa-plus"></i> إضافة نقطة جديدة
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-info">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="70%">العنصر</th>
                                                <th width="20%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($ulItems)): ?>
                                                <?php foreach ($ulItems as $ulItem): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($ulItem['id']); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars($ulItem['list_item'])); ?></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class='btn btn-warning edit-ul-item-btn'
                                                                        data-toggle='modal' 
                                                                        data-target='#editUlItemModal'
                                                                        data-id='<?php echo $ulItem['id']; ?>'
                                                                        data-list-item='<?php echo htmlspecialchars($ulItem['list_item']); ?>'>
                                                                    <i class='fas fa-edit'></i>
                                                                </button>
                                                                <a href="about.php?delete_ul_item=<?php echo $ulItem['id']; ?>" 
                                                                   class="btn btn-danger"
                                                                   onclick="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                                                                    <i class='fas fa-trash'></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">لا توجد عناصر</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal تعديل محتوى من نحن -->
        <div class="modal fade" id="editAboutModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل محتوى من نحن</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editAboutForm" method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="editAboutId">
                            <div class="form-group">
                                <label for="editAboutTitle" class="required-field">العنوان:</label>
                                <input type="text" name="title" id="editAboutTitle" class="form-control" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="editAboutP1" class="required-field">التعليق:</label>
                                <textarea name="p1" id="editAboutP1" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editAboutP2" class="required-field">الفقرة:</label>
                                <textarea name="p2" id="editAboutP2" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editAboutImage">الصورة:</label>
                                <input type="file" name="image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">الأنواع المسموحة: JPG, PNG, GIF, WEBP - الحد الأقصى: 2MB</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" name="update_about" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal تعديل عنصر القائمة -->
        <div class="modal fade" id="editUlItemModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل عنصر القائمة</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editUlItemForm" method="POST" action="">
                            <input type="hidden" name="ul_item_id" id="editUlItemId">
                            <div class="form-group">
                                <label for="editUlItemList" class="required-field">العنصر:</label>
                                <textarea name="list_item" id="editUlItemList" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" name="update_ul_items" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal إضافة عنصر جديد -->
        <div class="modal fade" id="addUlItemModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة نقطة جديدة</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addUlItemForm" method="POST" action="">
                            <div class="form-group">
                                <label for="newUlItemList" class="required-field">النقطة الجديدة:</label>
                                <textarea name="new_list_item" id="newUlItemList" class="form-control" rows="4" required placeholder="أدخل النقطة الجديدة..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit" name="add_ul_item" class="btn btn-success">
                                    <i class="fas fa-plus"></i> إضافة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/js.php'; ?>
    <script>
        $(document).ready(function() {
            // معالجة فتح modal تعديل محتوى من نحن
            $('.edit-about-btn').on('click', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var p1 = $(this).data('p1');
                var p2 = $(this).data('p2');
                
                $('#editAboutId').val(id);
                $('#editAboutTitle').val(title);
                $('#editAboutP1').val(p1);
                $('#editAboutP2').val(p2);
            });

            // معالجة فتح modal تعديل عنصر القائمة
            $('.edit-ul-item-btn').on('click', function() {
                var id = $(this).data('id');
                var listItem = $(this).data('list-item');
                
                $('#editUlItemId').val(id);
                $('#editUlItemList').val(listItem);
            });

            // تنظيف modal الإضافة عند فتحه
            $('#addUlItemModal').on('show.bs.modal', function () {
                $('#newUlItemList').val('');
            });

            // تأكيد الحذف
            $('.btn-danger').on('click', function(e) {
                if (!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.')) {
                    e.preventDefault();
                }
            });

            // إظهار تأكيد قبل إغلاق النموذج إذا كان هناك تغييرات
            $('form').on('change', function() {
                window.onbeforeunload = function() {
                    return 'هل أنت متأكد أنك تريد المغادرة؟ هناك تغييرات غير محفوظة.';
                };
            });

            $('form').on('submit', function() {
                window.onbeforeunload = null;
            });
        });
    </script>
</body>
</html>