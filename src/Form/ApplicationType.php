<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
        /**
     * Fonction permettant de créer les configurations de base
     * 
     * @params string $label
     * 
     * @params string $placeholder
     * 
     * @params array $options
     * 
     * @return array
     */

    protected function getConfiguration($label, $placeholder, $options = []) {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
                ]
            ], $options);
    } 

}