<?php
require_once 'function/catlegories_list.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Danh mục sản phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Danh mục sản phẩm</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-body">

                <table id="categoriesTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Img</th>
                            <th>Tên danh mục</th>
                            <th>Ghi chú</th>
                            <th>
                                <a href="index.php?page=category-add" class="btn btn-success w-100">
                                    <i class="fas fa-plus"></i> Thêm mới
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $categorie): ?>
                            <tr>
                                <td><?php echo $categorie['category_id']; ?></td>
                                <td>
                                    <img src="<?php echo $categorie['image_url'] ?>" alt="<?php echo htmlspecialchars($categorie['category_name']); ?>" width="50" class="img-circle">
                                </td>
                                <td><?php echo htmlspecialchars($categorie['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($categorie['description']); ?></td>
                                <td>
                                    <a href="index.php?page=category-edit&id=<?php echo $categorie['category_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="pages/category/delete.php?id=<?php echo $categorie['category_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>