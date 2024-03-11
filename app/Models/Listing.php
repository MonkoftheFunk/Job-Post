<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['sinceCreated', 'logoUri', 'clicksCount'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getSinceCreatedAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    public function getLogoUriAttribute(): string
    {
        return asset('storage/' . basename($this->logo));
    }

    public function getClicksCountAttribute(): string
    {
        return $this->clicks()->count();
    }
}
