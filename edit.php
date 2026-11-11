<?php
require_once 'connect.php';

$old_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = mysqli_query($conn, "SELECT * FROM GiangVien WHERE IDGV = $old_id");
$gv = mysqli_fetch_assoc($result);

if (!$gv) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_id   = intval($_POST['idgv']);
    $fullname = trim($_POST['fullname']);
    $sumclass = intval($_POST['sumclass']);
    $image    = $gv['Image'];

    // 1. Kiểm tra nếu người dùng đổi IDGV mới có bị trùng với giảng viên khác hay không
    if ($new_id != $old_id) {
        $check = mysqli_query($conn, "SELECT IDGV FROM GiangVien WHERE IDGV = $new_id");
        if (mysqli_num_rows($check) > 0) {
            $error = "Mã IDGV #$new_id đã tồn tại! Vui lòng nhập mã IDGV khác.";
        }
    }

    // 2. Xử lý tải ảnh mới nếu có
    if (empty($error) && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image = time() . '_' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);

            if ($gv['Image'] != 'default.png' && file_exists('uploads/' . $gv['Image'])) {
                unlink('uploads/' . $gv['Image']);
            }
        } else {
            $error = "Chỉ chấp nhận các file ảnh: JPG, JPEG, PNG, GIF, WEBP!";
        }
    }

    // 3. Cập nhật dữ liệu bao gồm cả mã IDGV
    if (empty($error)) {
        $sql = "UPDATE GiangVien 
                SET IDGV = $new_id, Fullname = '$fullname', Image = '$image', SumClass = $sumclass 
                WHERE IDGV = $old_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Lỗi cập nhật: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Giảng Viên</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="form-card">
    <h2 class="form-title"><i class="fa-solid fa-user-pen"></i> Chỉnh Sửa Giảng Viên</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="idgv">Mã IDGV:</label>
            <input type="number" id="idgv" name="idgv" class="form-control" value="<?= $gv['IDGV'] ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="fullname">Họ và tên Giảng viên:</label>
            <input type="text" id="fullname" name="fullname" class="form-control" value="<?= htmlspecialchars($gv['Fullname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="sumclass">Tổng số lớp giảng dạy:</label>
            <input type="number" id="sumclass" name="sumclass" class="form-control" value="<?= $gv['SumClass'] ?>" min="0" required>
        </div>

        <div class="form-group">
            <label>Ảnh đại diện hiện tại:</label><br>
            <img src="uploads/<?= htmlspecialchars($gv['Image']) ?>" class="avatar-img" style="margin-top: 8px; margin-bottom: 8px;" alt="Avatar" onerror="this.src='https://via.placeholder.com/50'">
        </div>

        <div class="form-group">
            <label for="image">Thay đổi ảnh (để trống nếu giữ nguyên):</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Cập nhật</button>
            <a href="index.php" class="btn-cancel">Hủy bỏ</a>
        </div>
    </form>
</div>

</body>
</html>