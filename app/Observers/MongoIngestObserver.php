<?php

namespace App\Observers;

use MongoDB\Laravel\Eloquent\Model;

class MongoIngestObserver
{
    /**
     * @param IngestInterface $ingest
     * @return Model
     * @throws \Exception
     */
    protected static function resolve(IngestInterface $ingest): Model
    {
        $class_name = get_class($ingest);
        $mongo_model = 'App\Models\Mongo' . $$class_name;
        if (!class_exists($mongo_model)) {
            throw new \RuntimeException('Mongo Model doesn\'t exist for model ' . $class_name);
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
            // todo log
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
