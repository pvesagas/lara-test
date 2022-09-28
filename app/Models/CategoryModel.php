<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryModel extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $primaryKey = 'id';

    protected $fillable = [
        'category',
    ];

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'category_no');
    }
}
