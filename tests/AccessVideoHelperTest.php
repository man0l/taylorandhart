<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Helper\AccessVideoHelper;
use App\Entity\User;

class AccessVideoHelperTest extends WebTestCase
{
    const CONFIG_VIEWS_NUM = 10;
    const CONFIG_WAIT_TIME_SECONDS_ONE_DAY = 864000;
    const CONFIG_WAIT_TIME_SECONDS_30_MIN  = 60 * 30;
    const CONFIG_WAIT_TIME_SECONDS_60_MIN  = 60 * 60;

    public function testAdminHasAccess()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn(new \DateTime());
        $stub->method('getIsAdmin')->willReturn(true);
        $stub->method('getViews')->willReturn(11);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_ONE_DAY);
        $this->assertTrue($access);
    }

    public function testViewsLessThan10()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn(new \DateTime());
        $stub->method('getIsAdmin')->willReturn(false);
        $stub->method('getViews')->willReturn(9);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_ONE_DAY);
        $this->assertTrue($access);
    }


    public function testViewsEqual10()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn(new \DateTime());
        $stub->method('getIsAdmin')->willReturn(false);
        $stub->method('getViews')->willReturn(10);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_ONE_DAY);
        $this->assertTrue($access);
    }

    public function testViewsMoreThan10()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn(new \DateTime());
        $stub->method('getIsAdmin')->willReturn(false);
        $stub->method('getViews')->willReturn(11);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_ONE_DAY);
        $this->assertFalse($access);
    }

    public function testViewsMoreThan10Before34minutesMax30min()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn((new \DateTime())->modify('-34 minutes'));
        $stub->method('getIsAdmin')->willReturn(false);
        $stub->method('getViews')->willReturn(11);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_30_MIN);
        $this->assertTrue($access);
    }

    public function testViewsMoreThan10Before34minutesMax60min()
    {
        $stub = $this->createMock(User::class);
        $stub->method('getLastViewAt')->willReturn((new \DateTime())->modify('-34 minutes'));
        $stub->method('getIsAdmin')->willReturn(false);
        $stub->method('getViews')->willReturn(11);

        $access = AccessVideoHelper::hasAccess($stub, self::CONFIG_VIEWS_NUM, self::CONFIG_WAIT_TIME_SECONDS_60_MIN);
        $this->assertFalse($access);
    }


}
