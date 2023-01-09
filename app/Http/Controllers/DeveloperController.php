<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\Paragraph;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DeveloperController extends Controller
{
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

