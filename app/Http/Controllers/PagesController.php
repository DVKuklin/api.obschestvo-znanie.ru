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

    public function getDataForFavoritesOrderBySectionThemeAsc(Request $request) {
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
                                                 'paragraphs.sort as paragraph_sort',
                                                 'content',
                                                 'themes.sort as theme_sort',
                                                 'themes.name as theme_name',
                                                 'sections.sort as section_sort',
                                                 'sections.name as section_name',
                                                 'sections.image as section_image')
                                        ->orderBy('sections.sort','asc')
                                        ->orderBy('themes.sort','asc')
                                        ->orderBy('paragraphs.sort','asc')
                                        ->paginate(5);
        

        //Добавляем время дату
        foreach($dataForFavorites as $i => &$item) {
            foreach($favorites as $favorite_item) {
                if ($favorite_item->id == $item->id) {
                    $item->date_time = date("d.m.Y H.i.s",$favorite_item->date_time);
                }
            }
        }

        return [
            'status'=>'success',
            'favorites'=>$dataForFavorites,
        ];
    }

    public function getDataForFavoritesOrderBySectionThemeDesc(Request $request) {
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
                                                 'paragraphs.sort as paragraph_sort',
                                                 'content',
                                                 'themes.sort as theme_sort',
                                                 'themes.name as theme_name',
                                                 'sections.sort as section_sort',
                                                 'sections.name as section_name',
                                                 'sections.image as section_image')
                                        ->orderBy('sections.sort','desc')
                                        ->orderBy('themes.sort','desc')
                                        ->orderBy('paragraphs.sort','desc')
                                        ->paginate(5);
        

        //Добавляем время дату
        foreach($dataForFavorites as $i => &$item) {
            foreach($favorites as $favorite_item) {
                if ($favorite_item->id == $item->id) {
                    $item->date_time = date("d.m.Y H.i.s",$favorite_item->date_time);
                }
            }
        }

        return [
            'status'=>'success',
            'favorites'=>$dataForFavorites,
        ];
    }

    public function getDataForFavoritesOrderByDateTimeAsc (Request $request) {
        $pagination=5;
        $favorites = json_decode($request->user()->favorites);

        if ($favorites == null) {
            return [
                'status' => 'noData',
                'message' => 'В избранном пока ничего нет'
            ];
        }

        //Сортируем по времени
        function sortAsc($data) {
            
            function isNotSorted($data) {
                
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]->date_time>$data[$i+1]->date_time) {
                        return true;
                    }
                }
                return false;
            }

            while (isNotSorted($data)) {
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]->date_time>$data[$i+1]->date_time) {
                        $temp = [
                            'id'=>$data[$i]->id,
                            'date_time'=>$data[$i]->date_time
                        ];

                        $data[$i]->id = $data[$i+1]->id;
                        $data[$i]->date_time = $data[$i+1]->date_time;
    
                        $data[$i+1]->id = $temp['id'];
                        $data[$i+1]->date_time =$temp['date_time'];
                    }
                }
            }

            return $data;
        }
        //END сортировка по времени восходящая

        $favorites = sortAsc($favorites);
        //Переводим дату в строку
        for ($i=0;$i<count($favorites);$i++) {
            $favorites[$i]->date_time = date("d.m.Y H.i.s",$favorites[$i]->date_time);
        }

        $count_of_paragraphs = count($favorites);

        $count_of_pages = $count_of_paragraphs/$pagination;

        if ($count_of_paragraphs % $pagination > 0) {
            $count_of_pages++;
        }

        //Валидация текущей страницы
        if ($request->page) {
            $current_page = (int)$request->page;
        } else {
            $current_page = 1;
        }

        if ($current_page > $count_of_pages or $current_page<=0) {
            $current_page = 1;
        }

        //Достаем параграфы текущей страницы
        $current_paragraphs = [];

        for ($i=0;$i<$pagination;$i++) {
            $index = ($current_page-1)*$pagination+$i;
            if ($index>count($favorites)-1) {
                break;
            }
            $current_paragraphs[$i] = [
                'id'=>$favorites[$index]->id,
                'date_time'=>$favorites[$index]->date_time
            ];
        }

        //Создаем ссылки для кнопок
        $url = url()->current();
        if ($current_page == 1) {
            $previous_url = null;
        } else {
            $previous_page = $current_page -1;
            $previous_url = $url.'?page='.$previous_page;
        }

        $links[0] = [
            'url' => $previous_url,
            'label' => '<',
            'active' => false
        ];

        for ($i=1;$i<=$count_of_pages;$i++) {
            $active = false;
            if ($i == $current_page) {
                $active = true;
            }
            $links[$i] = [
                'url' => $url.'?page='.$i,
                'label' => $i,
                'active' => $active
            ];
        }

        if ($current_page == $count_of_pages) {
            $next_url = null;
        } else {
            $next_page = $current_page +1;
            $next_url = $url.'?page='.$next_page;
        }

        array_push($links,[
            'url' => $next_url,
            'label' => '>',
            'active' => false
        ]);

        //Достаем параграфы для текущей страницы из базы
        $dataFavorites = [];
        foreach ($current_paragraphs as $i => $paragraph) {
            $dataFavorites[$i] = Paragraph::where('paragraphs.id',$paragraph['id'])
                                ->join('themes','paragraphs.theme','=','themes.id')
                                ->join('sections','themes.section','=','sections.id')
                                ->select('paragraphs.id as id',
                                        'paragraphs.sort as paragraph_sort',
                                        'content',
                                        'themes.sort as theme_sort',
                                        'themes.name as theme_name',
                                        'sections.sort as section_sort',
                                        'sections.name as section_name',
                                        'sections.image as section_image')
                                ->first();
            $dataFavorites[$i]['date_time'] = $paragraph['date_time'];
        }

        return [
            "status" => 'success',
            'favorites' => [
                'data' => $dataFavorites,
                'links' => $links
            ]
        ];
    }

    public function getDataForFavoritesOrderByDateTimeDesc (Request $request) {
        $pagination=5;
        $favorites = json_decode($request->user()->favorites);

        if ($favorites == null) {
            return [
                'status' => 'noData',
                'message' => 'В избранном пока ничего нет'
            ];
        }

        //Сортируем по времени
        function sortAsc($data) {
            
            function isNotSorted($data) {
                
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]->date_time<$data[$i+1]->date_time) {
                        return true;
                    }
                }
                return false;
            }

            while (isNotSorted($data)) {
                for ($i=0;$i<count($data)-1;$i++) {
                    if ($data[$i]->date_time<$data[$i+1]->date_time) {
                        $temp = [
                            'id'=>$data[$i]->id,
                            'date_time'=>$data[$i]->date_time
                        ];

                        $data[$i]->id = $data[$i+1]->id;
                        $data[$i]->date_time = $data[$i+1]->date_time;
    
                        $data[$i+1]->id = $temp['id'];
                        $data[$i+1]->date_time =$temp['date_time'];
                    }
                }
            }

            return $data;
        }
        //END сортировка по времени восходящая

        $favorites = sortAsc($favorites);
        //Переводим дату в строку
        for ($i=0;$i<count($favorites);$i++) {
            $favorites[$i]->date_time = date("d.m.Y H.i.s",$favorites[$i]->date_time);
        }

        $count_of_paragraphs = count($favorites);

        $count_of_pages = $count_of_paragraphs/$pagination;

        if ($count_of_paragraphs % $pagination > 0) {
            $count_of_pages++;
        }

        //Валидация текущей страницы
        if ($request->page) {
            $current_page = (int)$request->page;
        } else {
            $current_page = 1;
        }

        if ($current_page > $count_of_pages or $current_page<=0) {
            $current_page = 1;
        }

        //Достаем параграфы текущей страницы
        $current_paragraphs = [];

        for ($i=0;$i<$pagination;$i++) {
            $index = ($current_page-1)*$pagination+$i;
            if ($index>count($favorites)-1) {
                break;
            }
            $current_paragraphs[$i] = [
                'id'=>$favorites[$index]->id,
                'date_time'=>$favorites[$index]->date_time
            ];
        }

        //Создаем ссылки для кнопок
        $url = url()->current();
        if ($current_page == 1) {
            $previous_url = null;
        } else {
            $previous_page = $current_page -1;
            $previous_url = $url.'?page='.$previous_page;
        }

        $links[0] = [
            'url' => $previous_url,
            'label' => '<',
            'active' => false
        ];

        for ($i=1;$i<=$count_of_pages;$i++) {
            $active = false;
            if ($i == $current_page) {
                $active = true;
            }
            $links[$i] = [
                'url' => $url.'?page='.$i,
                'label' => $i,
                'active' => $active
            ];
        }

        if ($current_page == $count_of_pages) {
            $next_url = null;
        } else {
            $next_page = $current_page +1;
            $next_url = $url.'?page='.$next_page;
        }

        array_push($links,[
            'url' => $next_url,
            'label' => '>',
            'active' => false
        ]);

        //Достаем параграфы для текущей страницы из базы
        $dataFavorites = [];
        foreach ($current_paragraphs as $i => $paragraph) {
            $dataFavorites[$i] = Paragraph::where('paragraphs.id',$paragraph['id'])
                                ->join('themes','paragraphs.theme','=','themes.id')
                                ->join('sections','themes.section','=','sections.id')
                                ->select('paragraphs.id as id',
                                        'paragraphs.sort as paragraph_sort',
                                        'content',
                                        'themes.sort as theme_sort',
                                        'themes.name as theme_name',
                                        'sections.sort as section_sort',
                                        'sections.name as section_name',
                                        'sections.image as section_image')
                                ->first();
            // return var_dump($dataFavorites[$i]);

            $dataFavorites[$i]['date_time'] = $paragraph['date_time'];
        }

        return [
            "status" => 'success',
            'favorites' => [
                'data' => $dataFavorites,
                'links' => $links
            ],
            'count' => count($dataFavorites)
        ];
    }
}
