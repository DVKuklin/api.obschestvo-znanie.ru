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

        $data = [
            'status' => 'success',
            'section' => $section->name,
            'theme' => $theme->sort.". ".$theme->name,
            'paragraphs' => $paragraphs
        ];
        return $data;

    }
}
