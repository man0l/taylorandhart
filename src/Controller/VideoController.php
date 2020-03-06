<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Helper\AccessVideoHelper;

class VideoController extends AbstractController
{
    /**
     * @ParamConverter("video", class="App\Entity\Video")
     * @Route("/video/{id}", name="view_video")
     */
    public function index(Video $video)
    {
        if(!$video) {
            throw new NotFoundHttpException('This video does not exsist');
        }

        $access = AccessVideoHelper::hasAccess(
            $this->getUser(),
            (int)$this->getParameter('max_views'),
            (int)$this->getParameter('wait_time_seconds')
        );

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            'video' => $video,
            'access' => $access,
            'max_views' => $this->getParameter('max_views'),
            'wait_time_seconds' => $this->getParameter('wait_time_seconds')
        ]);
    }
}
