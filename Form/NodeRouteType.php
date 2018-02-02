<?php

namespace MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Form;

use MandarinMedien\MMAdminBundle\Form\ContainerAwareType;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use MandarinMedien\MMCmfContentBundle\Entity\RoutableNodeInterface;
use MandarinMedien\MMCmfRoutingBundle\Entity\ExternalNodeRoute;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfRoutingBundle\Entity\RedirectNodeRoute;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeRouteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $container = $options['container'];

        $builder
            ->add('route');


        if (get_class($options['data']) != ExternalNodeRoute::class) {
            $builder->add('node', EntityType::class, array(
                'class' => Page::class,
                'required' => true,
                'mapped' => false
            ));
        }

        if (get_class($options['data']) == RedirectNodeRoute::class) {
            $builder->add('statusCode', ChoiceType::class, array(
                'required' => true,
                'choices' => array(
                    '301' => 301,
                    '302' => 302
                )
            ));
        }


        $builder
            ->add('submit', SubmitType::class, array('label' => 'save'))
            ->add('save_and_add', SubmitType::class, array(
                'attr' => array(
                    'data-target' => $container->get('router')->generate('mm_cmf_admin_routing_addon_noderoute_new') // for later xhr usage
                ),
            ))
            ->add('save_and_back', SubmitType::class, array(
                'attr' => array(
                    'data-target' => $container->get('router')->generate('mm_cmf_admin_routing_addon_noderoute') // for later xhr usage
                )
            ));
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


    public function getParent()
    {
        return ContainerAwareType::class;
    }
}
