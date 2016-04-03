<?php

namespace PhiTrac\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use PhiTrac\ProjectBundle\Entity\Project;
use PhiTrac\UserBundle\Entity\User;


class MembersController extends Controller
{
    /*
    * Show the members of a project (GET method)
    * POST method: add a member to the project
    */
    public function homeAction(Project $project) 
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        } 

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('PhiTracUserBundle:User');
        
        if ($request->getMethod()=='POST') {
            $userToAdd = $repository->findOneBySlug($request->request->get('userToAdd'));
            if ($userToAdd===null) {
                throw $this->createNotFoundException('User not found.');
            }
            $project->addMember($userToAdd);
            $em->persist($project);
            $em->flush();
        }
   
        $users = $repository->findNotMemberOfProject($project);       
            
		return $this->render('PhiTracProjectBundle:Members:home.html.twig', array('project' => $project, 'users' => $users));
    }

    /**
    * Delete a project
    * GET method: ask confirmation to delete the project
    * POST method: delete the project and redirect to the empty homepage
    *
    * @ParamConverter("project", options={"mapping": {"project_slug": "slug"}})
    * @ParamConverter("user", options={"mapping": {"user_slug": "slug"}})
    */ 
	public function removeAction(Project $project, User $user)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }
        
        $form = $this->createFormBuilder()->getForm();
        
        if ($this->get('request')->getMethod()=='POST') {
            $em = $this->getDoctrine()->getManager();
            
            $project->removeMember($user);
            
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('members_home', array('slug' => $project->getSlug())));
        }
        
		return $this->render('PhiTracProjectBundle:Members:remove.html.twig', array('project' => $project, 'user' => $user, 'form' => $form->createView()));
    }
}
