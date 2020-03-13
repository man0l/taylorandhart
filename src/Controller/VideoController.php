<?php

namespace App\Controller;

use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Video $video, EntityManagerInterface $entityManager)
    {
        if(!$video) {
            throw new NotFoundHttpException('This video does not exsist');
        }

        $hasAccess = $this->isGranted(UserVoter::VIEW);

        if($hasAccess) {
            // update the user
            $user = $this->getUser();
            if(\is_object($user)) {
                $user->setViews($this->user->getViews() + 1);
                $user->setLastViewAt(new \DateTime());
                $entityManager->flush();
            }
        }

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            'video' => $video,
            'hasAccess' => $hasAccess,
            'max_views' => $this->getParameter('max_views'),
            'wait_time_seconds' => $this->getParameter('wait_time_seconds')
        ]);
    }
}
