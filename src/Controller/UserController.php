<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $encorder, SluggerInterface $slugger)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encorder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            //ajouter un fichier 
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('avatar')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFilename);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('avatar'),
                        $newFileName
                    );
                } catch (FileException $error) {
                    $error->getMessage();
                }
                $user->setAvatar($newFileName);
            }
            $om = $doctrine->getManager();
            $om->persist($user);
            $om->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('user/add.html.twig', [
            "formulaireinscription" => $form->createView()
        ]);
    }
}
