<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\ArrayConversion;
use Neat\Object\Relations;
use Neat\Object\Storage;

abstract class Entity
{
    use Storage;
    use ArrayConversion;
    use Relations;
}
