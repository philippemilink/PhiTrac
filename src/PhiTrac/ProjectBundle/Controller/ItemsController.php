<?php

namespace PhiTrac\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use PhiTrac\ProjectBundle\Entity\Project;
use PhiTrac\ProjectBundle\Entity\Item;
use PhiTrac\ProjectBundle\Form\ItemType;
use PhiTrac\ProjectBundle\Form\ItemStatusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ItemsController extends Controller
{
    /*
    * Add a new item to a project.
    */ 
	public function addAction(Project $project)
    {
        $request = $this->get('request');
        
		$item = new Item();
		$item->setProject($project);
		if ($request->query->get('type')!=null) {
		    if ($request->query->get('type')=='BUG') {
		        $item->setType('BUG');
	        }
	        if ($request->query->get('type')=='TODO') {
		        $item->setType('TODO');
	        }
        }
		
		$form = $this->createForm(new ItemType, $item);
		
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();
		        $em->persist($item);		        
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
	        }
        }        

        return $this->render('PhiTracProjectBundle:Items:add.html.twig', array('form' => $form->createView(), 'project' => $project));
    }
    
    /**
    * Show an item
    * 
    * POST method: update the status of the item and redirect to the homepage of the project
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("item", options={"mapping": {"item_slug": "slug"}})
    */
    public function showAction(Project $project, Item $item) 
    {
        $request = $this->get('request');
        $form = $this->createForm(new ItemStatusType, $item);
        
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();
                $em->persist($project);
                $em->flush();
                
                return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
            }
        }
        
		return $this->render('PhiTracProjectBundle:Items:show.html.twig', array('project' => $project, 'item' => $item, 'form' => $form->createView()));
    }
    
    /**
    * Edit an item
    * 
    * POST method: edit the item and redirect to the item page (show)
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("item", options={"mapping": {"item_slug": "slug"}})
    */
	public function editAction(Project $project, Item $item) 
    {
        $form = $this->createForm(new ItemType, $item);
		
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();
		        $em->persist($item);
		        $em->flush();
		        $em->persist($project);
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('show_item', array('project_slug' => $project->getSlug(), 'item_slug' => $item->getSlug())));
	        }
        }        

        return $this->render('PhiTracProjectBundle:Items:edit.html.twig', array('project' => $project, 'form' => $form->createView(), 'item' => $item));
    }
    
    /**
    * Delete an item
    * 
    * POST method: delete the item and redirect to the homepage of the project
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("item", options={"mapping": {"item_slug": "slug"}})
    */ 
	public function deleteAction(Project $project, Item $item)
    {
        $form = $this->createFormBuilder()->getForm();
        
        if ($this->get('request')->getMethod()=='POST') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();
            return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
        }
        
		return $this->render('PhiTracProjectBundle:Items:delete.html.twig', array('project' => $project, 'form' => $form->createView(), 'item' => $item));
    }
    
    /**
    * Close an item: set status at DONE.
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("item", options={"mapping": {"item_slug": "slug"}})
    */ 
	public function closeAction(Project $project, Item $item)
    {        
        $item->setStatus("DONE");
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();
        $em->persist($project);
        $em->flush();

        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
    }
    
    /**
    * Reopen an item: set status at TODO.
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("item", options={"mapping": {"item_slug": "slug"}})
    */ 
	public function reopenAction(Project $project, Item $item)
    {        
        $item->setStatus("TODO");
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();
        $em->persist($project);
        $em->flush();

        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
    }
    
    /*
    * Generate the view of the list of items in the home of a project.
    * Used in home_project.html.twig TWIG template.
    */
    public function getItemsDashboardAction(Project $project)
    {
        return $this->render('PhiTracProjectBundle:Items:items_dashboard.html.twig', array('project' => $project));
    }
}
