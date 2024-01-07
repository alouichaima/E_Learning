<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Lesson;
use App\Entity\Devoir;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class DevoirController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/devoir', name: 'app_devoir')]
    
    public function index(Request $request,ManagerRegistry $doctrine,Devoir $devoir=null){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $alldevoir=$doctrine->getRepository(Devoir::class)->findAll();
        $lesson = $doctrine->getRepository(Lesson::class)->findAll();
        
        
  
        if($devoir==null){
            $devoir = new Devoir();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($devoir)
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
                        $entityManager->persist($devoir);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_devoir');
                    }
                   
  
        return $this->render('devoir/index.html.twig', [
            'alldevoir' => $alldevoir,
            'lesson'=>$lesson,
            'form' =>$form->createView(),
            
        ]);
    }



     /**
     * @Route("getInfoDevoir/{id}", name="getInfoDevoir")
     */
    public function getInfoDevoir($id):Response
    // ici on récupère toute les infos de la question enseignant en fct de l'id passé en paramtre
    {
        try{
            $user = $this->em->find(Devoir::class,$id);
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
     * @Route("codeEditDevoir/{id}", name="codeEditDevoir")
     */
    public function codeEditDevoir(Request $request,int $id) :Response
    {
        try
        {
            $user = $this->em->find(Devoir::class,$id);
            
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
     * @Route("/delete_devoir/{id}" , name="delete_devoir")
     */
    public function deleteDevoir($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Devoir::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun devoir avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_devoir'));

    }
}
