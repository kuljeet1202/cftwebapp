<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebSeoPages extends Model
{
    use HasFactory;

    protected $table = 'tbl_web_seo_pages';

    protected $fillable = ['meta_title', 'meta_keyword', 'meta_description', 'schema_markup', 'og_image', 'page_type'];

    public function getOgImageAttribute($image)
    {
        if (!empty($image) && strpos($image, 'web_seo_pages/') === false) {
            $image = 'web_seo_pages/' . $image;
        }

        return $image && Storage::disk('public')->exists($image) ? url(Storage::url('/' . $image)) : '';
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($image) {
            // before delete() method call this
            if (!is_null($image->og_image) && Storage::disk('public')->exists($image->getRawOriginal('og_image'))) {
                Storage::disk('public')->delete($image->getRawOriginal('og_image'));
            }
        });
    }
}
