<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\Paragraph;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

class DeveloperController extends Controller
{
    //Данная функция просто переносит из столбца text в столбец content
    //Сравнивает название темы с названием темы в таблице themes
    //Если совпадает, то вписывает id темы в столбец theme
    public function changeParagraphsFromOldTableV01(Request $request) {
        $paragraphs = Document::all();

        foreach ($paragraphs as &$paragraph) {
            $data = splitStr($paragraph->name);
            $theme = Theme::where('name',$data['themeName'])->select('id')->first();
            if ($theme) {
                $paragraph->theme = $theme->id;
                $paragraph->content = $paragraph->text;
                $paragraph->save();
            }
        }

        // $paragraphs->save();
        return $paragraphs;
    }

    //Данная функция из старой таблицы из HOSTCms взяля названия и сортирувку тем и перенесла в themes
    //А так же в paragraphs.theme поместила id тем
    //А так же извлекла из названия темы сортировку и само название.
    public function changeParagraphsFromOldTable(Request $r) {
        $p = Paragraph::all();//$p=paragraphs

        for ($i=0;$i<count($p);$i++) {
            $themeName = $p[$i]['theme_old'];
            $themeEntry = Theme::where('name',$themeName)->first();

            if (!$themeEntry){//Такой темы нет
                //вызываем splitStr
                //Добавляем новую запись в themes в themes.name и themes.sort
                
                $data=splitStr($themeName);

                $themeID = Theme::insertGetId(
                    [
                        'name' => $data['themeName'], 
                        'sort' => $data['themeSort']
                    ]
                );
                
                Paragraph::where('id',$p[$i]->id)->update(['theme'=>$themeID]);
  


            } else { //такая тема уже есть
                //Берем id темы и записываем ее в paragraphs.theme
                
                Paragraph::where('id',$p[$i]->id)->update(['theme'=>$themeEntry->id]);
                



                
            }
        }

        // $themeEntry = Theme::where('name','Простая теа')->first();

        return "OK";
    }

    //Данная функция установит url у тем по типу them_1
    public function setThemeUrl(){
        $themes = Theme::select('id','sort')->get();

        for ($i = 0;$i<count($themes);$i++) {
            $url = 'them_'.$themes[$i]->sort;
            Theme::where('id',$themes[$i]->id)->update(['url'=>$url]);
        }

        return "OK";
    }

    public function test() {
        $res = Paragraph::all();

        $res=Paragraph::where('id',$res[0]->id)->update(['theme'=>345]);
        return $res;
    }

    public function setAllowedThemes(Request $r) {
        $themes = Theme::select('id')->get();
        $data = [];

        for ($i=0;$i<count($themes);$i++) {
            $data[$i] = [
                'id'=>$themes[$i]->id,
                'allowed'=>true
            ];
        }

        $jsonData = json_encode($data);

        User::where('id',$r->user_id)->update(['allowed_themes'=>$jsonData]);

        return $jsonData;
    }

    public function getHeaders() {
        $headers = apache_request_headers();
        // foreach ($headers as $header => $value) {
        //     echo "$header: $value <br />\n";
        // }
        // return "sdfsdf";
        return $headers;
    }

    public function getToken(Request $request) {
        $user = User::where("email",$request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return "Такого пользователя нет";
        }

        $token = $user->createToken("sdf");
        return ["message"=>"Вы авторизованы",'token' => $token->plainTextToken];
    }

    public function getMe(Request $request) {
        return $request->user();
    }


    public function splitParagraphs(Request $request) {
        $themes = Theme::all();

        foreach ($themes as $theme) {
            $paragraphs = Paragraph::where('theme',$theme->id)->get();

            $last_paragraph = $paragraphs[count($paragraphs)-1];

            $id=$last_paragraph->id;
    
            $new_paragraphs_content = explode('<p style="margin-left: 0cm; margin-right: 0cm;"> </p>',$last_paragraph->content);
    
            if (count($new_paragraphs_content) == 1) {
                continue;
            }
            
            $sort = $last_paragraph->sort;
    
            if ($sort == null) {
                $sort = 1;
            }
    
            Paragraph::where('id',$id)->update([
                                                    'content'=>$new_paragraphs_content[0],
                                                    'sort'=>$sort
                                                ]);
    
            for ($i=1;$i<count($new_paragraphs_content);$i++) {
                $p = Paragraph::create([
                    'content'=>$new_paragraphs_content[$i],
                    'theme'=>$theme->id,
                    'sort'=>$sort+$i
                ]);
            }
        }

        return "ok";
    }
}
/*
"id": 21,
"created_at": null,
"updated_at": null,
"theme_old": "1. Человек. Природное и общественное в человеке",
"content": "<p style",
"theme": 0,
"sort": null

*/

function splitStr($str) {
    $b=false;
    $i=0;
    $themeName = '';
    $themeSort = '';

    for ($i=0;$i<strlen($str);$i++) {
        if (!$b){
            if ($str{$i}=='.') {
                $b=true;
                if ($str{$i+1} == ' ') {
                    $i=$i+1;
                }
                continue;
            }

            $themeSort = $themeSort.$str{$i};
        } else {
            $themeName .=$str{$i};
        }

    }

    return [
        'themeName'=>$themeName,
        'themeSort'=>(int)$themeSort
    ];
}

