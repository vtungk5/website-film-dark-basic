<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\RedirectResponse;
use MiladRahimi\PhpRouter\View\View;
use Laminas\Diactoros\ServerRequest;
use stdclass;
use DB;

class Admin
{
    public function index(View $view)
    {
        return $view->make('admin/index');
    }

    public function Editpart(View $view, ServerRequest  $request, $IDFilm, $IDPart)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $ck = $DB->num_rows('film', ['IDFilm' => $IDFilm])->get();
        $ck2 = $DB->num_rows('part', ['IDPart' => $IDPart, 'IDFilm' => $IDFilm])->get();

        if (empty($ck)) :
            return new RedirectResponse('/admin/film/');
        elseif (empty($ck2)) :
            return new RedirectResponse('/admin/film/' . $IDFilm . '/');
        endif;

        $get = $DB->find('part', ['IDPart' => $IDPart, 'IDFilm' => $IDFilm])->get();

        if ($request->getMethod() == 'POST') :

            $name = isset($_POST['name']) ? $_POST['name'] : null;

            if (empty($name)) :
                $data->status = 'error';
                $data->message = 'Tên phần không được bỏ trống';
            else :
                $save = new stdclass();
                $save->name     = $name;

                if ($DB->update('part', $save, ['IDPart' => $IDPart, 'IDFilm' => $IDFilm])->status()) :

                    $data->status = 'success';
                    $data->message = 'Cập nhập phần phim thành công';

                else :

                    $data->status = 'error';
                    $data->message = 'Cập nhập phần phim thất bại';

                endif;
            endif;

        endif;

