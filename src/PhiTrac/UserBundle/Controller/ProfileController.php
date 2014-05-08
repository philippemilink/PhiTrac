<?php

namespace PhiTrac\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PhiTrac\UserBundle\Entity\User;

class ProfileController extends Controller
{
    public function showAction(User $user)
    {
    	if ($user!=$this->getUser() AND !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied');
        }

        return $this->render('PhiTracUserBundle:Profile:show.html.twig', array('user' => $user));
    }
}

