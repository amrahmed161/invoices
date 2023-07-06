<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Products extends Model
{
    use HasFactory;
    protected $guarded = [];

    // protected $fillable = [
    //     'products_name',
    //     'section_id',
    //     'description',
    // ];
    /**
 * Get the post that owns the comment.
 */

public function sections(): BelongsTo
{
    return $this->belongsTo(Sections::class,'section_id');
}

}
