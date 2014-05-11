<?php

namespace PhiTrac\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use PhiTrac\ProjectBundle\Entity\Project;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	public function findAllAlphabetical()
    {
        return $this->_em->createQueryBuilder()
                    ->select('a')
                    ->from('PhiTracUserBundle:User', 'a')
                    ->orderBy('a.name')
                    ->getQuery()
                    ->getResult();
    }

    public function findNotMemberOfProject(Project $project)
    {
        $users = $this->findAllAlphabetical();
       	$i = 0;
        foreach ($users as $user) {
        	foreach ($project->getMembers() as $member) {
        		if ($member==$user) {
        			unset($users[$i]);
        		}
        	}

        	if ($user==$project->getCreator()) {
        		unset($users[$i]);
        	}
        	$i++;
        }

        return $users;
    }
}
