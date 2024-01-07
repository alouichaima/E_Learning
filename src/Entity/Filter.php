<?php

namespace App\Entity;

class Filter

{
    #[ORM\Column(type: 'array')]
    public $categorie = [];

    #[ORM\Column(type: 'integer')]
    public $max;

    #[ORM\Column(type: 'integer')]
    public $min;

    #[ORM\Column(type: 'string', length: 255)]
    public $mot;
}