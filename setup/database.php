<?php
class DB
{

    public static $hostserver = null;
    public static $username = null;
    public static $password = null;
    public static $database = null;
    public static $port = 3306;
    public static $ketnoi = true;
    public static $status = false;
    public static $message = null;
    public static $runsql = null;
    public static $getID = null;

    public static $type = [
        'INSERT' => 'INSERT INTO',
        'UPDATE' => 'UPDATE',
        'DELETE' => 'DELETE FROM',
        'SELECT' => 'SELECT * FROM',
    ];

    public static $data = false;

    public function message()
    {
        return static::$message;
    }

    public function status()
    {
        return static::$status;
    }

    public static function hostserver($hostserver = 'localhost')
    {
        static::$hostserver = $hostserver;
        return new (static::class);
    }

    public static function username($username = 'root')
    {
        static::$username = $username;
        return new (static::class);
    }

    public static function password($password = null)
    {
        static::$password = $password;
        return new (static::class);
    }

    public static function database($database = '1234')
    {
        static::$database = $database;
        return new (static::class);
    }

    public static function port($port = 3306)
    {
        static::$port = $port;
        return new (static::class);
    }

    public function get()
    {
        return isset(static::$data[0]) ? static::$data[0] : static::$data;
    }
    
    public function all()
    {
        return static::$data;
    }

    public static function connect()
    {
        global $_ENV;
        try {
            static::$status = true;
            static::$message = 'Kết nối cơ sở dữ liệu thành công';

            $hostserver = (empty($_ENV['HOSTSERVER'])) ? "localhost" : $_ENV['HOSTSERVER'];
            $username = (empty($_ENV['USERNAME'])) ? "root" : $_ENV['USERNAME'];
            $password = (empty($_ENV['PASSWORD'])) ? "" : $_ENV['PASSWORD'];
            $database = (empty($_ENV['DATABASE'])) ? "test" : $_ENV['DATABASE'];

            static::$ketnoi = mysqli_connect($hostserver, $username, $password, $database, static::$port);
            mysqli_query(static::$ketnoi, "set names 'utf8'");
        } catch (\Throwable$error) {
            static::$status = false;
            static::$message = $error;
        }
        return new (static::class);
    }

