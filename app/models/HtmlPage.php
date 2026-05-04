<?php

namespace App\models;

use App\Customer;
use Illuminate\Database\Eloquent\Model;

class HtmlPage extends Model
{
    protected $table = 'html_pages';

    protected $fillable = [
        'title',
        'slug',
        'content_html',
        'status',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(Customer::class, 'created_by');
    }
}

