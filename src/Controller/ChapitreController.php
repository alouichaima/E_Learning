<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Lesson;
use App\Entity\Chapitre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class ChapitreController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/chapitre', name: 'app_chapitre')]
    
    public function index(Request $request,ManagerRegistry $doctrine,Chapitre $chapitre=null){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $allchapitre=$doctrine->getRepository(Chapitre::class)->findAll();
        $lesson = $doctrine->getRepository(Lesson::class)->findAll();
        
        
  
        if($chapitre==null){
            $chapitre = new Chapitre();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($chapitre)
            // Configuration des paramètre du formulaire
                    ->add('nom',TextType::class)
                    ->add('id_lesson',EntityType::class, [
                        'class'=> Lesson::class,
                        'choice_label'=>'nom',
                        'expanded'=>false,
                        'multiple'=>false
                    ])
                    ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){
                        $entityManager->persist($chapitre);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_chapitre');
                    }
                   
  
        return $this->render('chapitre/index.html.twig', [
            'allchapitre' => $allchapitre,
            'lesson'=>$lesson,
            'form' =>$form->createView(),
            
        ]);
    }

    
     /**
     * @Route("getInfoChapitre/{id}", name="getInfoChapitre")
     */
    public function getInfoChapitre($id):Response
    // ici on récupère toute les infos de la question enseignant en fct de l'id passé en paramtre
    {
        try{
            $user = $this->em->find(Chapitre::class,$id);
            $data=[
                "id"=>$user->getId(),
                "nom"=>$user->getNom(),
                "id_lesson"=>$user->getIdLesson()->getId(),
            ];
            
            return $this->json($data);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditChapitre/{id}", name="codeEditChapitre")
     */
    public function codeEditChapitre(Request $request,int $id) :Response
    {
        try
        {
            $user = $this->em->find(Chapitre::class,$id);
            
            $lesson=$this->em->find(Lesson::class,$request->request->get("id_lesson"));
            
           
            $user->setNom($request->request->get("nom"));
            $user->setIdLesson($lesson);
        
          
            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }
        catch(Exception $ex)
        {
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/delete_chapitre/{id}" , name="delete_chapitre")
     */
    public function deleteChapitre($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Chapitre::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun chapitre avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_chapitre'));

    }
}
