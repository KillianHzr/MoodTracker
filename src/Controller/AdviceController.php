<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Form\AdviceType;
use App\Repository\AdviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/advice')]
final class AdviceController extends AbstractController
{
    #[Route(name: 'app_advice_index', methods: ['GET'])]
    public function index(AdviceRepository $adviceRepository): Response
    {
        $adviceList = $adviceRepository->findAll();
        $adviceCount = count($adviceList);
        $canAddAdvice = $adviceCount < 5;

        return $this->render('advice/index.html.twig', [
            'advice' => $adviceList,
            'canAddAdvice' => $canAddAdvice,
        ]);
    }

    #[Route('/new', name: 'app_advice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AdviceRepository $adviceRepository): Response
    {
        if (count($adviceRepository->findAll()) >= 5) {
            $this->addFlash('warning', 'You have reached the maximum of 5 advices. Please edit or delete an existing one.');
            return $this->redirectToRoute('app_advice_index');
        }

        $templates = $this->getEmailTemplates();

        $advice = new Advice();
        $form = $this->createForm(AdviceType::class, $advice, [
            'email_templates' => $templates,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advice);
            $entityManager->flush();

            return $this->redirectToRoute('app_advice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advice/new.html.twig', [
            'advice' => $advice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advice_show', methods: ['GET'])]
    public function show(Advice $advice): Response
    {
        return $this->render('advice/show.html.twig', [
            'advice' => $advice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_advice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advice $advice, EntityManagerInterface $entityManager): Response
    {
        $templates = $this->getEmailTemplates();

        $form = $this->createForm(AdviceType::class, $advice, [
            'email_templates' => $templates,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_advice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advice/edit.html.twig', [
            'advice' => $advice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advice_delete', methods: ['POST'])]
    public function delete(Request $request, Advice $advice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_advice_index', [], Response::HTTP_SEE_OTHER);
    }

    private function getEmailTemplates(): array
    {
        $finder = new Finder();
        $templates = [];
        $path = $this->getParameter('kernel.project_dir') . '/templates/emails';

        if (is_dir($path)) {
            foreach ($finder->files()->in($path) as $file) {
                $filename = $file->getFilename();
                $templates[$filename] = $filename;
            }
        }

        return $templates;
    }
}
