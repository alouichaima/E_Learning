<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Apprenant;
use App\Entity\Cours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\CoursRepository;

use App\Entity\Filter;
use App\Form\FilterFormType;

class ApprenantController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
   
    /**
     * @Route("/apprenant" , name="app_apprenant")
     
     */

    public function index(Request $request,ManagerRegistry $doctrine,Apprenant $apprenant=null){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $allapprenant=$doctrine->getRepository(Apprenant::class)->findAll();
  
  
        if($apprenant==null){
            $apprenant = new Apprenant();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($apprenant)
            // Configuration des paramètre du formulaire
                    ->add('email',EmailType::class)
                    ->add('password',PasswordType::class)
                    ->add('nom',TextType::class)
                    ->add('prenom',TextType::class)
                    ->add('image',FileType::class,array('data_class'=> null, 'label' => 'Image','label' => false ))
                    ->add('enregistrer',SubmitType::class)
                    ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){

                        $file = $form->get('image')->getData();
            
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();
                        try {
                            $file->move(
                                $this->getParameter('images_directory'),
                                $fileName
                            );
                        } catch (FileException $e) {
                           
                        }   $apprenant->setImage($fileName);

                        $entityManager->persist($apprenant);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_apprenant');
                    }
  
  
        return $this->render('apprenant/index.html.twig', [
            'allapprenant' => $allapprenant,
            'form' =>$form->createView(),
            'request' =>$request
            
        ]);
  
    }

    
  /**
     * @Route("/delete_apprenant/{id}" , name="delete_apprenant")
     */
    public function deleteApprenant($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Apprenant::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun apprenant avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_apprenant'));

    }

    /**
     * @Route("getInfoApprenant/{id}", name="getInfoApprenant")
     */
    public function getInfoApprenant($id)
    // ici on récupère toute les infos de l'enseignant en fct de l'id passé en paramtre
    {
        try{

            $user = $this->em->getRepository(Apprenant::class)->getOneApprenant((int)$id);
            

            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditApprenant", name="codeEditApprenant")
     */
    public function codeEditApprenant(Request $request)
    {
        try{
            $data = json_decode($request->getContent());

            $user = $this->em->find(Apprenant::class,(int)$data->id);
            $user->setEmail($data->email);
            //$user->setPassword($data->password);
            $user->setNom($data->nom);
            $user->setPrenom($data->prenom);
           

            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }


    
      /**
     * @Route("/liste_cours" , name="liste_cours")
     */

    public function list(Request $request,ManagerRegistry $doctrine,CoursRepository $a){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $allapprenant=$doctrine->getRepository(Cours::class)->findAll();
        
        $data = new Filter();
        $form = $this->createForm(FilterFormType::class, $data);
        $form->handleRequest($request);
       
        $cours = $a->findSearch($data);
        dump($cours);
        //dump($data);
  
        return $this->render('apprenant/test.html.twig', [
            'allapprenant' => $allapprenant,
            'form' => $form->createView(),
            'cours' => $cours
             
            
        ]);
    }

}
