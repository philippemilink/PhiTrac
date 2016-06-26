<?php

namespace PhiTrac\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PhiTrac\UserBundle\Entity\User;
use PhiTrac\UserBundle\Form\Type\UserType;

class AdministrationController extends Controller
{
    public function homeAction()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('PhiTracUserBundle:User');
        $users = $repository->findAllAlphabetical();
        
        return $this->render('PhiTracUserBundle:Administration:home.html.twig', array("users" => $users));
    }
    
    public function createUserAction()
    {
        $user = new User();
		
		$form = $this->createForm(new UserType, $user);
		
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $em = $this->getDoctrine()->getManager();

		        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
                
                $user->setRoles(array($form->get('roles')->getData()));
		        
                $em->persist($user);
		        $em->flush();
		        
		        return $this->redirect($this->generateUrl('administration_home'));
	        }
        }        

        return $this->render('PhiTracUserBundle:Administration:createUser.html.twig', array('form' => $form->createView()));
    }
}

