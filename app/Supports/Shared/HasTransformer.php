<?php

namespace App\Supports\Shared;

trait HasTransformer
{
    public function transform($transformer = null)
    {
        $transformer = $transformer ? $transformer : new $this->transformer;

        return $transformer->transform($this);
    }
}
