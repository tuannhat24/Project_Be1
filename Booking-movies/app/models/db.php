<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/config/database.php';

class Database
{
    public static $connection = NULL;
    public function __construct()
    {
        if (self::$connection == NULL) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
    }
    // // Lấy kết nối cơ sở dữ liệu
    // public static function getConnection()
    // {
    //     return self::$connection;
    // }

    public function select($sql)
    {
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// $db = new Database();
// $conn = Database::getConnection();  // Lấy kết nối cơ sở dữ liệu từ phương thức static

// $rooms = [
//     ['room_id' => 1, 'capacity' => 100], // Room 1 với 100 ghế
//     ['room_id' => 2, 'capacity' => 120], // Room 2 với 120 ghế
//     ['room_id' => 3, 'capacity' => 80],  // Room 3 với 80 ghế
//     ['room_id' => 4, 'capacity' => 90]   // Room 4 với 90 ghế
// ];

// // Đoạn mã này sẽ chèn dữ liệu vào bảng seats
// foreach ($rooms as $room) {
//     $room_id = $room['room_id'];
//     $capacity = $room['capacity'];
//     $rows = ceil($capacity / 12); // Tính số hàng (12 ghế/hàng)

//     for ($i = 0; $i < $rows; $i++) {
//         $row = chr(65 + $i); // Chuyển đổi số thành chữ cái: 65 là ASCII của 'A'

//         for ($seat_number = 1; $seat_number <= 12; $seat_number++) {
//             // Nếu seat_number lớn hơn sức chứa thì dừng lại
//             if (($i * 12) + $seat_number > $capacity) break;

//             // Insert vào bảng seats
//             $sql = "INSERT INTO seats (room_id, seat_row, seat_number, seat_type, status, created_at, updated_at) 
//                     VALUES ($room_id, '$row', $seat_number, 'normal', 'available', '2024-12-19 13:32:00', '2024-12-19 13:32:00')";
//             if ($conn->query($sql) === TRUE) {
//                 echo "Record created successfully for Room $room_id, Row $row, Seat $seat_number.<br>";
//             } else {
//                 echo "Error: " . $sql . "<br>" . $conn->error;
//             }
//         }
//     }
// }
