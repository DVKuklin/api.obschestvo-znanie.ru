<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $fillable = [
        'content',
        'theme',
        'sort',
    ];

    public function themeName() {
        return $this->belongsTo('App\Models\Theme','theme','id');
    }

    protected $table = 'paragraphs';

}
