<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/video/{id}", name="view_video")
     */
    public function index(int $id, VideoRepository $repository)
    {

        $video = $repository->find($id);
        if(!$video) {
            throw new NotFoundHttpException('This video does not exsits');
        }

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            'video' => $video
        ]);
    }
}
