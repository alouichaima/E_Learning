<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class ReponseController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/reponse', name: 'app_reponse')]
    
    public function index(Request $request,ManagerRegistry $doctrine,Reponse $reponse=null){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $allreponse=$doctrine->getRepository(Reponse::class)->findAll();
        $question = $doctrine->getRepository(Question::class)->findAll();
        
        
  
        if($reponse==null){
            $reponse = new Reponse();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($reponse)
            // Configuration des paramètre du formulaire
                    ->add('nom',TextType::class)
                    ->add('id_question',EntityType::class, [
                        'class'=> Question::class,
                        'choice_label'=>'nom_question',
                        'expanded'=>false,
                        'multiple'=>false
                    ])
                    ->add('content', CKEditorType::class)
                    ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){
                        $entityManager->persist($reponse);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_reponse');
                    }
                   
  
        return $this->render('reponse/index.html.twig', [
            'allreponse' => $allreponse,
            'question'=>$question,
            'form' =>$form->createView(),
            
        ]);
    }

    /**
     * @Route("getInfoReponse/{id}", name="getInfoReponse")
     */
    public function getInfoReponse($id):Response
    // ici on récupère toute les infos de la question enseignant en fct de l'id passé en paramtre
    {
        try{
            $user = $this->em->find(Reponse::class,$id);
            $data=[
                "id"=>$user->getId(),
                "nom"=>$user->getNom(),
                "id_question"=>$user->getIdQuestion()->getId(),
                "content"=>$user->getContent()
            ];
            
            return $this->json($data);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditReponse/{id}", name="codeEditReponse")
     */
    public function codeEditReponse(Request $request,int $id) :Response
    {
        try
        {
            $user = $this->em->find(Reponse::class,$id);
            
            $question=$this->em->find(Question::class,$request->request->get("id_question"));
            
           
            $user->setNom($request->request->get("nom"));
            $user->setIdQuestion($question);
            $user->setContent($request->request->get("content"));
          
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
     * @Route("/delete_reponse/{id}" , name="delete_reponse")
     */
    public function deleteReponse($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Reponse::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucune reponse avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_reponse'));

    }
}
