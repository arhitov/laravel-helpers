<?php

namespace Arhitov\Helpers\Model\Eloquent;

use Carbon\Carbon;
use ErrorException;

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

    public function setState($state): void
    {
        $stateClass = get_class($this->state);
        if (is_string($state)) {
            if ($state = $stateClass::tryFrom($state)) {
                $this->state = $state;
                $this->setStateDatetime(true);
            } else {
                throw new ErrorException('The name was not found in the state class enumeration.');
            }
        } elseif (get_class($state) === $stateClass) {
            $this->state = $state;
            $this->setStateDatetime(true);
        } else {
            throw new ErrorException('Undescribed behavior.');
        }
    }

    private function setStateDatetime(bool $forcibly = false): void
    {
        if ($forcibly || ! $this->exists || $this->state !== $this->getOriginal('state')) {
            $datetimeKey = 'state_' . ($this->state?->value ?? '') . '_at';
            if (($this->casts[$datetimeKey] ?? null) === 'datetime') {
                $this->$datetimeKey = Carbon::now();
            }
        }
    }
}
