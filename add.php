<?php
require_once 'connect.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idgv     = intval($_POST['idgv']);
    $fullname = trim($_POST['fullname']);
    $sumclass = intval($_POST['sumclass']);
    $image    = 'default.png';

    // 1. Kiểm tra IDGV đã tồn tại chưa
    $check_id = mysqli_query($conn, "SELECT IDGV FROM GiangVien WHERE IDGV = $idgv");
    if (mysqli_num_rows($check_id) > 0) {
        $error = "Mã IDGV #$idgv đã tồn tại! Vui lòng nhập mã khác.";
    }

    // 2. Xử lý tải ảnh đại diện
    if (empty($error) && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image = time() . '_' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        } else {
            $error = "Chỉ chấp nhận các file ảnh: JPG, JPEG, PNG, GIF, WEBP!";
        }
    }

    // 3. Chèn dữ liệu có cả IDGV do người dùng nhập
    if (empty($error)) {
        $sql = "INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES ($idgv, '$fullname', '$image', $sumclass)";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Lỗi thêm dữ liệu: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Giảng Viên Mới</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="form-card">
    <h2 class="form-title"><i class="fa-solid fa-user-plus"></i> Thêm Giảng Viên Mới</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Ô nhập IDGV thủ công -->
        <div class="form-group">
            <label for="idgv">Mã IDGV:</label>
            <input type="number" id="idgv" name="idgv" class="form-control" placeholder="Nhập số IDGV (VD: 101, 102)..." min="1" required>
        </div>

        <div class="form-group">
            <label for="fullname">Họ và tên Giảng viên:</label>
            <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Nhập họ và tên..." required>
        </div>

        <div class="form-group">
            <label for="sumclass">Tổng số lớp giảng dạy:</label>
            <input type="number" id="sumclass" name="sumclass" class="form-control" value="0" min="0" required>
        </div>

        <div class="form-group">
            <label for="image">Ảnh đại diện:</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu dữ liệu</button>
            <a href="index.php" class="btn-cancel">Hủy bỏ</a>
        </div>
    </form>
</div>

</body>
</html>