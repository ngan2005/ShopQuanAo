<?php
require_once 'database.php';
class san_Pham {
    private $conn;
    private $table_name = "san_pham";
    public $id_SP;
    public $ten_San_Pham;
    public $gia_Ban;
    public $gia_Goc;
    public $hinh_Anh;
    public $id_DM;
    public $ma_Giam_Gia;
    public $ngay_Cap_Nhat;
    public $ngay_Tao;
    public $so_Luong_Ton;
    public $thuongHieu;
    public $trang_Thai;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Lấy tất cả sản phẩm
    public function getAllSanPham() {
        $query = "SELECT * FROM san_Pham";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm mới
    public function addSanPham($ten_san_pham, $gia, $mo_ta) {
        $query = "INSERT INTO san_pham (ten_san_pham, gia, mo_ta) VALUES (:ten_san_pham, :gia, :mo_ta)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_san_pham', $ten_san_pham);
        $stmt->bindParam(':gia', $gia);
        $stmt->bindParam(':mo_ta', $mo_ta);
        return $stmt->execute();
    }

    // Cập nhật sản phẩm
    public function updateSanPham($id, $ten_san_pham, $gia, $mo_ta) {
        $query = "UPDATE san_pham SET ten_san_pham = :ten_san_pham, gia = :gia, mo_ta = :mo_ta WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ten_san_pham', $ten_san_pham);
        $stmt->bindParam(':gia', $gia);
        $stmt->bindParam(':mo_ta', $mo_ta);
        return $stmt->execute();
    }

    // Xóa sản phẩm
    public function deleteSanPham($id) {
        $query = "DELETE FROM san_pham WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>