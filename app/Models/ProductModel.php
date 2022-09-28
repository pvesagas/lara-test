<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $primaryKey = 'id';

    protected $fillable = [
        'category_no',
        'price',
        'name',
        'image_path'
    ];

    protected $appends = [
        'display_price',
        'category_name'
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    /**
     * @return string
     */
    public function getDisplayPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * @return string
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->hasOne(CategoryModel::class, 'id', 'category_no')->first()->category;
    }
}
