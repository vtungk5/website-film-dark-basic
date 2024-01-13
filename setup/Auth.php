<?php

class Auth
{

    static public $table = "users";
    static public $session_name = "account";
    static public $data_session_name = "phone";

    public static function save($data)
    {
        $session_name = static::$session_name;
        $data_session_name = static::$data_session_name;

        if (static::insert($data)):
            $_SESSION["$session_name"] = $data->$data_session_name;
            return true;
        else:
            return false;
        endif;
    }


    public static function login($data)
    {
        $session_name = static::$session_name;
        $data_session_name = static::$data_session_name;

        $_SESSION["$session_name"] = $data->$data_session_name;
        return true;
    }

    public static function check_username($username)
    {
        $DB = DB::connect();
        $table = static::$table;
        return empty($DB->num_rows("$table", ["username" => $username])->get())? false: true;
    }

    public static function check_email($email)
    {
        $DB = DB::connect();
        $table = static::$table;
        return empty($DB->num_rows("$table", ["email" => $email])->get())? false: true;
    }

    public static function check_uid($uid)
    {
        $DB = DB::connect();
        $table = static::$table;
        return empty($DB->num_rows("$table", ["uid" => $uid])->get())? false: true;
    }
    
    public static function check_phone($phone)
    {
        $DB = DB::connect();
        $table = static::$table;
        return empty($DB->num_rows("$table", ["phone" => $phone])->get())? false: true;
    }

    public static function find()
    {
        $DB = DB::connect();
        $table = static::$table;
        $data_session_name = static::$data_session_name;
        return $DB->find("$table", ["$data_session_name" => static::session()])->get();
    }

    public static function num_rows()
    {
        $DB = DB::connect();
        $table = static::$table;
        $data_session_name = static::$data_session_name;
        return empty($DB->num_rows("$table", ["$data_session_name" => static::session()])->get()) ? false: true;
    }

    public static function logout()
    {
        $session_name = static::$session_name;
        unset($_SESSION["$session_name"]);
        return true;
    }

    public static function insert($data)
    {
        $DB = DB::connect();
        $table = static::$table;
        return $DB->insert("$table", $data)->status();
    }

    public static function check()
    {
        if (static::user()):
            return true;
        else:
            return false;
        endif;
    }

    public static function user()
    {
        if (static::session()):
            
            $ck = static::num_rows();

            if (!$ck):
                return false;
            else:
                return static::find();
            endif;
        else:
            return false;
        endif;
    }

    public static function session()
    {
        $session_name = static::$session_name;
        return isset($_SESSION["$session_name"]) ? $_SESSION["$session_name"] : false;
    }
}
