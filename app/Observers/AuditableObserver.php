<?php

namespace App\Observers;

use App\Services\AuditLogService;

class AuditableObserver
{
    public function created(object $model): void
    {
        AuditLogService::log(
            action: 'create',
            entityType: AuditLogService::entityTypeFromModel($model),
            entityId: $model->getKey(),
            new: $model->getAttributes(),
        );
    }

    public function updated(object $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = array_intersect_key($model->getRawOriginal(), $changes);

        AuditLogService::log(
            action: 'update',
            entityType: AuditLogService::entityTypeFromModel($model),
            entityId: $model->getKey(),
            old: $original,
            new: $changes,
        );
    }

    public function deleted(object $model): void
    {
        AuditLogService::log(
            action: 'delete',
            entityType: AuditLogService::entityTypeFromModel($model),
            entityId: $model->getKey(),
            old: $model->getAttributes(),
        );
    }
}
