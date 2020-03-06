<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class VideoController extends AbstractController
{
    /**
     * @ParamConverter("video", class="App\Entity\Video")
     * @Route("/video/{id}", name="view_video")
     */
    public function index(Video $video)
    {
        if(!$video) {
            throw new NotFoundHttpException('This video does not exsits');
        }

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            'video' => $video
        ]);
    }
}
