<?php

namespace Neat\Object;

abstract class Event
{
    public const STORING  = 'storing';
    public const STORED   = 'stored';
    public const LOADING  = 'loading';
    public const LOADED   = 'loaded';
    public const DELETING = 'deleting';
    public const DELETED  = 'deleted';
}
