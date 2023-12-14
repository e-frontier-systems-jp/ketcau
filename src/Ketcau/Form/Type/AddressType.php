<?php

namespace Ketcau\Form\Type;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\Form\AbstractType;

class AddressType extends AbstractType
{
    public function __construct(
        public KetcauConfig $ketcauConfig
    ){}



}