<?php

namespace Neat\Object;

abstract class Event
{
    const STORING  = 'storing';
    const STORED   = 'stored';
    const LOADING  = 'loading';
    const LOADED   = 'loaded';
    const DELETING = 'deleting';
    const DELETED  = 'deleted';
}
