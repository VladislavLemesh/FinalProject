<?php

namespace App\Controller;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\VideoRepository;
use App\Form\VideoType;


class VideoController extends AbstractController
{
    #[Route('/show_videos', name: 'show_videos')]
    public function showVideos(ManagerRegistry $doctrine): Response
    {
        $videos = $doctrine->getRepository(Video::class)->findAll();
        return $this->render('video/show_videos.html.twig', [
          'videos' => $videos
        ]);
    }

    #[Route('/add_video', name: 'add_video')]
    public function add(Request $request, VideoRepository $videoRepository): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoRepository->add($video, true);

            return $this->redirectToRoute('show_videos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/add_video.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

  #[Route('/show_video_by_cat/{category_id}', name: 'show_video_by_cat')]
  public function showByCategory(ManagerRegistry $doctrine, int $category_id): Response
  {
    $videos = $doctrine->getRepository(Video::class)->findByCategory($category_id);
    return $this->render('video/show_videos.html.twig', [
      'videos' => $videos
    ]);
  }

    #[Route('/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoRepository->add($video, true);

            return $this->redirectToRoute('show_videos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $videoRepository->remove($video, true);
        }

        return $this->redirectToRoute('show_videos', [], Response::HTTP_SEE_OTHER);
    }
}
