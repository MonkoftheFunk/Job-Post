<?php

namespace App\Models;

use App\Observers\IngestInterface;
use App\Observers\MongoIngestObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model implements IngestInterface
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['sinceCreated', 'logoUri', 'clicksCount', 'tagsCSV'];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted(): void
    {
        self::observe(MongoIngestObserver::class);
    }

    /**
     * Load Model by 'slug' attribute in route
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return HasMany
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return string
     */
    public function getSinceCreatedAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * @return string
     */
    public function getLogoUriAttribute(): string
    {
        return asset('storage/' . basename($this->logo));
    }

    /**
     * @return string
     */
    public function getClicksCountAttribute(): string
    {
        return $this->clicks()->count();
    }

    /**
     * @return string
     */
    public function getTagsCSVAttribute(): string
    {
        return implode(',', array_map(static function ($t) {
            return $t->name;
        }, $this->tags()->get()->all()));
    }

    /**
     * IngestInterface
     *
     * @return array
     */
    public function getIngest(): array
    {
        return $this->toArray();
    }
}
