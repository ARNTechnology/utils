<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Utils\Iterator;

class ExpectingIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;
    
    /**
     * @var bool
     */
    private $wasValid = false;

    /**
     * ExpectingIterator constructor.
     * @param \Iterator $inner
     */
    public function __construct(\Iterator $inner)
    {
        $this->inner = $inner;
    }
    
    public function next()
    {
        if (!$this->wasValid && $this->valid()) {
            // Just do nothing, because the inner iterator has became valid
        } else {
            $this->inner->next();
        }
        $this->wasValid = $this->valid();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->inner->current();
    }

    /**
     *
     */
    public function rewind()
    {
        $this->inner->rewind();
        $this->wasValid = $this->valid();
    }

    /**
     * @return bool|float|int|string|null
     */
    public function key()
    {
        return $this->inner->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->inner->valid();
    }
}
