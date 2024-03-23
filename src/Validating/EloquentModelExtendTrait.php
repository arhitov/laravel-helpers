<?php

namespace Arhitov\Helpers\Validating;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait EloquentModelExtendTrait
{
    /**
     * Extend rules
     * in:class:ClassNameEnum to new Enum(ClassNameEnum)
     * unique to Rule::unique('connect.database.table') or Rule::unique('connect.database.table')->ignore(123, 'id')
     * @return array
     */
    public function getRules(): array
    {
        $rules = $this->rules;

        foreach ($rules as $fieldName => $ruleFiled) {
            foreach ($ruleFiled as $ruleItemIdx => $ruleItem) {
                $rules[$fieldName][$ruleItemIdx] = match (true) {
                    (is_string($ruleItem) && str_starts_with($ruleItem, 'in:class:')) => new Enum(substr($ruleItem, strlen('in:class:'))),
                    'unique' === $ruleItem => (function() {
                        $uniqueTable = "{$this->getConnection()->getName()}.{$this->getConnection()->getDatabaseName()}.{$this->getTable()}";
                        $primaryKey = $this->getKeyName();
                        return $this->exists
                            ? Rule::unique($uniqueTable)->ignore($this->{$primaryKey}, $primaryKey)
                            : Rule::unique($uniqueTable);
                    })(),
                    default => $ruleItem,
                };
            }
        }

        return $rules;
    }
}
