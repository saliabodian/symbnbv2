<?php

namespace App\Form\DataTransformer ;

use Symfony\Component\Form\DataTransformerInterface;
use PhpParser\Node\Stmt\Throw_;
use Symfony\Component\Form\Exception\TransformationFailedException;


class FrenchToDateTimeTransformer implements DataTransformerInterface {

    public function transform($date){
        if($date === null ){
            return '';
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate){
        if($frenchDate === null ){
            // Exception
            throw new TransformationFailedException("Vous devez fournir une date !");
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date === false){
            throw new TransformationFailedException("Le format de la date fournie n'est pas correcte !");
        }

        return $date;
    }
}