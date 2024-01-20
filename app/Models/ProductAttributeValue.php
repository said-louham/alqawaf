<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected  $fillable=[
        'product_id',
        'attribute_id',
        'attribute_value_id',
        'price',
    ];



    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class,'attribute_id');
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValues::class,'attribute_value_id');
    }

}
