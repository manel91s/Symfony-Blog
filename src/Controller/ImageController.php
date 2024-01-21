<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/images/avatar/{filename}', name: 'images_show', methods: 'GET')]
    public function showAvatar($filename)
    {
        // Construye la ruta completa al archivo de imagen
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/avatar/' . $filename;

        // Crea una respuesta binaria para enviar el archivo de imagen
        $response = new BinaryFileResponse($imagePath);

        // Define el tipo de contenido y forza la descarga (opcional)
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }

    #[Route('/images/posts/{filename}', name: 'posts_show', methods: 'GET')]
    public function showImagePost($filename)
    {
        // Construye la ruta completa al archivo de imagen
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/posts/' . $filename;

        // Crea una respuesta binaria para enviar el archivo de imagen
        $response = new BinaryFileResponse($imagePath);

        // Define el tipo de contenido y forza la descarga (opcional)
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }
}