<?php

namespace App\Http\Controllers\Admin;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Backpack\CRUD\app\Library\Widget;

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

        $this->crud->setListView('custom_admin.paragraphs-edit');

        Widget::add()->type('style')->content('admin_assets/css/paragraphs-edit.css');
     }
}
