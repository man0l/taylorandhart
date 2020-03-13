<?php


namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{

    const VIEW = 'view';
    private $security;
    private $configViewsNum;
    private $configWaitTimeInSeconds;

    public function __construct(Security $security, $configViewsNum, $configWaitTimeInSeconds)
    {
        $this->security = $security;
        $this->configViewsNum          = (int)$configViewsNum;
        $this->configWaitTimeInSeconds = (int)$configWaitTimeInSeconds;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports(string $attribute, $subject)
    {

        if($attribute != self::VIEW) {
            return true;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if($token === null) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $this->canView($user->getLastViewAt(), $user->getViews());
    }

    private function canView(\DateTimeInterface $lastViewsDate, $currentViewsNum)
    {
        $now = new \DateTime();
        $date = ($lastViewsDate !== null) ? clone $lastViewsDate : new \DateTime();
        $date->modify(sprintf('+%s seconds', $this->configWaitTimeInSeconds));

        if($currentViewsNum > $this->configViewsNum && $now > $date) {
            return true;
        }

        if($currentViewsNum <= $this->configViewsNum) {
            return true;
        }

        return false;
    }
}
