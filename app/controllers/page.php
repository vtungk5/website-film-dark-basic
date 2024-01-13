<?php

namespace App\Controllers;

use MiladRahimi\PhpRouter\View\View;
use Laminas\Diactoros\Response\HtmlResponse;
use DB;
use stdclass;

class PAGES
{
    public function index(View $view)
    {
        $DB = DB::connect();
        $film = $DB->find('film', null, ['RAND()'],[20])->all();
        return $view->make('home/index', ['film' => $film]);
    }

    public function video(View $view, $slug, $IDPart, $episode)
    {
        $DB = DB::connect();

        $ckfilm = $DB->num_rows('film', ['slug' => $slug])->get();

        if (empty($ckfilm)) :
            return new HtmlResponse('Đi lạc à chú em!', 404);
        endif;

        $film = $DB->find('film', ['slug' => $slug])->get();

        $ckPart = $DB->num_rows('part', ['IDFilm' => $film->IDFilm, 'IDPart' => $IDPart])->get();
        $ckVideo = $DB->num_rows('video', ['IDFilm' => $film->IDFilm, 'IDPart' => $IDPart, 'episode' => $episode])->get();

        $part = $DB->find('part', ['IDFilm' => $film->IDFilm, 'IDPart' => $IDPart])->get();
        $video = $DB->find('video', ['IDFilm' => $film->IDFilm, 'IDPart' => $IDPart, 'episode' => $episode])->get();

        $listEpisode = $DB->find('video', ['IDFilm' => $film->IDFilm, 'IDPart' => $IDPart])->all();
        $listPart = $DB->find('part', ['IDFilm' => $film->IDFilm])->all();

        $head = new stdclass;

        if (!empty($ckPart)) :

            if (!empty($ckVideo)) :
                $head->title = $part->name . ' | ' . $video->name . ' ' . $film->name;
            else :
                $head->title = $part->name . ' | ' . $film->name;
            endif;

        else :
            $head->title = $film->name;
        endif;

        return $view->make('video/index', [
            'film' => $film,
            'part' => $part,
            'head' => $head,
            'video' => $video,
            'listPart' => $listPart,
            'listEpisode' => $listEpisode
        ]);
    }
}
