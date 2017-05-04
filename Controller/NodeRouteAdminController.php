<?php

namespace MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Controller;

use MandarinMedien\MMCmf\Admin\PageAddonBundle\Form\PageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Form\Types\NodeRouteInlineType;
use MandarinMedien\MMCmfContentBundle\Entity\Page;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Form\NodeRouteType;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use MandarinMedien\MMAdminBundle\Controller\BaseController;

/**
 * Class NodeRouteAdminController
 * @package MandarinMedien\MMCmf\Admin\RoutingAddonBundle\Controller
 */
class NodeRouteAdminController extends BaseController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(NodeRoute::class)->findAll();

        return $this->render("MMCmfAdminRoutingAddonBundle:NodeRoute:list.html.twig", array(
            'noderoutes' => $entities,
            'factory' => $this->get('mm_cmf_routing.node_route_factory'),
            'types' => $this->get('mm_cmf_routing.node_route_factory')->getDiscriminators()
        ));
    }


    /**
     * @param Request $request
     * @param $node_route_type
     * @return mixed
     */
    public function newAction(Request $request, $node_route_type)
    {

        $page = null;

        if($page_id = (int) $request->get('page'))
        {
            $page = $this->getDoctrine()->getRepository(Page::class)->find($page_id);
        }

        $factory = $this->get('mm_cmf_routing.node_route_factory');

        $entity = $factory->createNodeRoute($node_route_type);
        $entity->setNode($page);

        if ($entity) {

            $form = $this->createCreateForm($entity);

            return $this->render('MMCmfAdminRoutingAddonBundle:NodeRoute:new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView())
            );
        }
    }


    /**
     * @param Request $request
     * @param $node_route_type
     * @return \MandarinMedien\MMAdminBundle\Controller\JsonFormResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, $node_route_type)
    {
        $factory = $this->get('mm_cmf_routing.node_route_factory');

        $entity = $factory->createNodeRoute($node_route_type);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        return $this->formResponse($form);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function editAction($id)
    {

        $nodeRoute = $this->getDoctrine()->getRepository(NodeRoute::class)->find($id);

        return $this->render("MMCmfAdminRoutingAddonBundle:NodeRoute:edit.html.twig", array(
            'form' => $this->createEditForm($nodeRoute)->createView(),
            'nodeRoute' => $nodeRoute));
    }


    /**
     * @param NodeRoute $nodeRoute
     * @return \MandarinMedien\MMAdminBundle\Controller\JsonFormResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(NodeRoute $nodeRoute)
    {

        $form = $this->createEditForm($nodeRoute);
        $form->handleRequest($this->get('request_stack')->getCurrentRequest());

        //var_dump($this->get('request'));die();

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->formResponse($form);
    }


    /**
     * @param NodeRoute $nodeRoute
     * @return \Symfony\Component\Form\Form
     */
    public function createEditForm(NodeRoute $nodeRoute)
    {
        return $this->createForm( NodeRouteType::class, $nodeRoute, array(
            'method' => 'PUT',
            'attr' => array(
                'rel' => 'ajax'
            ),
            'action' => $this->generateUrl('mm_cmf_admin_routing_addon_noderoute_update', array(
                'id' => $nodeRoute->getId()
            ))
        ));
    }

    /**
     * @param NodeRouteInterface $nodeRoute
     * @return \Symfony\Component\Form\Form
     */
    public function createCreateForm(NodeRouteInterface $nodeRoute)
    {
        return $this->createForm(NodeRouteType::class, $nodeRoute, array(
            'method' => 'POST',
            'action' => $this->generateUrl('mm_cmf_admin_routing_addon_noderoute_create', array(
                'node_route_type' => $this->get('mm_cmf_routing.node_route_factory')
                    ->getDiscriminatorByClass($nodeRoute)
            ))
        ));
    }


    /**
     * @param $node_route_type
     * @param Page $page
     * @return JsonResponse
     */
    public function getInlineFormAction($node_route_type, Page $page)
    {

        $factory = $this->container->get('mm_cmf_routing.node_route_factory');
        $nodeRoute = $factory->createNodeRoute($node_route_type);

        $form = $this->createForm(NodeRouteInlineType::class, $nodeRoute);
        $parent = $this->createForm(PageType::class, $page);


        return new JsonResponse(
            array(
                'content' => $this->renderView('@MMCmfAdmin/Admin/Page/page.route.html.twig', array(
                    'form' => $form->createView($parent->createView())
                ))
            )
        );
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {

        //if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(NodeRoute::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }


        $em->remove($entity);
        $em->flush();
        //}

        return $this->redirectToRoute('mm_cmf_admin_routing_addon_noderoute');
    }

}
