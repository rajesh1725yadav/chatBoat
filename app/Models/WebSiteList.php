<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSiteList extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable =['id','website','website_url','bot_name','massage', 'category_id','status','web_site_code'];
}
