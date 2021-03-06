<?php

namespace blacksenator\NameParser\Part;

class Nickname extends AbstractPart
{
    /**
     * camelcase the nickname for normalization
     *
     * @return string
     */
    public function normalize(): string
    {
        return $this->camelcase($this->getValue());
    }
}
