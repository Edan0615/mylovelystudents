<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'analysis',
        'foods',
        'total_calories',
    ];

    protected $casts = [
        'foods' => 'array',
        'total_calories' => 'decimal:2',
    ];

    /**
     * 取得擁有此分析的使用者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
