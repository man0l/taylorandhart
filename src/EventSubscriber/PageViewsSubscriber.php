<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class PageViewsSubscriber implements EventSubscriberInterface
{
    const ROUTE_VIEW_VIDEO = 'view_video';
    private $user;
    private UserRepository $repository;

    public function __construct(Security $security, UserRepository $repository)
    {
        $this->user = $security->getUser();
        $this->repository = $repository;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if(!$this->user) {
            return $event;
        }

        $route = $event->getRequest()->get('_route');

        if(strpos($route, self::ROUTE_VIEW_VIDEO) !== false) {
            // update views and last view time

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
