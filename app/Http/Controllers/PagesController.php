<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Theme;
use App\Models\Paragraph;
use App\Models\User;

class PagesController extends Controller
{
    function getSections() {
        return Section::orderBy('sort', 'asc')->get();
    }

    function getThemesAndSectionBySectionUrl(Request $r) {
        $section = Section::where('url',$r->section_url)->first();
        
        $themes = Theme::where('section',$section->id)->select('name','url','sort')->orderBy('sort', 'asc')->get();

        $data = [
            'section' => $section->name,
            'themes' => $themes
        ];

        return $data;
    }

    // URL get_section_name_and_theme_name_by_url
    // {
    //     "section_url":"section_5",
    //     "theme_url":"them_36"
    // }
    function getSectionNameAndThemeNameByUrl(Request $request) {

        $section = Section::where('url',$request->section_url)->select('name','id')->first();

        if ($section == null) {
            return ["status"=>"notFound"];
        } 

        $theme = Theme::where('section',$section->id)
                        ->where('url',$request->theme_url)
                        ->select('name', 'sort')
                        ->first();
        
        if ($theme == null) {
            return ["status"=>"notFound"];
        }

        return [
            'status'=>'success',
            'section_name' => $section->name,
            'theme_name' => $theme->sort.'. '.$theme->name
        ];
    }

    // URL get_paragraps_by_section_and_theme_url
    // {
    //     "section_url":"section_5",
    //     "them_url":"theme_36"
    // }
    function getParagraphsBySectionAndThemeUrl(Request $request) {
        $status='';

        $section = Section::where('url',$request->section_url)->first();
        if ($section == null) {
            return ["status"=>"notFound"];
        }
    
        $theme = Theme::where('section',$section->id)
                        ->where('url',$request->theme_url)
                        ->select('id','name', 'sort')
                        ->first();

        if ($theme == null) {
            return ["status"=>"notFound"];
        }

        $user = $request->user();

        if ($user == null) {
            $data = [
                'status' => 'notAuth',
                'section' => $section->name,
                'theme' => $theme->sort.". ".$theme->name
            ];
            return $data;
        }
        //Определяем разрешена ли тема
        $permitions = $user->allowed_themes;
        $permitions = json_decode($permitions);

        if ($permitions == null) {
            $data = [
                'status' => 'notAllowed',
                'section' => $section->name,
                'theme' => $theme->sort.". ".$theme->name
            ];
            return $data;
        }

        if (gettype($permitions)!="array") {
            $status = 'notAllowed';
        } else {
            $isAllowed=false;

            for ($i=0;$i<count($permitions);$i++) {
                if ($permitions[$i]->id == $theme->id and $permitions[$i]->allowed == 'true') {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                $data = [
                    'status' => 'notAllowed',
                    'section' => $section->name,
                    'theme' => $theme->sort.". ".$theme->name
                ];
                return $data;
            }
        }

        $paragraphs = Paragraph::where('theme',$theme->id)
                        ->orderBy('sort', 'asc')
                        ->get();

        //Добавляем свойство isInFavorites - добавлено в избранное
        $favorites = json_decode($user->favorites);
        
        if ($favorites == null or gettype($favorites) != 'array') {
            $favorites = [];
        }

        $newParagraphs = [];
        for ($i=0;$i<count($paragraphs);$i++) {
            $isInFavorites = false;

            for ($j=0;$j<count($favorites);$j++) {
                if ($paragraphs[$i]->id == $favorites[$j]->id) {
                    $isInFavorites = true;
                }
            }

            $newParagraphs[$i] = [
                'id'=>$paragraphs[$i]->id,
                'content'=>$paragraphs[$i]->content,
                'isInFavorites'=>$isInFavorites
            ];
        }

        $data = [
            'status' => 'success',
            'section' => $section->name,
            'theme' => $theme->sort.". ".$theme->name,
            'paragraphs' => $newParagraphs
        ];
        return $data;

    }

    // http://api.obschestvo-znanie.ru/api/set_paragraph_to_learn
    // {
    //     "paragraph_id":21,
    //     "name":"Денис",
    //     "email":"dvkuklin@yandex.ru",
    //     "password":********
    // }
    public function setParagraphToFavorites(Request $request) {
        $paragraph_id = (int)$request->paragraph_id;

        $paragraph = Paragraph::find($paragraph_id);

        if (!$paragraph) {
            return [
                'status'=>'notFound',
                'message'=>'Paragraph does not exist.'
            ];
        }

        $inserted_paragraph = ['id'=>$paragraph_id,'date_time'=>time()+(3*60*60)];

        $user = $request->user();

        $favorites = json_decode($user->favorites);
        
        if ($user->favorites==null or gettype($favorites)!='array') {
            
            $user->favorites = json_encode([$inserted_paragraph]);

            try {
                $user->save();

                return ['status'=>'success',
                        'message'=>'Data was seted first time.'];
            }catch(\Exception $e){
                return ['status'=>'error',
                        'message'=>'BD error 1'];
            }

        }

        //Если такой параграф уже присутствует
        for ($i=0;$i<count($favorites);$i++) {
            if ($favorites[$i]->id == $paragraph_id) {
                return ['status'=>'alreadeyExists',
                        'message'=>'Data already in list'];
            } 
        }

        array_push($favorites,$inserted_paragraph);

        $user->favorites = json_encode($favorites);
        try {
            $user->save();

            return ['status'=>'success',
                           'message'=>'That is OK'];
        }catch(\Exception $e) {
            return ['status'=>'error',
            'message'=>'BD error'];
        }

    }

    public function deleteParagraphFromFavorites(Request $request) {
        $user = $request->user();

        $favorites = json_decode($user->favorites);

        $newFavorites = [];
        $newIndex=0;

        for ($i=0;$i<count($favorites);$i++) {
            if ($favorites[$i]->id==$request->paragraph_id) {
                continue;
            }

            $newFavorites[$newIndex] = $favorites[$i];
            $newIndex++;
        }
        $user->favorites=json_encode($newFavorites);

        try {
            $res = $user->save();
            return [
                'status'=>'success',
                'message'=>$res
            ];
        }catch(\Exception $e) {
            return ['status'=>'error',
                    'message'=>'BD error'];
        }
    }

    public function getDataForFavorites(Request $request) {

        $favorites = json_decode($request->user()->favorites);

        if ($favorites == null or gettype($favorites) != 'array') {
            return [
                'status' => 'noData',
                'message' => 'Data not exists'
            ];
        }

        $paragraphs_id = [];
        foreach ($favorites as $paragraph) {
            array_push($paragraphs_id,$paragraph->id);
        }

        $dataForFavorites = Paragraph::whereIn('paragraphs.id',$paragraphs_id)
                                        ->join('themes','paragraphs.theme','=','themes.id')
                                        ->join('sections','themes.section','=','sections.id')
                                        ->select('paragraphs.id as id',
                                                 'content',
                                                 'themes.sort as theme_sort',
                                                 'themes.name as theme_name',
                                                 'sections.sort as section_sort',
                                                 'sections.name as section_name',
                                                 'sections.image as section_image')
                                        ->get();

        //Добавляем время дату
        $dataForFavoritesWithDateTime = [];

        foreach($dataForFavorites as $i => $item) {
            foreach($favorites as $favorite_item) {
                if ($favorite_item->id == $item->id) {
                    $dataForFavoritesWithDateTime[$i] = [
                        'id' => $item->id,
                        'content' => $item->content,
                        'theme_sort' => $item->theme_sort,
                        'theme_name' => $item->theme_name,
                        'section_sort' => $item->section_sort,
                        'section_name' => $item->section_name,
                        'section_image' => $item->section_image,
                        'date_time' => $favorite_item->date_time
                    ];
                }
            }
        }

        //Сортировка по времени
        function sort($data) {
            function isNotSorted($data) {
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]['date_time']<$data[$i+1]['date_time']) {
                        return true;
                    }
                }
    
                return false;
            }

