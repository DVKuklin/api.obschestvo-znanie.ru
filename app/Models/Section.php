<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'sort',
        'image',
        'imagehover',
        'description'
    ];
    public function setImageAttribute($value) {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "images/sections";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setImagehoverAttribute($value) {
        $attribute_name = "imagehover";
        $disk = "public";
        $destination_path = "images/sections";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
