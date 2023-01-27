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
        $users = User::select('id','name','email')->get();

        //Разделы
        $sections = Section::select('id','name')->orderBy('id', 'asc')->get();

        //Темы с разрешениями по пользователю
        $user = User::where('id',$user_id)->select('allowed_themes')->first();
        $permitions = json_decode($user->allowed_themes);

        $themes = Theme::where('section',$current_section)
                        ->select('id','sort','name')
                        ->orderBy('sort','asc')
                        ->get();

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
        
        $user = User::where('id',$request->current_user)->select('allowed_themes')->first();

        if (!$user) {
            return [
                'status'=>'error',
                'message'=>'Something went wrong.'
            ];
        }

        $permitions = json_decode($user->allowed_themes);

        $themes = $request->themes;

        for ($j=0;$j<count($themes);$j++) {

            $isDone = false;//Установили разрешение или нет
        
            if ($permitions!=null) {
                for ($i=0;$i<count($permitions);$i++) {

                    if (isset($permitions[$i]->id)) {
                        if ($permitions[$i]->id == $themes[$j]) {
                            $permitions[$i]->allowed = $request->permition;
                            $isDone = true;
                            break;
                        }
                    } else {
                        if ($permitions[$i]['id'] == $themes[$j]) {
                            $permitions[$i]['allowed'] = $request->permition;
                            $isDone = true;
                            break;
                        }
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
                    (object)[
                        "id"=>$themes[$j],
                        "allowed"=>$request->permition
                    ]
                ];
            }    
        }

        $jsonData = json_encode($permitions);

        $user = User::where('id',$request->current_user)->update(['allowed_themes'=>$jsonData]);

        if ($user) {
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

    public function getDataForParagraphsEdit(Request $request) {
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

        $sections = Section::orderBy('sort','asc')->select('id','name')->get();

        //Верификация current_theme
        $current_theme = (int)$request->current_theme;

        if (gettype($current_theme)!="integer") {
            $currentTheme = Theme::select('id')
                                    ->where('section',$current_section)
                                    ->get();            
            $current_theme = $currentTheme[0]->id; 
        } else {
            $currentTheme = Theme::where('id',$current_theme)
                                    ->where('section',$current_section)
                                    ->select('id')
                                    ->first();
            if ($currentTheme) {
                $current_theme = $currentTheme->id;
            } else {
                $currentTheme = Theme::select('id')
                                ->where('section',$current_section)
                                ->orderBy('sort')
                                ->get();
                if ($currentTheme) {
                    $current_theme = $currentTheme[0]->id;
                } else {
                    $current_theme = null;
                }
            }
        }
        
        $themes = Theme::where('section','=',$current_section)
                        ->select('id','sort','name')
                        ->orderBy('sort')
                        ->get();

        //Достаем параграфы

        if ($current_theme == null) {
            $paragraphs = null;
        } else {
            $paragraphs = Paragraph::where('theme',$current_theme)
                                    ->select('id','content','sort')
                                    ->orderBy('sort','asc')
                                    ->get();
            if (count($paragraphs)==0) $paragraphs = null;
        }

        // Результирующий набор данных
        $data = [
            'status'=>'success',
            'current_section'=>$current_section,
            'current_theme'=>$current_theme,
            'themes'=>$themes,
            'sections' => $sections,
            'paragraphs' => $paragraphs
        ];
        return $data;
    }

    public function addParagraph(Request $request) {

        $theme = Theme::where('id',$request->theme)->select('id')->first();

        if (!$theme) {
            return [
                'status'=>'error',
                'message'=>'Theme not found'
            ];
        }

        $new_paragraph_id = null;

        try {
            $new_paragraph_id = Paragraph::insertGetId([
                'theme'=>$theme->id,
            ]);
            if (!$new_paragraph_id) {
                return [
                    'status'=>'BDError'
                ];
            }
        }catch(\Exception $e){
            return [
                'status'=>'BDException'
            ];
        }


        $paragraphs = Paragraph::where('theme',$request->theme)
                                ->select('id')
                                ->orderBy('sort','asc')
                                ->get();

        if (!$paragraphs) {
            return [
                'status'=>'error',
                'message'=>'Some thing went wrong.'
            ];
        }

        try {
            $index = 1;

            foreach ($paragraphs as $paragraph) {

                if ($new_paragraph_id == $paragraph->id) {
                    continue;
                }
    
                if ($index == (int)$request->sort) {
                    if ($request->position == "above") {
                        Paragraph::where('id',$new_paragraph_id)->update(['sort'=>$index]);
                        $index++;
                        Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                    } else {
                        Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                        $index++;
                        Paragraph::where('id',$new_paragraph_id)->update(['sort'=>$index]);
                    }
                } else {
                    Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                }
    
                $index++;
            }
    
            return [
                'status'=>'success'
            ];
    
        } catch (\Exception $e) {
            return [
                'status'=>'error',
                'message'=>'Ошибка базы данных'
            ];
        }
    }

    public function deleteParagraph(Request $request) {

        $paragraph = Paragraph::where('id',$request->paragraph_id)->select('theme')->first();

        $theme_id = $paragraph->theme;

        try {
            $res = Paragraph::where('id',$request->paragraph_id)->delete();
            if (!$res) {
                return [
                    'status' => 'error',
                    'message' => "Paragraph was not deleted."
                ];
            }
        }catch(\Exception $e){
            return [
                'status' => 'exception',
                'message' => "Some exception.",
                // 'ecxeption' => $e
            ];
        }

        //Переделываем сортировку
        $paragraphs = Paragraph::where('theme',$theme_id)
                                ->select('id')
                                ->orderBy('sort','asc')
                                ->get();

        if (!$paragraphs) {
            return [
                'status'=>'error',
                'message'=>'Some thing went wrong.'
            ];
        }

        try {
            $index = 1;

            foreach ($paragraphs as $paragraph) {
                Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                $index++;
            }
        } catch (\Exception $e) {
            return [
                'status'=>'error',
                'message'=>'Ошибка базы данных'
            ];
        }

        return [
            'status' => 'success',
            'message' => "Paragraph was deleted."
        ];
    }

    public function saveParagraphs(Request $request) {
        //Валидация
        $paragraphs = $request->paragraphs;

        if (gettype($paragraphs)!='array') {
            return [
                'status'=>'badData'
            ];
        }

        for ($i=0;$i<count($paragraphs);$i++) {
            if (gettype((int)$paragraphs[$i]['id']) != 'integer' or (int)$paragraphs[$i]['id'] == 0 or !isset($paragraphs[$i]['content'])) {
                return [
                    'status'=>'badData'
                ];
            }
        }

        try {
            foreach($paragraphs as $paragraph) {
                $res = Paragraph::where('id',$paragraph['id'])->update(['content'=>$paragraph['content']]);
                if (!$res) {
                    return [
                        'status'=>'error',
                        'message'=>'Ошибка БД'
                    ];
                }
            }

            return [
                'status'=>'success'
            ];
        }catch(\Exception $e) {
            return [
                'status'=>'exception'
            ];
        }
    }

}
