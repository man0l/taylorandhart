<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\AccessVideoHelper;

class PageViewsSubscriber implements EventSubscriberInterface
{
    const ROUTE_VIEW_VIDEO = 'view_video';
    private User $user;
    private EntityManagerInterface $em;
    private int $configViewsNum;
    private int $configWaitTimeInSeconds;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, $configViewsNum, $configWaitTimeInSeconds)
    {
        if (null !== $token = $tokenStorage->getToken()) {
            if (\is_object($user = $token->getUser())) {
                /** @var User $user */
                $this->user = $user;
            }
        }

        $this->em = $em;
        $this->configViewsNum = (int)$configViewsNum;
        $this->configWaitTimeInSeconds = (int)$configWaitTimeInSeconds;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if(!isset($this->user)) {
            return $event;
        }

        $route = $event->getRequest()->get('_route');

        if(strpos($route, self::ROUTE_VIEW_VIDEO) !== false) {

            $hasAccess = AccessVideoHelper::hasAccess($this->user, $this->configViewsNum, $this->configWaitTimeInSeconds);

            if(!$hasAccess) {
                return $event;
            }
            // update views and last view time
            $this->user->setViews($this->user->getViews() + 1);
            $this->user->setLastViewAt(new \DateTime());

            $this->em->persist($this->user);
            $this->em->flush();

            return $event;
        }

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