            while (isNotSorted($data)) {
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]['date_time']<$data[$i+1]['date_time']) {
                        $temp = [
                            'id' => $data[$i]['id'],
                            'content' => $data[$i]['content'],
                            'theme_sort' => $data[$i]['theme_sort'],
                            'theme_name' => $data[$i]['theme_name'],
                            'section_sort' => $data[$i]['section_sort'],
                            'section_name' => $data[$i]['section_name'],
                            'section_image' => $data[$i]['section_image'],
                            'date_time' => $data[$i]['date_time']
                        ];
    
                        $data[$i]['id'] = $data[$i+1]['id'];
                        $data[$i]['content'] = $data[$i+1]['content'];
                        $data[$i]['theme_sort'] = $data[$i+1]['theme_sort'];
                        $data[$i]['theme_name'] = $data[$i+1]['theme_name'];
                        $data[$i]['section_sort'] = $data[$i+1]['section_sort'];
                        $data[$i]['section_name'] = $data[$i+1]['section_name'];
                        $data[$i]['section_image'] = $data[$i+1]['section_image'];
                        $data[$i]['date_time'] = $data[$i+1]['date_time'];
    
                        $data[$i+1]['id'] = $temp['id'];
                        $data[$i+1]['content'] = $temp['content'];
                        $data[$i+1]['theme_sort'] = $temp['theme_sort'];
                        $data[$i+1]['theme_name'] = $temp['theme_name'];
                        $data[$i+1]['section_sort'] = $temp['section_sort'];
                        $data[$i+1]['section_name'] = $temp['section_name'];
                        $data[$i+1]['section_image'] = $temp['section_image'];
                        $data[$i+1]['date_time'] = $temp['date_time'];
                    }
                }
            }

            return $data;
        }
        //END сортировка по времени

        if ($request->sort == 'date_time_desc') {
            $dataForFavoritesWithDateTime = sort($dataForFavoritesWithDateTime);
        }

        foreach ($dataForFavoritesWithDateTime as &$item) {
            $item['date_time'] = date("d.m.Y H.i.s",$item['date_time']);
        }

        return [
            'status'=>'success',
            'favorites'=>$dataForFavoritesWithDateTime
        ];
    }
}
