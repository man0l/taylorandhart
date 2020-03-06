<?php

namespace App\Tests;

use App\Entity\User;
use DG\BypassFinals;
use Doctrine\ORM\EntityManagerInterface;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\VarDumper\Cloner\Stub;
use App\EventSubscriber\PageViewsSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PageViewsSubscriberTest extends TestCase
{

    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $em;
    private $configViewsNum = 10;
    private $configWaitTimeInSeconds = 86400;

    public function testOnKernelResponseEventNotLoggedUser() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn(null);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager->expects($this->never())
            ->method($this->anything());

        $mockedEvent = $this->createMock(ResponseEvent::class);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventLoggedUserRouteNotVideo() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(10);
        $user->setIsAdmin(false);
        $user->setLastViewAt(new \DateTime());

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager->expects($this->never())
            ->method($this->anything());

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn('not-video');

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventLoggedUserRouteVideo() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(10);
        $user->setIsAdmin(false);
        $user->setLastViewAt(new \DateTime());

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $entityManager->expects($this->once())
            ->method('persist');

        $entityManager->expects($this->once())
            ->method('flush');

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn(PageViewsSubscriber::ROUTE_VIEW_VIDEO);

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventAdminUserRouteVideoViewsMoreThan10() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(11);
        $user->setIsAdmin(true);
        $user->setLastViewAt((new \DateTime())->modify('-1 hour'));

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $entityManager->expects($this->atLeastOnce())
            ->method('persist');

        $entityManager->expects($this->atLeastOnce())
            ->method('flush');

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn(PageViewsSubscriber::ROUTE_VIEW_VIDEO);

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventUserRouteVideoViewsMoreThan10WithinTimePeriod() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(11);
        $user->setIsAdmin(false);
        $user->setLastViewAt((new \DateTime())->modify('-1 hour'));

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $entityManager->expects($this->never())
            ->method('persist');

        $entityManager->expects($this->never())
            ->method('flush');

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn(PageViewsSubscriber::ROUTE_VIEW_VIDEO);

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventUserRouteVideoViewsMoreThan10OverTimePeriod() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(11);
        $user->setIsAdmin(false);
        $user->setLastViewAt((new \DateTime())->modify('-2 day'));

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $entityManager->expects($this->once())
            ->method('persist');

        $entityManager->expects($this->once())
            ->method('flush');

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn(PageViewsSubscriber::ROUTE_VIEW_VIDEO);

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }

    public function testOnKernelResponseEventUserRouteVideoViewsLessThan10() {

        // use this library to mock the ResponseEvent object
        BypassFinals::enable();

        $user = new User();
        $user->setEmail('email@email.com');
        $user->setViews(5);
        $user->setIsAdmin(false);
        $user->setLastViewAt((new \DateTime())->modify('-2 day'));

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $entityManager->expects($this->atLeastOnce())
            ->method('persist');

        $entityManager->expects($this->atLeastOnce())
            ->method('flush');

        $mockedRequest = $this->createMock(Request::class);
        $mockedRequest->method('get')->willReturn(PageViewsSubscriber::ROUTE_VIEW_VIDEO);

        $mockedEvent = $this->createMock(ResponseEvent::class);
        $mockedEvent->method('getRequest')->willReturn($mockedRequest);

        $subscriber = new PageViewsSubscriber($tokenStorage, $entityManager, 10, 86400);
        $subscriber->onKernelResponse($mockedEvent);

        $this->assertEquals($mockedEvent, $subscriber->onKernelResponse($mockedEvent));
    }
}
