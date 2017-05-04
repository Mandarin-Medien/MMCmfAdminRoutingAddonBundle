<?php

namespace MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Form\Types;

use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeRouteInlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('route');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => NodeRoute::class
        ));
    }
}