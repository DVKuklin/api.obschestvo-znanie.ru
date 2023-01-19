<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ParagraphRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class ParagraphCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ParagraphCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Paragraph::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/paragraph');
        CRUD::setEntityNameStrings('абзац', 'абзацы');

        
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        Widget::add()->type('style')->content('admin/css/paragraphs-listOperation-style.css');
        // CRUD::column('content')->label('контент')->type('textarea')->escaped('true');
        
        $this->crud->addColumn([
            'name'=>'content',
            'label'=>'контент',
            'type'=>'textarea',
            'limit'  => 120,
            'escaped' => false
        ]);        
        $this->crud->addColumn([
            'name'=>'theme',
            'label'=>'Тема',
            // 'type'=>'text'
            'type'=>'select',
            'entity'=>'themeName',
            'model'  => 'App\Models\Theme',
            'attribute' => 'name'
        ]);
        CRUD::column('sort')->label('сортировка');


        // CRUD::column('theme')->label('тема');


        // Widget::add()->type('script')->content('assets/js/paragraph.js');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        $this->crud->addColumn([
            'name'=>'theme',
            'label'=>'Тема',
            // 'type'=>'text'
            'type'=>'select',
            'entity'=>'themeName',
            'model'  => 'App\Models\Theme',
            'attribute' => 'name'
        ]);
        CRUD::column('sort')->label('сортировка');
        $this->crud->addColumn([
            'name'=>'content',
            'label'=>'контент',
            'type'=>'textarea',
            'limit'  => 120,
            'escaped' => false
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }
    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ParagraphRequest::class);

        CRUD::field('theme');
        $this->crud->addField([
            'name' => 'theme',
            'label' => 'тема',
            'type' => 'select',
            'model' => 'App\Models\Theme',
            'attribute' => 'name'
        ]);

        CRUD::field('sort');
        CRUD::field('content')->label('контент')->type('summernote');


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
