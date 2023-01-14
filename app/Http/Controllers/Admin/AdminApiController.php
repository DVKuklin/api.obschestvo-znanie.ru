<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Theme;
use App\Models\Paragraph;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{
    // http://127.0.0.1:8000/api/admin/get_data_for_user_extended
    // {
    //     "current_user":5,
    //     "current_section":5
    // }
    public function getDataForUserExtended(Request $request) {

        //Верифицируем current_user
        $user_id = (int)$request->current_user;
        if (gettype($user_id)!="integer") {
            $currentUser = User::select('id')->orderBy('id', 'asc')->get();
            $user_id = $currentUser[0]->id; 
        } else {
            $currentUser = User::where('id',$user_id)->select('id')->first();
            if ($currentUser) {
                $user_id = $currentUser->id;
            } else {
                $currentUser = User::select('id')->orderBy('id', 'asc')->get();
                $user_id = $currentUser[0]->id; 
            }
        }

        //Верификация current_section
        $current_section = (int)$request->current_section;
        if (gettype($current_section)!="integer") {
            $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
            $current_section = $currentSection[0]->id; 
        } else {
            $currentSection = Section::where('id',$current_section)->select('id')->first();
            if ($currentSection) {
                $current_section = $currentSection->id;
            } else {
                $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
                $current_section = $currentSection[0]->id; 
            }
        }
        
        //Пользователи
        $users = User::select('id','name')->get();

        //Разделы
        $sections = Section::select('id','name')->orderBy('id', 'asc')->get();

        //Темы с разрешениями по пользователю
        $res = User::where('id',$user_id)->select('allowed_themes')->first();
        $permitions = json_decode($res->allowed_themes);

        $themes = Theme::where('section',$current_section)->select('id','sort','name')->get();

        $themes_with_permissions = [];

        for ($i=0;$i<count($themes);$i++) {
            $permition=false;
            
            if ($permitions) {
                for ($j=0;$j<count($permitions);$j++) {
                    if ($permitions[$j]->id == $themes[$i]->id) {
                        $permition = $permitions[$j]->allowed;
                        break;
                    }
                }
            }


            $themes_with_permissions[$i] = [
                'theme_id'=>$themes[$i]->id,
                'theme'=>$themes[$i]->name,
                'sort'=>$themes[$i]->sort,
                'permition'=>$permition
            ];
        }

        //Результирующий набор данных
        $data = [
            'current_user'=>$user_id,
            'current_section'=>$current_section,
            'status'=>'success',
            'users'=>$users,
            'sections'=>$sections,
            'themes'=>$themes_with_permissions,
        ];
        return $data;
    }

    public function setPermition(Request $request) {

        $res = User::where('id',$request->current_user)->select('allowed_themes')->first();

        if (!$res) {
            return [
                'status'=>'error',
                'message'=>'Something went wrong.'
            ];
        }
        $permitions = json_decode($res->allowed_themes);
        $isDone = false;//Установили разрешение или нет
        
        if (gettype($permitions)=='array') {
            for ($i=0;$i<count($permitions);$i++) {
                if ($permitions[$i]->id == $request->theme_id) {
                    $permitions[$i]->allowed = $request->permition;
                    $isDone = true;
                    break;
                }
            }
    
            if (!$isDone) {
                array_push($permitions,[
                    "id"=>$request->theme_id,
                    "allowed"=>$request->permition
                ]);
            }            
        } else {
            $permitions = [
                [
                    "id"=>$request->theme_id,
                    "allowed"=>$request->permition
                ]
            ];
        }

        $jsonData = json_encode($permitions);

        $res = User::where('id',$request->current_user)->update(['allowed_themes'=>$jsonData]);

        if ($res) {
            $status = 'success';
            $message = 'That is ok';
        } else {
            $status = 'error';
            $message = 'Somethink went wrong';  
        }

        $data = [
            'status'=>$status,
            'message'=>$message
        ];

        return $data;
    }

    public function setPermitions(Request $request) {
        
        $res = User::where('id',$request->current_user)->select('allowed_themes')->first();

        if (!$res) {
            return [
                'status'=>'error',
                'message'=>'Something went wrong.'
            ];
        }

        $permitions = json_decode($res->allowed_themes);

        $themes = $request->themes;

        for ($j=0;$j<count($themes);$j++) {

            $isDone = false;//Установили разрешение или нет
        
            if (gettype($permitions)=='array') {
                for ($i=0;$i<count($permitions);$i++) {
                    if ($permitions[$i]->id == $themes[$j]) {
                        $permitions[$i]->allowed = $request->permition;
                        $isDone = true;
                        break;
                    }
                }
        
                if (!$isDone) {
                    array_push($permitions,[
                        "id"=>$themes[$j],
                        "allowed"=>$request->permition
                    ]);
                }            
            } else {
                $permitions = [
                    [
                        "id"=>$themes[$j],
                        "allowed"=>$request->permition
                    ]
                ];
            }    
        }

        $jsonData = json_encode($permitions);

        $res = User::where('id',$request->current_user)->update(['allowed_themes'=>$jsonData]);

        if ($res) {
            $status = 'success';
            $message = 'That is ok';
        } else {
            $status = 'error';
            $message = 'Somethink went wrong';  
        }

        $data = [
            'status'=>$status,
            'message'=>$message
        ];

        return $data;

    }

    public function getDataForParagraphsEdit(Request $r) {

        //Верификация current_section
        $current_section = (int)$r->current_section;
        if (gettype($current_section)!="integer") {
            $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
            $current_section = $currentSection[0]->id; 
        } else {
            $currentSection = Section::where('id',$current_section)->select('id')->first();
            if ($currentSection) {
                $current_section = $currentSection->id;
            } else {
                $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
                $current_section = $currentSection[0]->id; 
            }
        }

        //Верификация current_theme
        $current_theme = (int)$r->current_theme;

        if (gettype($current_theme)!="integer") {
            $currentTheme = Theme::select('id','sort')->where('section',$current_section)->orderBy('sort', 'asc')->get();            
            $current_theme = $currentTheme[0]->id; 
        } else {
            $currentTheme = Theme::where('id',$current_theme)->where('section',$current_section)->select('id')->first();
            if ($currentTheme) {
                $current_theme = $currentTheme->id;
            } else {
                $currentTheme = Theme::select('id','sort')->where('section',$current_section)->orderBy('sort', 'asc')->get();
                if (count($currentTheme)==0) {
                    $current_theme=null;
                } else {
                    $current_theme = $currentTheme[0]->id; 
                }
            }
        }
        
        //Достаем параграфы

        if ($current_theme == null) {
            $paragraphs = null;
        } else {
            $paragraphs = Paragraph::where('theme',$current_theme)->select('id','content','sort')->get();
            if (count($paragraphs)==0) $paragraphs = null;
        }

        //Результирующий набор данных
        $data = [
            'status'=>'success',
            'current_section'=>$current_section,
            'current_theme'=>$current_theme,
            'paragraphs' => $paragraphs
        ];
        return $data;
    }
}
