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
        'description',
        'image_path'
    ];

    protected $appends = [
        'display_price'
    ];

    public function getDisplayPriceAttribute($value): string
    {
        return number_format($this->price, 2);
    }
}
