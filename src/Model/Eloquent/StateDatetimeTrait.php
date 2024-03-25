<?php

namespace Arhitov\Helpers\Model\Eloquent;

use Carbon\Carbon;

/**
 * Sets a datetime for a state change.
 * The model must contain:
 * Field "state" of enum type;
 * Field "casts" of array type;
 * Timestamp fields of the format "{value}_at" format "datetime";
 * The "casts" array contains keys of the format "{value}_at" with the specified type "datetime".
 */
trait StateDatetimeTrait
{
    public static function bootStateDatetimeTrait(): void
    {
        self::creating(function (self $operation) {
            $operation->setStateDatetime();
        });
        self::updating(function (self $operation) {
            $operation->setStateDatetime();
        });
    }

    private function setStateDatetime(): void
    {
        $datetimeKey = ($this->state?->value ?? '') . '_at';
        if (($this->casts[$datetimeKey] ?? null) === 'datetime') {
            $this->$datetimeKey = Carbon::now();
        }
    }
}
