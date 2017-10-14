<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentFormFactory
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;        
    }

    public function buildWithAction(string $action): FormInterface
    {
        return $this->factory->createBuilder()
            ->setAction($action)
            ->add('credit_card', IntegerType::class)
            ->add('channel', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => 'Pay'])
            ->getForm();
    }
}
