<?php
// src/Security/TeamRoomVoter.php
namespace App\Security;

use App\Entity\TeamRoom;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamRoomVoter extends Voter
{
    const VIEW = 'view';
    const POST = 'post';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof TeamRoom && in_array($attribute, [self::VIEW, self::POST]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof Utilisateur) {
            return false;
        }

        /** @var TeamRoom $room */
        $room = $subject;

        // Pour VIEW: soit membre, soit dans l'équipe
        if ($attribute === self::VIEW) {
            return $this->canView($room, $user);
        }

        // Pour POST: doit être membre actif
        if ($attribute === self::POST) {
            return $this->canPost($room, $user);
        }

        return false;
    }

    private function canView(TeamRoom $room, Utilisateur $user): bool
    {
        // Vérifie si l'utilisateur est membre du salon
        if ($room->hasMember($user)) {
            return true;
        }

        // Vérifie si l'utilisateur est membre de l'équipe
        $equipe = $room->getEquipe();
        return $equipe->getConstructeur()?->getConstructeur() === $user ||
               $equipe->getGestionnairestock()?->getGestionnairestock() === $user ||
               $equipe->getArtisan()->exists(function($key, $artisan) use ($user) {
                   return $artisan->getArtisan() === $user;
               });
    }

    private function canPost(TeamRoom $room, Utilisateur $user): bool
    {
        foreach ($room->getMembers() as $member) {
            if ($member->getUser() === $user && $member->getIsActive()) {
                return true;
            }
        }
        return false;
    }
}