<?php

namespace App\Controllers\API;

use Auth;
use DB;
use Laminas\Diactoros\ServerRequest;
use stdClass;
use Hash;

class Authorization
{

    public function Register(ServerRequest $request)
    {
        header("content-type: application/json; charset=UTF-8");

        $DB = DB::connect();
        $data = array();

        $body = objectToArray($request->getParsedBody());

        $name = isset($body->name) ? $body->name : null;
        $username = isset($body->username) ? $body->username : null;
        $phone = isset($body->phone) ? $body->phone : null;
        $password = isset($body->password) ? $body->password : null;
        $confirm_password = isset($body->confirm_password) ? $body->confirm_password : null;

        if (empty($phone)) :

            $data['status'] = "warning";
            $data['message'] = "Số điện thoại không được bỏ trống";

        elseif (Auth::check_phone($phone)) :

            $data['status'] = "error";
            $data['message'] = "Số điện thoại đã tồn tại";

        elseif (empty($name)) :

            $data['status'] = "warning";
            $data['message'] = "Họ và tên không được bỏ trống";

        elseif (empty($username)) :

            $data['status'] = "warning";
            $data['message'] = "Tên đăng nhập không được bỏ trống";

        elseif (Auth::check_username($username)) :

            $data['status'] = "error";
            $data['message'] = "Tên đăng nhập đã tồn tại";

        elseif (empty($password)) :

            $data['status'] = "warning";
            $data['message'] = "Mật khẩu không được bỏ trống";

        elseif (empty($confirm_password)) :

            $data['status'] = "warning";
            $data['message'] = "Mật khẩu xác nhận không được bỏ trống";

        elseif ($confirm_password != $password) :

            $data['status'] = "error";
            $data['message'] = "Mật khẩu xác nhận không chính xác";

        else :

            $save = new stdClass;
            $save->phone = $phone;
            $save->avatar = "https://ui-avatars.com/api/?background=random&name=$name";
            $save->name = $name;
            $save->username = $username;
            $save->password = Hash::make($password);
            $save->type = 'account';
            $save->level = 0;
            $save->date = time();
            $save->status = 1;

            if (Auth::save($save)) :

                $data['status'] = "success";
                $data['href'] = "/";
                $data['message'] = "Tạo tài khoản thành công";

            else :

                $data['status'] = "error";
                $data['message'] = "Tạo tài khoản thất bại";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Login(ServerRequest $request)
    {
        header("content-type: application/json; charset=UTF-8");

        $DB = DB::connect();
        $data = array();

        $body = objectToArray($request->getParsedBody());

        $account = isset($body->account) ? $body->account : null;
        $password = isset($body->password) ? $body->password : null;

        if(!empty($account)):
            if(Auth::check_phone($account)):
                $acc ='phone';
            elseif(Auth::check_username($account)):
                $acc = 'username';
            elseif (Auth::check_email($account)) :
                $acc = 'email';
            else:
                $acc = false;
            endif;
        endif;

        if (empty($account)) :

            $data['status'] = "warning";
            $data['message'] = "Tài khoản không được bỏ trống";

        elseif (!$acc) :

            $data['status'] = "error";
            $data['message'] = "Tài khoản không tồn tại";

        elseif (empty($password)) :

            $data['status'] = "warning";
            $data['message'] = "Mật khẩu không được bỏ trống";

        else :

            $users = $DB->find("users", [$acc => "$account"])->get();

            if (!Hash::check($password, $users->password)) :

                $data['status'] = "error";
                $data['message'] = "Mật khẩu không chính xác";

            elseif (Auth::login($users)) :

                $data['status'] = "success";
                $data['href'] = "/";
                $data['message'] = "Đăng nhập tài khoản thành công";

            else :

                $data['status'] = "error";
                $data['message'] = "Đăng nhập tài khoản thất bại";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Logout()
    {
        header("content-type: application/json; charset=UTF-8");
        $data = array();

        if (!Auth::check()) :

            $data['status'] = "error";
            $data['message'] = "Bạn chưa đăng nhập vui lòng đăng nhập tài khoản để sử dụng dịch vụ này!";

        elseif (Auth::logout()) :

            $data['status'] = "success";
            $data['message'] = "Đăng xuất tài khoản thành công";
            $data['href'] = "/?logout=ok";

        else :

            $data['status'] = "error";
            $data['message'] = "Đăng xuất tài khoản thất bại";

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
