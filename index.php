<?php
require_once 'connect.php';

// 1. XỬ LÝ XÓA GIẢNG VIÊN
if (isset($_GET['delete_id'])) {
    $idgv = intval($_GET['delete_id']);
    
    $res = mysqli_query($conn, "SELECT Image FROM GiangVien WHERE IDGV = $idgv");
    if ($row = mysqli_fetch_assoc($res)) {
        if ($row['Image'] != 'default.png' && file_exists('uploads/' . $row['Image'])) {
            unlink('uploads/' . $row['Image']);
        }
    }
    
    mysqli_query($conn, "DELETE FROM GiangVien WHERE IDGV = $idgv");
    header("Location: index.php");
    exit();
}

// 2. XỬ LÝ TÌM KIẾM VÀ SẮP XẾP IDGV TĂNG DẦN
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $search_clean = mysqli_real_escape_string($conn, $search);
    $search_id = ltrim($search_clean, '#');

    $sql = "SELECT * FROM GiangVien 
            WHERE Fullname LIKE '%$search_clean%' 
               OR IDGV = '$search_id' 
            ORDER BY IDGV ASC";
} else {
    // Sắp xếp mã IDGV tăng dần (1, 2, 4, 5, 6, 8...)
    $sql = "SELECT * FROM GiangVien ORDER BY IDGV ASC";
}

$result = mysqli_query($conn, $sql);
$total_rows = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Giảng Viên - QLHocPhan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="app-container">
    <!-- TIÊU ĐỀ TRANG CĂN GIỮA -->
    <h1 style="text-align: center; margin-bottom: 24px; color: #0f172a; font-size: 26px; font-weight: 700;">
        <i class="fa-solid fa-user-gear" style="color: #1677ff;"></i> QUẢN LÝ GIẢNG VIÊN
    </h1>

    <div class="top-bar">
        <form action="index.php" method="GET" class="search-form">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" placeholder="Tìm theo tên, IDGV (Gõ rồi nhấn Enter)..." value="<?= htmlspecialchars($search) ?>">
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="clear-search" title="Hủy tìm kiếm">Xóa tìm</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="bar-actions">
            <span class="count-badge">Tổng số: <?= $total_rows ?></span>
            <a href="add.php" class="btn-add"><i class="fa-solid fa-plus"></i> Thêm Giảng Viên Mới</a>
        </div>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th style="width: 100px; text-align: center;">MÃ IDGV</th>
                    <th style="width: 140px;">ẢNH ĐẠI DIỆN</th>
                    <th style="text-align: left;">HỌ VÀ TÊN</th>
                    <th style="width: 180px; text-align: center;">TỔNG SỐ LỚP</th>
                    <th style="width: 180px; text-align: center;">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="col-id" style="text-align: center; font-weight: 700; color: #1677ff;">#<?= $row['IDGV'] ?></td>

                        <td>
                            <img src="uploads/<?= htmlspecialchars($row['Image']) ?>" class="avatar-img" alt="Avatar" onerror="this.src='https://via.placeholder.com/50'">
                        </td>

                        <td class="col-name"><?= htmlspecialchars($row['Fullname']) ?></td>

                        <td style="text-align: center;">
                            <span class="status-badge">
                                <span class="dot"></span> <?= $row['SumClass'] ?> Lớp
                            </span>
                        </td>

                        <td>
                            <div class="action-btns">
                                <a href="edit.php?id=<?= $row['IDGV'] ?>" class="btn-text btn-edit-text">
                                    <i class="fa-regular fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="index.php?delete_id=<?= $row['IDGV'] ?>" class="btn-text btn-delete-text" onclick="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?')">
                                    <i class="fa-regular fa-trash-can"></i> Xóa
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-data">Không tìm thấy dữ liệu giảng viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>