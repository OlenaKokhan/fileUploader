<?php

namespace AppBundle\Controller;

//use App\Storage\RedisCacheRepository;

use AppBundle\Entity\FileEntity;
use AppBundle\Form\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function newAction(Request $request)
    {
        $fileHelper = $this->container->get('file_helper');

        $fileEntity = new FileEntity();
        $fileUploader = $this->container->get('file_uploader');
        $form = $this->createForm(FormType::class, $fileEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $fileEntity->getFile();

            $fileName = $fileUploader->upload($file);

            $fileEntity->setFile($fileName);
            $fileHelper->split_file($fileName, $this->getParameter('files_parts'));
            return $this->redirect($this->generateUrl('download_button', ['name' => $fileName]));
        }

        return $this->render('default/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param string $name
     * @Route("/{name}", name="download_button")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(string $name)
    {
        $fileHelper = $this->container->get('file_helper');
        $return = $fileHelper->merge_file($name, $this->getParameter('files_parts'));
        return $this->render('default/result.html.twig', array(
            'download_path' => $this->getParameter('download_path') . '/' . $name
        ));
    }
}