    public function setsql($sql)
    {
        static::$status = false;
        try {

            $data = mysqli_query(static::$ketnoi, $sql);
            static::$runsql = $data;

            if ($data) {
                static::$status = true;
                static::$message = 'thành công';
            } else {
                static::$status = false;
                static::$message = 'thất bại';
            }

        } catch (\Throwable$error) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

    public function run()
    {
       return static::$runsql;
    }

    public function insert($table, $data = [])
    {
        static::$status = false;
        try {
            $sql = static::$type['INSERT'] . " `$table` SET ";
            $i = 1;

            foreach ($data as $key => $value) {
                if ($i++ == 1) {
                    $sql .= " `$key` = '$value' ";
                } else {
                    $sql .= ", `$key` = '$value' ";
                }
            }

            $insert = mysqli_query(static::$ketnoi, $sql);
            static::$getID = mysqli_insert_id(static::$ketnoi);

            if ($insert) {
                static::$status = true;
                static::$message = 'Insert dữ liệu lên bảng `table` thành công';
            } else {
                static::$status = false;
                static::$message = 'Insert dữ liệu lên bảng `table` thất bại';
            }

        } catch (\Throwable$error) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

    public function update($table, $data = [], $where = null)
    {
        static::$status = false;
        try {

            $sql = static::$type['UPDATE'] . " `$table` SET ";
            $i = 1;
            foreach ($data as $key => $value) {
                if ($i++ == 1) {
                    $sql .= " `$key` = '$value' ";
                } else {
                    $sql .= ", `$key` = '$value' ";
                }
            }

            if ($where != null) {
                $sql .= " WHERE ";
                $x = 1;
                foreach ($where as $where_key => $where_value) {
                    if ($x++ == 1) {
                        $sql .= " `$where_key` = '$where_value' ";
                    } else {
                        $sql .= " AND `$where_key` = '$where_value' ";
                    }
                }
            }

            $update = mysqli_query(static::$ketnoi, $sql);

            if ($update) {
                static::$status = true;
                static::$message = 'Cập nhập dữ liệu lên bảng `$table` thành công';
            } else {
                static::$status = false;
                static::$message = 'Cập nhập dữ liệu lên bảng `$table` thất bại';
            }

        } catch (\Throwable$error) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

    public function find($table, $where = null, $order_by = null, $limit = null)
    {
 
            $sql = static::$type['SELECT'] . " `$table` ";

            if ($where != null) {
                $sql .= "WHERE ";
                $i = 0;
                foreach ($where as $where_key => $where_value) {
                    if ($i++ === 0) {
                        $sql .= "`$where_key`='$where_value'";
                    } else {
                        $sql .= " AND `$where_key`='$where_value'";
                    }
                }
            }

            if ($order_by != null) {
                $sql .= " ORDER BY ";
                $x = 0;
                foreach ($order_by as $order_by_id) {
                    if ($x++ === 0) {
                        $sql .= "$order_by_id";
                    } else {
                        $sql .= ",$order_by_id";
                    }
                }
            }


            if ($limit != null) {

                $sql .= " LIMIT ";
                $y = 0;
                foreach ($limit as $limit_id) {
                    if ($y++ === 0) {
                        $sql .= "$limit_id";
                    } else {
                        $sql .= ",$limit_id";
                    }
                }
            }

            $result = mysqli_query(static::$ketnoi, $sql);
            $return = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $return[] = $row;
            }

            mysqli_free_result($result);
            static::$data = objectToArray($return);

            if (isset($return)) {
                static::$status = true;
                static::$message = "Lấy danh sách bảng `$table` thành công";
            } else {
                static::$status = false;
                static::$message = "Lấy danh sách bảng `$table` thất bại";
            }

        return new (static::class);
    }

    public function delete($table, $where = null)
    {
        try {
            $sql = static::$type['DELETE'] . " `$table` ";
            $i = 0;
            if ($where != null) {
                $sql .= " WHERE ";
                $x = 0;
                foreach ($where as $where_key => $where_value) {
                    if ($x++ === 0) {
                        $sql .= " `$where_key` = '$where_value' ";
                    } else {
                        $sql .= " AND `$where_key` = '$where_value' ";
                    }
                }
            }

            $delete = mysqli_query(static::$ketnoi, $sql);

            if ($delete) {
                static::$status = true;
                static::$message = "Xóa thông tin bảng `$table` thành công";
            } else {
                static::$status = false;
                static::$message = "Không thể xóa thông tin bảng `$table`";
            }

        } catch (Exception $error) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

    public function num_rows($table, $where = null)
    {
        try {
            $sql = static::$type['SELECT'] . " `$table` ";

            if ($where != null) {
                $sql .= "WHERE ";
                $x = 0;
                foreach ($where as $where_key => $where_value) {
                    if ($x++ === 0) {
                        $sql .= " `$where_key` = '$where_value' ";
                    } else {
                        $sql .= " AND `$where_key` = '$where_value' ";
                    }
                }
            }
            $result = mysqli_query(static::$ketnoi, $sql);

            if (!$result) {
                static::$status = false;
                static::$message = "Không thể lấy thông tin bảng `$table`";
            } else {
                $row = mysqli_num_rows($result);
                mysqli_free_result($result);
                static::$data = $row;

                if ($row) {
                    static::$status = true;
                    static::$message = "Không thể lấy số lượng thông tin bảng `$table`";
                } else {
                    static::$status = false;
                    static::$message = "Lấy số lượng thông tin bảng `$table` thành công";
                }
            }
        } catch (Exception $error) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

    public function create_table($data = null)
    {
        try {
            $uptable = mysqli_query(static::$ketnoi, $data);
            if ($uptable) {
                static::$status = true;
                static::$message = "Không thể thêm bảng `$table`";
            } else {
                static::$status = false;
                static::$message = "Thêm bảng `$table` thành công";
            }
        } catch (Exception $e) {
            static::$message = $error;
            static::$status = false;
        }
        return new (static::class);
    }

}