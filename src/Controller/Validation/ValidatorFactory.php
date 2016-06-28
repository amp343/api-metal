<?php

namespace ApiMetal\Controller\Validation;

use Respect\Validation\Validator as v;

class ValidatorFactory
{
    /**
     * Given a shorthand validation type string,
     * return the associated Respect validator
     *
     * @param  string $type The shorthand validator name, ie, 'latitude'
     * @return array        A kv array having keys: $validator, describing the
     *                      associated Respect validator, and $errors, describing
     *                      any custom errors that should be used during validation
     */
    public static function getValidatorByType(string $type = null): array
    {
        $rval = ['validator' => null, 'errors' => null];

        if (!isset($type)) {
            return $rval;
        }

        switch ($type) {
            case 'string':
                $rval['validator'] = v::stringType();
                break;

            case 'integer':
                $rval['validator'] = v::intVal();
                break;

            case 'int':
                $rval['validator'] = v::intVal();
                break;

            case 'latitude':
                $rval['validator'] = v::numeric()->min(-90, true)->max(90, true);
               break;

            case 'longitude':
                $rval['validator'] = v::numeric()->min(-180, true)->max(180, true);
                break;

            case 'phone':
                $rval['validator'] = v::phone();
                break;

            case 'flag':
                $rval['validator'] = v::numeric()->min(0, true)->max(1, true);
                break;

            case 'positiveNonZeroInt':
                $rval['validator'] = v::intVal()->positive();
                break;

            case 'nonNegativeInt':
                $rval['validator'] = v::intVal()->min(0, true);
                break;

            case 'nonZeroInt':
                $rval['validator'] = v::intVal()->oneOf(
                    v::positive(),
                    v::negative()
                );
                $rval['errors'] = [
                    'positive' => '{{name}} must be a non-zero integer',
                    'negative' => '{{name}} must be a non-zero integer',
                ];
                break;

            case 'Y-m-d':
                $rval['validator'] = v::date('Y-m-d');
                break;
        }

        // consider value limits
        // if (isset($rules['minVal'])) {
        //     $rval['validator']->min($validate['minVal'], true);
        // }
        // if (isset($rules['maxVal'])) {
        //     $rval['validator']->max($validate['maxVal'], true);
        // }

        return $rval;
    }
}
