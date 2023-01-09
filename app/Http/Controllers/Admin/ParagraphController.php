<?php

namespace App\Http\Controllers\Admin;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


use App\Http\Controllers\PagesController;

class ParagraphController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup() {
        $this->crud->setModel(\App\Models\Paragraph::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/myparagraph');
        $this->crud->setEntityNameStrings('абзац', 'абзацы');

        // $paragraps = $this->getParagraphs();

        $this->crud->setListView('custom_admin.paragraphs-edit');

        $this->crud->setColumns([
            [
                'name'=>'content',
                'label'=>'контент',
                'type'=>'text'
                // 'type'=>'closure',
                // 'function'=> function($entry) use($paragraps) {
                //     if($entry->content == 0) return "-";

                //     return $paragraps[$entry->content];
                // }
            ]
        ]);

        $this->crud->addFields([
            [
                'name'=>'content',
                'label'=>'контент',
                'type'=>'textarea'
            ]
        ]);
    }

    public function setupListOperation()
    {
        // $this->crud->addButton('top', 'create', 'view', 'crud::buttons.create');
        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButtonFromView('line', 'clone', 'clone', 'beginning');
          });
        
    }

    protected function setupCloneRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/clone', [
            'as'        => $routeName.'.clone',
            'uses'      => $controller.'@clone',
            'operation' => 'clone',
        ]);
    }

    public function clone($id) 
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('Clone');

        $clonedEntry = $this->crud->model->findOrFail($id)->replicate();

        return (string) $clonedEntry->push();
    }


    protected function setupCloneDefaults() {
        $this->crud->allowAccess('clone');
      
        $this->crud->operation(['list', 'show'], function () {
          $this->crud->addButtonFromView('line', 'clone', 'clone', 'beginning');
        });
      }
    // private function getParagraphs() {
    //     $p = new PagesController;
    //     $res = $p->getParagraphsBySectionAndThemeUrl([
    //         'section_url'=>'section_1',
    //         'theme_url'=>'them_01'
    //     ]);

    //     return $res;
    // }
}
