<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;

class UserExtendedController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup() {
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user_extended');
        $this->crud->setEntityNameStrings('Расширенная панель пользователей', 'РАСШИРЕННАЯ ПАНЕЛЬ УПРАВЛЕНИЯ ПОЛЬЗОВАТЕЛЯМИ');
        $this->crud->setListView('custom_admin.user-extended');

    }
}
