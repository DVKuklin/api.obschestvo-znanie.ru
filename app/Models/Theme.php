<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    // public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
        'sort',
        'section',
        'image',
        'description'
    ];

    public function setImageAttribute($value) {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "images/themes";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function sectionName() {
        return $this->belongsTo('App\Models\Section','section','id');
    }
}
