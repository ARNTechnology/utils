<?php


namespace ARNTech\Utils\Iterator;


class MapIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;

    /**
     * MapIterator constructor.
     * @param \Iterator $inner
     */
    public function __construct(\Iterator $inner)
    {
        $this->inner = $inner;
    }

    /**
     *
     */
    public function next()
    {
        $this->inner->next();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->inner->current();
    }
    
    public function rewind()
    {
        $this->inner->rewind();
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
