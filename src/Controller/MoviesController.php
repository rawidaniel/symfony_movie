<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Name;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

class MoviesController extends AbstractController
{

    private $movieRepository;
    private $em;

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    #[Route('/movies', name: 'app_movies')]
    public function index(): Response
    {

        $movies = $this->movieRepository->findAll();
        // dd($movies);

        return $this->render('movies/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('movies/create', name: 'app_movie_create')]
    public function create(Request $request): Response
    {

        $movie = new Movie();
        $form =  $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movies/edit/{id}', name: 'app_movie_edit')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($imagePath) {

                if ($movie->getImagePath() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath())) {

                        unlink($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath());
                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                        try {
                            $imagePath->move(
                                $this->getParameter('kernel.project_dir') . '/public/uploads',
                                $newFileName
                            );
                        } catch (FileException $e) {
                            return new Response($e->getMessage());
                        }
                    }
                    $movie->setImagePath('/uploads/' . $newFileName);
                    $this->em->flush();
                    return $this->redirectToRoute('app_movies');
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());
                $this->em->flush();

                return $this->redirectToRoute('app_movies');
            }
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{id}', methods: ['GET', 'DELETE'], name: 'app_movie_delete')]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        if ($movie->getImagePath()) {
            if (file_exists($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath())) {
                unlink($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath());
            }
        }

        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirectToRoute('app_movies');
    }

    #[Route('/movies/{id}', methods: ["GET"], name: 'app_movie')]
    public function showMovie($id): Response
    {

        $movie = $this->movieRepository->find($id);
        // dd($movies);

        return $this->render('movies/show.html.twig', [
            'movie' => $movie,
        ]);
    }


    #[Route('/api', name: 'app_api')]
    #[OA\Response(
        response: 200,
        description: "Returns the list of books",
        // content: [
        //     'application/json' => [
        //         'schema' => [
        //             'type' => 'array',
        //             'items' => new OA\Items(ref: @Model(type: Movie::class))
        //         ]
        //     ]
        // ]
    )]
    #[OA\Tag(name: 'movies')]
    public function api(): JsonResponse
    {;
        $movie = $this->movieRepository->findAll();
        // dd($movies);

        return $this->json($movie);
    }
}
























// class MoviesController extends AbstractController
// {

//     private $em;

//     public function __construct(EntityManagerInterface $em){
//         $this->em = $em;
//     }

//     #[Route('/movies', name: 'app_movies')]
//     public function index(): Response
//     {
//         // return $this->render('movies/index.html.twig', [
//         //     'controller_name' => 'MoviesController',
//         // ]);
//         $repository = $this->em->getRepository(Movie::class);
//         // $movies = $repository->findAll();
//         // $movies = $repository->find(3);
//         // $movies = $repository->findBy(['title' => 'The Godfather']);
//         // $movies = $repository->findBy([],['id' => 'Desc']);
//         $movies = $repository->findOneBy(["id"=> 3, 'title' => "The Godfather"], ['id' => 'DESC']);

//         $movies = $repository->count([]);
//         $movies = $repository->getClassName();
//         // dd($movies);

//         return $this->render('movies/index.html.twig', [
//             'movies' => $movies,
//         ]);
//         // return $this->json(["hello world" => $movies]);
//     }


// }
