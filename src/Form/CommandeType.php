<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Twig\FiltreExtension;

class CommandeType extends AbstractType
{
    public function __construct(private FiltreExtension $filter){}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            
            // ->add('etat', ChoiceType::class, [
            //     'choices' => [
            //         'En cours de traitement' => 'En cours de traitement',
            //         'Envoyé' => 'Envoyé',
            //         'Livré' => 'Livré'
            //     ]
            // ])
            ->add('produit', EntityType::class, [ 'class' => Produit::class, 'choice_label' => function($produit){
                return $produit->getTitre() . ' - ' . $this->filter->deviseFR($produit->getPrix());
            }])

            ->add('montant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
