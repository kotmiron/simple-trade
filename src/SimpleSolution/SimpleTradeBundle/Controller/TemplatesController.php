<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Entity\Templates;
use SimpleSolution\SimpleTradeBundle\Form\templates\TemplatesType;
use SimpleSolution\SimpleTradeBundle\Form\templates\TemplatesEditForm;
use SimpleSolution\SimpleTradeBundle\Model\TemplatesModel;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

/**
 * Templates controller.
 *
 * @Route("/templates")
 */
class TemplatesController extends Controller
{

    /**
     * Lists all Templates entities.
     *
     * @Route("/", name="templates")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SimpleTradeBundle:Templates')->findAll();

        return array(
            'entities' => $entities,
            'create_action' => count($entities) < Constants::MAIL_TOTAL,
        );
    }

    /**
     * Finds and displays a Templates entity.
     *
     * @Route("/{id}/show", name="templates_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SimpleTradeBundle:Templates')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Templates entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Templates entity.
     *
     * @Route("/new", name="templates_new")
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $created = array( );
        $entities = $em->getRepository('SimpleTradeBundle:Templates')->findAll();
        foreach( $entities as $entity ) {
            $created[ $entity->getName() ] = $entity->getName();
        }

        $entity = new Templates();
        $form = $this->createForm(new TemplatesType($created), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Templates entity.
     *
     * @Route("/create", name="templates_create")
     * @Method("POST")
     * @Template("SimpleTradeBundle:Templates:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Templates();
        $form = $this->createForm(new TemplatesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('templates_show', array( 'id' => $entity->getId() )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Templates entity.
     *
     * @Route("/{id}/edit", name="templates_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SimpleTradeBundle:Templates')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Templates entity.');
        }

        $editForm = $this->createForm(new TemplatesEditForm(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Templates entity.
     *
     * @Route("/{id}/update", name="templates_update")
     * @Method("POST")
     * @Template("SimpleTradeBundle:Templates:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SimpleTradeBundle:Templates')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Templates entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TemplatesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('templates_edit', array( 'id' => $id )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Templates entity.
     *
     * @Route("/{id}/delete", name="templates_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SimpleTradeBundle:Templates')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Templates entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('Templates'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array( 'id' => $id ))
                ->add('id', 'hidden')
                ->getForm()
        ;
    }

}
