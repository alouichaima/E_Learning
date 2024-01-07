<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/classe')]
class ClasseController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/', name: 'app_classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository): Response
    {
        return $this->render('classe/page_principal.html.twig', [
            'classes' => $classeRepository->findAll(),
            
        ]);
    }
    #[Route('/page_principal/{id}', name: 'page_principal', methods: ['GET'])]
    public function page(ClasseRepository $classeRepository,$id): Response
    {
        
        $classes=$classeRepository->findClasseByIdEnseignant($id);
    //$arrayEnseignant=$classes->getIdEnseignant();
    //dd($classes);
    //dd($classes.getIdEnseignant());
        return $this->render('classe/page_principal.html.twig', [
            'classes' => $classes,
            
            
        ]);
            
    
    }

    #[Route('/new', name: 'app_classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClasseRepository $classeRepository): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
               
            }   $classe->setImage($fileName);
            $classeRepository->add($classe);
            dd($classe);
            return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
            
        }

        return $this->renderForm('classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }
        
        

    #[Route('/{id}', name: 'app_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, ClasseRepository $classeRepository): Response
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }   $classe->setImage($fileName);
            $classeRepository->add($classe);
            return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

           

    #[Route('/{id}', name: 'app_classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, ClasseRepository $classeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->request->get('_token'))) {
            $classeRepository->remove($classe);
        }

        return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("getInfoClasse/{id}", name="getInfoClasse")
     */
    public function getInfoClasse($id)
    
    {
        try{

            $user = $this->em->getRepository(Classe::class)->getOneClasse((int)$id);
            

            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex) {
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }
}
