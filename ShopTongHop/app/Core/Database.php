<?php
/**
 * ShopXpress - Core Database Layer
 * Design Pattern: Singleton Pattern
 * Driver: PDO (PHP Data Objects) with 100% Prepared Statements
 * Charset: utf8mb4_unicode_ci
 */

class Database {
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    /**
     * Khởi tạo kết nối PDO trong private constructor để ngăn chặn khởi tạo từ bên ngoài
     */
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ném Exception khi có lỗi truy vấn
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp (Associative Array)
            PDO::ATTR_EMULATE_PREPARES   => false,                  // 100% Prepared Statements thực thụ từ máy chủ MySQL
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . (defined('DB_COLLATION') ? DB_COLLATION : 'utf8mb4_unicode_ci')
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Thiết lập cưỡng chế bảng mã UTF-8 tiếng Việt hoàn chỉnh cho mọi phiên làm việc
            $collation = defined('DB_COLLATION') ? DB_COLLATION : 'utf8mb4_unicode_ci';
            $this->connection->exec("SET NAMES " . DB_CHARSET . " COLLATE " . $collation);
            $this->connection->exec("SET CHARACTER SET " . DB_CHARSET);
            $this->connection->exec("SET character_set_results = " . DB_CHARSET);
            $this->connection->exec("SET character_set_client = " . DB_CHARSET);
            $this->connection->exec("SET character_set_connection = " . DB_CHARSET);
        } catch (PDOException $e) {
            error_log("ShopXpress Database Error: " . $e->getMessage());
            die("
                <div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:25px; border:1px solid #ff4757; border-radius:8px; background:#fff5f5; color:#2f3542;'>
                    <h2 style='color:#ff4757; margin-top:0;'>Lỗi Kết Nối Cơ Sở Dữ Liệu</h2>
                    <p>Không thể kết nối đến máy chủ MySQL với cơ chế PDO Singleton.</p>
                    <p><strong>Chi tiết lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <p style='font-size:13px; color:#747d8c;'>Vui lòng kiểm tra dịch vụ MySQL trên phần mềm XAMPP và thông số trong tệp <code>app/Config/config.php</code>.</p>
                </div>
            ");
        }
    }

    /**
     * Ngăn chặn clone đối tượng
     */
    private function __clone() {}

    /**
     * Ngăn chặn deserialize
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Lấy thực thể duy nhất của Database (Singleton Instance)
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lấy đối tượng kết nối PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Shortcut tĩnh lấy trực tiếp PDO
     */
    public static function pdo(): PDO {
        return self::getInstance()->getConnection();
    }
}
