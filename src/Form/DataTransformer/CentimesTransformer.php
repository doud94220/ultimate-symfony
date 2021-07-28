<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface
{
    public function transform($value) //avant soumission du form
    {
        if ($value === null) {
            return;
        }

        return $value / 100;
    }

    public function reverseTransform($value) //après soumission du form
    {
        if ($value === null) {
            return;
        }

        return $value * 100;
    }
}
