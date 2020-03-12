<?php

namespace App\Helper;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessVideoHelper
{
    static User $user;
    static function hasAccess(UserInterface $user, $configViewsNum = 10, $configWaitTimeInSeconds = 3600) {


        $date = ($user->getLastViewAt() !== null) ? clone $user->getLastViewAt() : new \DateTime();

        $date->modify(sprintf('+%s seconds', $configWaitTimeInSeconds));
        $now = new \DateTime();

        return (
            $user->getIsAdmin() ||
            ($user->getViews() > $configViewsNum && $now > $date) ||
            ($user->getViews() <= $configViewsNum)
        );
    }
}
