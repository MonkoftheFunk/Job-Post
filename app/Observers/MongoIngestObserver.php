<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class MongoIngestObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * @param IngestInterface $ingest
     * @return Model
     * @throws \Exception
     */
    protected static function resolve(IngestInterface $ingest): Model
    {
        $reflect = new \ReflectionClass($ingest);
        $class_name = $reflect->getShortName();
        $mongo_model = 'App\Models\Mongo\\' . $class_name;
        if (!class_exists($mongo_model)) {
            throw new \Exception('Mongo Model doesn\'t exist for model ' . $class_name);
        }

        return new $mongo_model();
    }

    /**
     * Handle the Model "created" event.
     *
     * @throws \Exception
     */
    public function created(IngestInterface $ingest): void
    {
        $model = self::resolve($ingest);
        $model->setRawAttributes($ingest->getAttributes());
        if (!$model->save()) {
            Log::error('Failed to save ingest. ID: ' . $ingest->id . ' Class: ' . $model::class);
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(IngestInterface $ingest): void
    {
        //
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(IngestInterface $ingest): void
    {
        //
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(IngestInterface $ingest): void
    {
        //
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(IngestInterface $ingest): void
    {
        //
    }
}
