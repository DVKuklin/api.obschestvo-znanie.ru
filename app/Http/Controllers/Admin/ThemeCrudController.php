<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ThemeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ThemeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ThemeCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Theme::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/theme');
        CRUD::setEntityNameStrings('тема', 'темы');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'name', // The db column name
            'label' => "название", // Table column heading
            'type' => 'text'
          ]);
        // CRUD::column('name')->label('название')->type('textarea');
        CRUD::column('url')->label('url');
        CRUD::column('sort')->label('сортировка');
        CRUD::addColumn([
            'name' => 'section',
            'label' => 'раздел',
            'type' => 'select',
            'entity' => 'sectionName',
            // 'model' => 'App\Models\Section',
            'attribute' => 'name'
        ]);
        // CRUD::column('image')->label('картинка')->type('image');
        $this->crud->addColumn([
            'name'=>'image',
            'label'=>'картинка',
            'type'=>'image',
            'prefix'=>'storage/'
        ]);
        CRUD::column('description')->label('описание');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        CRUD::column('name')->label('название')->type('textarea');
        CRUD::column('url')->label('url');
        CRUD::column('sort')->label('сортировка');
        CRUD::addColumn([
            'name' => 'section',
            'label' => 'раздел',
            'type' => 'select',
            'entity' => 'sectionName',
            // 'model' => 'App\Models\Section',
            'attribute' => 'name'
        ]);
        $this->crud->addColumn([
            'name'=>'image',
            'label'=>'картинка',
            'type'=>'image',
            'prefix'=>'storage/'
        ]);
        CRUD::column('description')->label('описание');

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
        CRUD::setValidation(ThemeRequest::class);

        CRUD::field('name')->label('название');
        CRUD::field('url');
        CRUD::field('sort')->label('сортировка');
        $this->crud->addField([
            'name' => 'section',
            'label' => 'раздел',
            'type' => 'select',
            'model' => 'App\Models\Section',
            'attribute' => 'name'
        ]);
        $this->crud->addField([
            'name'=>'image',
            'label'=>'картинка',
            'type'=>'upload',
            'upload'=> true,
            'disk'=>'public' 
        ]);
        // CRUD::field('image')->label('картинка');
        CRUD::field('description')->label('описание');

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
