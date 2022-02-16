<?php

namespace mhunesi\cargo\enums;


class CargoCompany
{
    public const UPS = "ups";

    public const HEPSIJET = "hepsijet";

    public const ARAS = "aras";

    public function __get($name)
    {
        throw new \Exception("{$name} provider not found!");
    }
}