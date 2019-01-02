<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager ;
    private $twig;
    private $route;
    // Nouvelle variable pour rendre plus facile la définition des templates et permettre de les surcharger
    // Ce dernier va être paramètré au niveau du fichier service.yaml
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function display(){
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setRoute($route){
        $this->route = $route;
        return $this;
    }

    public function getRoute(){
        return $this->route;
    }

    public function getPages(){

        //gestion des erreurs
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'aveaz pas spécifié l'entité sur laquelle vous devez paginer! Utiliser la fonction setEntityClass()");
        }

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        $pages = ceil($total / $this->limit);
        return $pages;
    }

    public function getData(){
        //gestion des erreurs
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'aveaz pas spécifié l'entité sur laquelle vous devez paginer! Utiliser la fonction setEntityClass()");
        }

        // 1 - Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2 - Demander au repository de trouver l'élément
        $repo = $this->manager->getRepository($this->entityClass);
        $data=$repo->findBy([], [], $this->limit, $offset);

        // 3 - Renvoyer les éléments en question
        return $data;
    }

    public function setPage($currentPage){
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }
}