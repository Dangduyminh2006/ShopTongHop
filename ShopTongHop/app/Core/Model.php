<?php
/**
 * ShopXpress - Base Model
 * Đóng gói tầng thao tác cơ sở dữ liệu PDO an toàn 100% Prepared Statements
 */

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Thực thi câu lệnh SQL với Prepared Statement an toàn
     * @param string $sql Câu lệnh SQL chứa các placeholder ? hoặc :name
     * @param array $params Mảng các tham số truyền vào
     * @return PDOStatement Đối tượng PDOStatement sau khi execute
     */
    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("ShopXpress Model Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }

    /**
     * Lấy tất cả các dòng dữ liệu (Array of Associative Arrays)
     */
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Lấy 1 dòng dữ liệu duy nhất
     */
    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Lấy giá trị của 1 cột đầu tiên trong dòng đầu tiên (Ví dụ COUNT(*))
     */
    public function fetchColumn(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Thực thi lệnh INSERT / UPDATE / DELETE trả về true nếu thành công
     */
    public function execute(string $sql, array $params = []): bool {
        return $this->query($sql, $params)->rowCount() >= 0;
    }

    /**
     * Lấy ID vừa được tạo (Auto-Increment ID)
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * Bắt đầu một Transaction an toàn
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }

    /**
     * Hoàn tất Transaction (Commit)
     */
    public function commit(): bool {
        return $this->db->commit();
    }

    /**
     * Hoàn tác Transaction (Rollback)
     */
    public function rollBack(): bool {
        return $this->db->rollBack();
    }
}
