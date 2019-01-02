<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 
                TextType::class, 
                $this->getConfiguration("Titre", "Tapez une superbe annonce"))
            ->add('slug', 
                TextType::class, 
                $this->getConfiguration("Adresse web", "TapeZ votre qdresse web générée(automatique)", [
                    'required' => false
                ])
                )
            ->add('coverImage', 
                UrlType::class, 
                $this->getConfiguration("URL Image", "Tapez l'url de votre image"))
            ->add('introduction', 
                TextType::class, 
                $this->getConfiguration("Introduction", "Présentez votre bien"))
            ->add('content',
                TextareaType::class, 
                $this->getConfiguration("Description", "Décrivez votre bien e manière détaillée"))
            ->add('rooms', 
                IntegerType::class, 
                $this->getConfiguration("Chambres", "Le nombre de chambres disponibles"))
            ->add('price', 
                MoneyType::class, 
                $this->getConfiguration("Prix", "Prix de la nuité"))
            ->add('images', 
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
