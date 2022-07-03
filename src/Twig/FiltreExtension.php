<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class FiltreExtension extends AbstractExtension{

    public function getFilters(){
        return [

            new TwigFilter("deviseFr", [$this, "deviseFr"]),
            new TwigFilter("role" , [$this, "role"]),
        ];
            
    }

    public function deviseFr(float $prix) :string{

       return number_format($prix, 2, "," , " ") . " €";
    }

    public function role(array $roles) : string{

        if(isset($roles[0])){
            return strtolower(str_replace("ROLE_" , "", $roles[0]));
        }
        return "";
    }
}