<?php

namespace App\Security;

use DateTime;
use App\Entity\User;
use App\Entity\Conge;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CongeVoter extends Voter
{
    public const EDIT = 'CONGE_EDIT';
    public const DELETE = 'CONGE_DELETE';
    public const ACCEPT = 'CONGE_ACCEPT';
    public const DECLINE = 'CONGE_DECLINE';

    protected function supports(string $attribute, $conge): bool
    {
        return in_array($attribute, [self::EDIT,self::DELETE, self::ACCEPT, self::DECLINE])
            && $conge instanceof \App\Entity\Conge;
    }

    protected function voteOnAttribute(string $attribute, $conge, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($conge, $user);
                break;
            case self::DELETE:
                return $this->canDelete($conge, $user);
                break;
            case self::ACCEPT:
                return $this->canAcceptOrDecline($conge, $user);
                break;
            case self::DECLINE:
                return $this->canAcceptOrDecline($conge, $user);
                break;
        }

        return false;
    }


    private function canEdit(Conge $conge, User $user): bool 
    {
        if($user == $conge->getDemandeur() && ($conge->getDateDebut() > new \DateTime('now -7 day'))) {
            return true;
        } else {
            foreach($conge->getDemandeur()->getResponsable() as $responsable) {
                if($user == $responsable) {
                    return true;
                }
            }
        }
        return false;
    }

    private function canDelete(Conge $conge, User $user): bool
    {
        if($user == $conge->getDemandeur() && ($conge->getDateDebut() > new \DateTime('now -7 day') )) {
            return true;
        } 
        return false;
    }

    private function canAcceptOrDecline(Conge $conge, User $user): bool
    {
        foreach($conge->getDemandeur()->getResponsable() as $responsable) {
            if($user == $responsable) {
                return true;
            }
        }
        return false;
    }
}
