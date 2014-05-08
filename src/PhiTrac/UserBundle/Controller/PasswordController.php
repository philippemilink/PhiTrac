<?php

namespace PhiTrac\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PhiTrac\UserBundle\Entity\User;

class PasswordController extends Controller
{
    public function changeAction(User $user)
    {
        if ($user!=$this->getUser() AND !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied');
        }

        $errors = null;
        
        $formBuilder = $this->createFormBuilder($user);
        $formBuilder->add('currentPassword', "password", array('mapped' => false));
        $formBuilder->add('newPassword', "password", array('mapped' => false));
        $formBuilder->add('verification', "password", array('mapped' => false));
        $form = $formBuilder->getForm();
        
        $request = $this->get('request');
        if ($request->getMethod()=='POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
		        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $currentPassword = $encoder->encodePassword($form->get('currentPassword')->getData(), $user->getSalt());  
                
                if ($currentPassword===$user->getPassword()) {
                    if ($form->get('newPassword')->getData()===$form->get('verification')->getData()) {
                        $user->setPassword($encoder->encodePassword($form->get('newPassword')->getData(), $user->getSalt()));
                        
                        $em = $this->getDoctrine()->getManager();
		                $em->persist($user);
		                $em->flush();
                        
                        return $this->redirect($this->generateUrl('profile_show'));
                    } else {
                        $errors = "New password and verification are not equal.";
                    }
	            } else {
	                $errors = "Current password incorrect.";                
	            }
	        }
        }
        
        return $this->render('PhiTracUserBundle:Password:change.html.twig', array("errors" => $errors, "form" => $form->createView(), "user" => $user));
    }
}

