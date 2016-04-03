<?php

namespace PhiTrac\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use PhiTrac\UserBundle\Entity\User;

class ProfileController extends Controller
{
    public function showAction(User $user)
    {
    	if ($user!=$this->getUser() && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }

        return $this->render('PhiTracUserBundle:Profile:show.html.twig', array('user' => $user));
    }
}

