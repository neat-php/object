<?php

namespace Neat\Object\Exception;

use LogicException;

class LayerNotFoundException extends LogicException
{
    public function __construct(string $class)
    {
        parent::__construct("Layer: '$class' not found");
    }
}