        return $view->make('admin/part/edit', [
            'data' => $data,
            'get' => $get
        ]);
    }

    public function Editvideo(View $view, ServerRequest  $request, $IDFilm, $IDPart, $IDVideo)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $ck = $DB->num_rows('film', ['IDFilm' => $IDFilm])->get();
        $ck2 = $DB->num_rows('part', ['IDPart' => $IDPart, 'IDFilm' => $IDFilm])->get();
        $ck3 = $DB->num_rows('video', ['IDVideo' => $IDVideo, 'IDPart' => $IDPart, 'IDFilm' => $IDFilm])->get();

        if (empty($ck)) :
            return new RedirectResponse('/admin/film/');
        elseif (empty($ck2)) :
            return new RedirectResponse('/admin/film/' . $IDFilm . '/');
        elseif (empty($ck3)) :
            return new RedirectResponse('/admin/film/' . $IDFilm . '/part/' . $IDPart . '/');
        endif;

        $get = $DB->find('video', ['IDVideo' => $IDVideo, 'IDPart' => $IDPart, 'IDFilm' => $IDFilm])->get();

        if ($request->getMethod() == 'POST') :

            $name = isset($_POST['name']) ? $_POST['name'] : null;
            $episode = isset($_POST['episode']) ? $_POST['episode'] : null;
            $video = isset($_POST['video']) ? $_POST['video'] : null;
            $thumb = isset($_FILES['thumbnail']['name']) ? $_FILES['thumbnail']['name'] : null;
            $thumbnail = isset($_FILES['thumbnail']) ? $_FILES['thumbnail'] : null;

            if (!empty($thumb)) :
                $time = time();
                $target_file = "public/upload/film/episode/" . $time . "-" . $thumb;
                $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $extensions_arr = array("jpg", "png");
            endif;

            if (!empty($episode)) :
                if ($episode != $get->episode) :
                    $episodes = empty(DB::connect()->num_rows('video', ['IDPart' => $IDPart, 'IDFilm' => $IDFilm, 'episode' => $episode])->get()) ? true : false;
                else :
                    $episodes = true;
                endif;
            endif;

            if (empty($name)) :
                $data->status = 'error';
                $data->message = 'Tên tập không được bỏ trống';
            elseif (empty($episode)) :
                $data->status = 'error';
                $data->message = 'Mã tập không được bỏ trống';
            elseif (!$episodes) :
                $data->status = 'error';
                $data->message = 'Mã tập phim của phần này đã tồn tại';
            elseif (empty($video)) :
                $data->status = 'error';
                $data->message = 'Video không được bỏ trống';
            elseif (empty($thumb)) :

                $save = new stdclass();
                $save->episode = $episode;
                $save->name     = $name;
                $save->file  = $video;

                if ($DB->update('video', $save, ['IDVideo' => $IDVideo, 'IDPart' => $IDPart, 'IDFilm' => $IDFilm])->status()) :

                    $data->status = 'success';
                    $data->message = 'Cập nhập tập phim lên thành công';

                else :

                    $data->status = 'error';
                    $data->message = 'Cập nhập tập phim lên thất bại';

                endif;

            elseif (!in_array($FileType, $extensions_arr)) :
                $data->status = "error";
                $data->message = "Định dạng ảnh không hợp lệ";
            elseif (!move_uploaded_file($thumbnail["tmp_name"], $target_file)) :
                $data->status = "error";
                $data->message = "Không thể tải lên ảnh của bạn";
            else :

                $save = new stdclass();
                $save->thumbnail = "/upload/film/episode/" . $time . "-" . $thumb;
                $save->episode = $episode;
                $save->name     = $name;
                $save->file  = $video;

                if (file_exists("public/" . $get->thumbnail)) :
                    unlink("public/" . $get->thumbnail);
                endif;

                if ($DB->update('video', $save, ['IDVideo' => $IDVideo, 'IDPart' => $IDPart, 'IDFilm' => $IDFilm])->status()) :

                    $data->status = 'success';
                    $data->message = 'Cập nhập tập phim lên thành công';

                else :

                    $data->status = 'error';
                    $data->message = 'Cập nhập tập phim lên thất bại';

                endif;
            endif;

        endif;

        return $view->make('admin/video/edit', [
            'data' => $data,
            'get' => $get
        ]);
    }

    public function video(View $view, ServerRequest  $request, $IDFilm, $IDPart)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $ck = $DB->num_rows('film', ['IDFilm' => $IDFilm])->get();
        $ck2 = $DB->num_rows('part', ['IDFilm' => $IDFilm, 'IDPart' => $IDPart])->get();

        if (empty($ck)) :
            return new RedirectResponse('/admin/film/');
        elseif (empty($ck2)) :
            return new RedirectResponse('/admin/film/' . $IDFilm . '/');
        endif;

        $list = $DB->find('video', ['IDFilm' => $IDFilm, 'IDPart' => $IDPart])->all();

        if ($request->getMethod() == 'POST') :


            if (isset($_POST['delete'])) :
                $delete = $_POST['delete'];

                if (empty($delete)) :
                    $data->status = 'error';
                    $data->message = 'ID tập phim cần xóa không được bỏ trống';
                elseif (empty($DB->num_rows('video', ['IDVideo' => $delete, 'IDFilm' => $IDFilm, 'IDPart' => $IDPart])->get())) :
                    $data->status = 'error';
                    $data->message = 'ID tập phim không tồn tại';
                elseif ($DB->delete('video', ['IDVideo' => $delete, 'IDFilm' => $IDFilm, 'IDPart' => $IDPart])->status()) :
                    $data->status = 'success';
                    $data->message = 'Xóa tập phim thành công';
                else :
                    $data->status = 'error';
                    $data->message = 'Xóa tập phim thất bại';
                endif;

            else :

                $name = isset($_POST['name']) ? $_POST['name'] : null;
                $episode = isset($_POST['episode']) ? $_POST['episode'] : null;
                $video = isset($_POST['video']) ? $_POST['video'] : null;
                $thumb = isset($_FILES['thumbnail']['name']) ? $_FILES['thumbnail']['name'] : null;
                $thumbnail = isset($_FILES['thumbnail']) ? $_FILES['thumbnail'] : null;

                if (!empty($thumb)) :
                    $time = time();
                    $target_file = "public/upload/film/episode/" . $time . "-" . $thumb;
                    $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $extensions_arr = array("jpg", "png");
                endif;

                if (empty($name)) :
                    $data->status = 'error';
                    $data->message = 'Tên tập không được bỏ trống';
                elseif (empty($episode)) :
                    $data->status = 'error';
                    $data->message = 'Mã tập không được bỏ trống';
                elseif (!empty(DB::connect()->num_rows('video', ['IDPart' => $IDPart, 'IDFilm' => $IDFilm, 'episode' => $episode])->get())) :
                    $data->status = 'error';
                    $data->message = 'Mã tập phim của phần này đã tồn tại';
                elseif (empty($video)) :
                    $data->status = 'error';
                    $data->message = 'Video không được bỏ trống';
                elseif (empty($thumb)) :
                    $data->status = 'error';
                    $data->message = 'Ảnh bìa video không được bỏ trống';
                elseif (!in_array($FileType, $extensions_arr)) :
                    $data->status = "error";
                    $data->message = "Định dạng ảnh không hợp lệ";
                elseif (!move_uploaded_file($thumbnail["tmp_name"], $target_file)) :
                    $data->status = "error";
                    $data->message = "Không thể tải lên ảnh của bạn";
                else :

                    $save = new stdclass();
                    $save->thumbnail = "/upload/film/episode/" . $time . "-" . $thumb;
                    $save->IDFilm    = $IDFilm;
                    $save->IDPart     = $IDPart;
                    $save->episode = $episode;
                    $save->name     = $name;
                    $save->file  = $video;
                    $save->date     = time();

                    if ($DB->insert('video', $save)->status()) :

                        $data->status = 'success';
                        $data->message = 'Thêm tập phim lên thành công';

                    else :

                        $data->status = 'error';
                        $data->message = 'Thêm tập phim lên thất bại';

                    endif;

                endif;

            endif;

        endif;

        return $view->make('admin/video/index', [
            'data' => $data,
            'list' => $list
        ]);
    }


    public function part(View $view, ServerRequest  $request, $IDFilm)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $ck = $DB->num_rows('film', ['IDFilm' => $IDFilm])->get();
        if (empty($ck)) :
            return new RedirectResponse('/admin/film/');
        endif;

        $list = $DB->find('part', ['IDFilm' => $IDFilm])->all();

        if ($request->getMethod() == 'POST') :
            if (isset($_POST['delete'])) :
                $delete = $_POST['delete'];
                if (empty($delete)) :
                    $data->status = 'error';
                    $data->message = 'ID phần cần xóa không được bỏ trống';
                elseif (empty($DB->num_rows('part', ['IDPart' => $delete])->get())) :
                    $data->status = 'error';
                    $data->message = 'ID phần không tồn tại';
                elseif ($DB->delete('part', ['IDPart' => $delete, 'IDFilm' => $IDFilm])->status()) :
                    $data->status = 'success';
                    $data->message = 'Xóa phần thành công';
                else :
                    $data->status = 'error';
                    $data->message = 'Xóa phần thất bại';
                endif;

            else :
                $name = isset($_POST['name']) ? $_POST['name'] : null;

                if (empty($name)) :
                    $data->status = 'error';
                    $data->message = 'Tên phần không được bỏ trống';
                else :

                    $save = new stdclass();
                    $save->IDFilm     = $IDFilm;
                    $save->name     = $name;
                    $save->date     = time();

                    if ($DB->insert('part', $save)->status()) :

                        $data->status = 'success';
                        $data->message = 'Tạo phần phim thành công';

                    else :

                        $data->status = 'error';
                        $data->message = 'Tạo phần phim thất bại';

                    endif;
                endif;
            endif;
        endif;

        return $view->make('admin/part/index', [
            'data' => $data,
            'list' => $list
        ]);
    }

    public function film(View $view, ServerRequest  $request)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $list = $DB->find('film')->all();

        if ($request->getMethod() == 'POST') :

            if (isset($_POST['delete'])) :
                $delete = $_POST['delete'];
                if (empty($delete)) :
                    $data->status = 'error';
                    $data->message = 'ID phim cần xóa không được bỏ trống';
                elseif (empty($DB->num_rows('film', ['IDFilm' => $delete])->get())) :
                    $data->status = 'error';
                    $data->message = 'ID phim không tồn tại';
                elseif ($DB->delete('film', ['IDFilm' => $delete])->status() && $DB->delete('video', ['IDFilm' => $delete])->status() && $DB->delete('part', ['IDFilm' => $delete])->status()) :
                    $data->status = 'success';
                    $data->message = 'Xóa phim thành công';
                else :
                    $data->status = 'error';
                    $data->message = 'Xóa phim thất bại';
                endif;

            else :

                $name = isset($_POST['name']) ? $_POST['name'] : null;
                $note = isset($_POST['note']) ? $_POST['note'] : null;
                $content = isset($_POST['content']) ? $_POST['content'] : null;
                $category = isset($_POST['category']) ? $_POST['category'] : null;
                $thumb = isset($_FILES['thumbnail']['name']) ? $_FILES['thumbnail']['name'] : null;
                $thumbnail = isset($_FILES['thumbnail']) ? $_FILES['thumbnail'] : null;
                $embed = isset($_POST['embed']) ? $_POST['embed'] : null;
                $slug = str_replace(' ', '-', strtolower(vn_str_filter($name)));

                if (!empty($thumb)) :
                    $time = time();
                    $target_file = "public/upload/film/" . $time . "-" . $thumb;
                    $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $extensions_arr = array("jpg", "png");
                endif;

                if (empty($name)) :
                    $data->status = 'error';
                    $data->message = 'Tên phim không được bỏ trống';
                elseif (empty($content)) :
                    $data->status = 'error';
                    $data->message = 'Nội dung phim không được bỏ trống';
                elseif (empty($category)) :
                    $data->status = 'error';
                    $data->message = 'Thể loại phim không được bỏ trống';
                elseif (empty($thumb)) :
                    $data->status = 'error';
                    $data->message = 'Ảnh bìa phim không được bỏ trống';
                elseif (!in_array($FileType, $extensions_arr)) :
                    $data->status = "error";
                    $data->message = "Định dạng ảnh không hợp lệ";
                elseif (!move_uploaded_file($thumbnail["tmp_name"], $target_file)) :
                    $data->status = "error";
                    $data->message = "Không thể tải lên ảnh của bạn";
                else :

                    $ck_slug = $DB->num_rows('film', ['slug' => $slug])->get();

                    $save = new stdclass();
                    $save->thumbnail = "/upload/film/" . $time . "-" . $thumb;
                    $save->name     = $name;
                    $save->slug     = $slug;
                    $save->category = $category;
                    $save->note     = $note;
                    $save->content  = $content;
                    $save->embed    = $embed;
                    $save->date     = time();
                    $save->status   = 'active';


                    if ($DB->insert('film', $save)->status()) :

                        if (!empty($ck_slug)) :
                            $DB->update('film', ['slug' => $slug . '-' . DB::$getID], ['IDFilm' => DB::$getID])->status();
                        endif;

                        $data->status = 'success';
                        $data->message = 'Đăng tải phim lên thành công';

                    else :

                        $data->status = 'error';
                        $data->message = 'Đăng tải phim lên thất bại';

                    endif;

                endif;

            endif;
        endif;

        return $view->make('admin/film/index', [
            'data' => $data,
            'list' => $list
        ]);
    }


    public function EDitfilm(View $view, ServerRequest  $request, $IDFilm)
    {
        $DB = DB::connect();
        $data = new stdclass();
        $ck = $DB->num_rows('film', ['IDFilm' => $IDFilm])->get();
        if (empty($ck)) :
            return new RedirectResponse('/admin/film/');
        endif;

        $get = $DB->find('film', ['IDFilm' => $IDFilm])->get();

        if ($request->getMethod() == 'POST') :

            $name = isset($_POST['name']) ? $_POST['name'] : null;
            $note = isset($_POST['note']) ? $_POST['note'] : null;
            $content = isset($_POST['content']) ? $_POST['content'] : null;
            $category = isset($_POST['category']) ? $_POST['category'] : null;
            $thumb = isset($_FILES['thumbnail']['name']) ? $_FILES['thumbnail']['name'] : null;
            $thumbnail = isset($_FILES['thumbnail']) ? $_FILES['thumbnail'] : null;
            $embed = isset($_POST['embed']) ? $_POST['embed'] : null;
            $slug = str_replace(' ', '-', strtolower(vn_str_filter($name)));

            if (!empty($thumb)) :
                $time = time();
                $target_file = "public/upload/film/" . $time . "-" . $thumb;
                $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $extensions_arr = array("jpg", "png");
            endif;

            if (empty($name)) :
                $data->status = 'error';
                $data->message = 'Tên phim không được bỏ trống';
            elseif (empty($content)) :
                $data->status = 'error';
                $data->message = 'Nội dung phim không được bỏ trống';
            elseif (empty($category)) :
                $data->status = 'error';
                $data->message = 'Thể loại phim không được bỏ trống';
            elseif (empty($thumb)) :

                $ck_slug = $DB->num_rows('film', ['slug' => $slug])->get();

                $save = new stdclass();
                $save->name     = $name;
                $save->slug     = $slug;
                $save->category = $category;
                $save->note     = $note;
                $save->content  = $content;
                $save->embed    = $embed;


                if ($DB->update('film', $save, ['IDFilm' => $IDFilm])->status()) :

                    if ($slug != $get->slug || $name != $get->name) :
                        if (!empty($ck_slug)) :
                            $DB->update('film', ['slug' => $slug . '-' . $IDFilm], ['IDFilm' => $IDFilm])->status();
                        endif;
                    endif;

                    $data->status = 'success';
                    $data->message = 'Cập nhập phim thành công';

                else :

                    $data->status = 'error';
                    $data->message = 'Cập nhập phim thất bại';

                endif;

            elseif (!in_array($FileType, $extensions_arr)) :
                $data->status = "error";
                $data->message = "Định dạng ảnh không hợp lệ";
            elseif (!move_uploaded_file($thumbnail["tmp_name"], $target_file)) :
                $data->status = "error";
                $data->message = "Không thể tải lên ảnh của bạn";
            else :

                $ck_slug = $DB->num_rows('film', ['slug' => $slug])->get();

                $save = new stdclass();
                $save->thumbnail = "/upload/film/" . $time . "-" . $thumb;
                $save->name     = $name;
                $save->slug     = $slug;
                $save->category = $category;
                $save->note     = $note;
                $save->content  = $content;
                $save->embed    = $embed;

                if (file_exists("public/" . $get->thumbnail)) :
                    unlink("public/" . $get->thumbnail);
                endif;

                if ($DB->update('film', $save, ['IDFilm' => $IDFilm])->status()) :

                    if ($slug != $get->slug || $name != $get->name) :
                        if (!empty($ck_slug)) :
                            $DB->update('film', ['slug' => $slug . '-' . $IDFilm], ['IDFilm' => $IDFilm])->status();
                        endif;
                    endif;

                    $data->status = 'success';
                    $data->message = 'Cập nhập phim thành công';

                else :

                    $data->status = 'error';
                    $data->message = 'Cập nhập phim thất bại';

                endif;

            endif;

        endif;

        return $view->make('admin/film/edit', [
            'data' => $data,
            'get' => $get
        ]);
    }
}
