<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PageViewsSubscriber implements EventSubscriberInterface
{
    const ROUTE_VIEW_VIDEO = 'view_video';
    private User $user;
    private UserRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        if (null !== $token = $tokenStorage->getToken()) {
            if (\is_object($user = $token->getUser())) {
                $this->user = $user;
            }
        }

        $this->em = $em;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if(!isset($this->user)) {
            return $event;
        }

        $route = $event->getRequest()->get('_route');

        if(strpos($route, self::ROUTE_VIEW_VIDEO) !== false) {

            // update views and last view time
            echo $this->user->getId();
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
