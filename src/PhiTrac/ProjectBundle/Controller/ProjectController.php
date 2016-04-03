<?php

namespace PhiTrac\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use PhiTrac\ProjectBundle\Entity\Project;
use PhiTrac\ProjectBundle\Entity\Item;
use PhiTrac\ProjectBundle\Entity\Image;
use PhiTrac\ProjectBundle\Form\ProjectType;
use PhiTrac\ProjectBundle\Form\ProjectEditType;
use PhiTrac\ProjectBundle\Form\ImageType;

class ProjectController extends Controller
{
    /*
    * Home of application
    * slug no specified: ask to select or create a project
    * slug specified: show the project dashboard
    */
    public function homeAction($slug) 
    {        
		if ($slug===0) { // No project selected
        	return $this->render('PhiTracProjectBundle:Project:home_empty.html.twig');
		} else { // A project is selected
		    $repository = $this->getDoctrine()->getManager()->getRepository('PhiTracProjectBundle:Project');
            $project = $repository->findOneBySlug($slug);

            $allowed = false;
            if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $allowed = true;
            } else {
                foreach ($project->getMembers() as $member) {
                    if ($member===$this->getUser()) {
                        $allowed = true;
                    }
                }
            }
            if (!$allowed) {
                throw new AccessDeniedException('Access denied');
            }
            
            if ($project===null) {
                throw $this->createNotFoundException('Project not found.');
            }
            
			return $this->render('PhiTracProjectBundle:Project:home_project.html.twig', array('project' => $project));
		}
    }
    
    /**
    * Create a new project
    */ 
	public function addAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
		$project = new Project($this->getUser());
		
		$form = $this->createForm(new ProjectType, $project);
		
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();
		        $em->persist($project);
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
	        }
        }        

        return $this->render('PhiTracProjectBundle:Project:add.html.twig', array('form' => $form->createView()));
    }
    
    /**
    * Edit a project
    * GET method: show the form to edit the project
    * POST method: edit the project and redirect to the project homepage
    */
	public function editAction(Project $project) 
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
        $form = $this->createForm(new ProjectEditType, $project);
		
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();
		        $em->persist($project);
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
	        }
        }        

        return $this->render('PhiTracProjectBundle:Project:edit.html.twig', array('project' => $project, 'form' => $form->createView()));
    }

    /**
    * Delete a project
    * GET method: ask confirmation to delete the project
    * POST method: delete the project and redirect to the empty homepage
    */ 
	public function deleteAction(Project $project)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
        $form = $this->createFormBuilder()->getForm();
        
        if ($this->get('request')->getMethod()=='POST') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
            return $this->redirect($this->generateUrl('home_project'));
        }
        
		return $this->render('PhiTracProjectBundle:Project:delete.html.twig', array('project' => $project, 'form' => $form->createView()));
    }
    
    /**
    * Set icon of a project
    * GET method: show the form to set the icon
    * POST method: edit the project and create the icon entity
    */
	public function setIconAction(Project $project) 
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
        $icon = new Image();

        $form = $this->createForm(new ImageType, $icon);
		
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();
		        
		        if ($project->getIcon()===null) { // No icon before
		            $project->setIcon($icon);
		        } else { // An icon existed before
		            $oldIcon = $project->getIcon();
		            $project->setIcon($icon);
		            $em->remove($oldIcon);
		        }
		        $em->persist($icon);
		        $em->persist($project);
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
	        }
        }        

        return $this->render('PhiTracProjectBundle:Project:set_icon.html.twig', array('project' => $project, 'form' => $form->createView()));
    }
    
    /**
    * Delete this icon of a project
    * GET method: ask confirmation to delete the icon
    * POST method: delete the icon and redirect to the project homepage
    */ 
	public function deleteIconAction(Project $project)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
        if ($project->getIcon()===null) {
            throw $this->createNotFoundException('This project has not an icon.');
        }
        
        $form = $this->createFormBuilder()->getForm();
        
        if ($this->get('request')->getMethod()=='POST') {
            $em = $this->getDoctrine()->getManager();
            
            $em->remove($project->getIcon());
            
            $project->setIcon(null);
            $em->persist($project);
            
            $em->flush();
            
            return $this->redirect($this->generateUrl('home_project', array('slug' => $project->getSlug())));
        }
        
		return $this->render('PhiTracProjectBundle:Project:delete_icon.html.twig', array('project' => $project, 'form' => $form->createView()));
    }

    /*
    * Generate the view of the list of projects
    * Used in menuListProjects.html.twig TWIG template.
    *
    * currentUrl: the current URL passed by the function render in :::layout.html.twig
    */
	public function menuAction($currentUrl)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('PhiTracProjectBundle:Project');

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $projects = $repository->findAllAlphabetical();
        } else {
            $projects = $repository->findProjectWhereUserIsMember($this->getUser());
        }
        
        $currentProject = null;
        foreach ($projects as $project) {
            if (strpos($currentUrl, $project->getSlug())!==false) {
                $currentProject = $project->getSlug();
            }
        }
        
		return $this->render('PhiTracProjectBundle::menuListProjects.html.twig', array('projects' => $projects, "currentProject" => $currentProject));
	}
	
	/*
    * Generate the view of the header of projects in items pages
    * Used in Items/*.html.twig TWIG templates.
    */
	public function headerAction(Project $project)
	{        
		return $this->render('PhiTracProjectBundle:Project:header.html.twig', array('project' => $project));
	}	
}
