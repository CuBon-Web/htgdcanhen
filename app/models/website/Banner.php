<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = "banners";
    protected $fillable = ['id', 'image', 'status', 'link','title','description','subimg1','subimg2','subimg3','sublink1','sublink2','sublink3'];
    
}
