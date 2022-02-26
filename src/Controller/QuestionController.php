<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController
{
    /**
     * @Route("/")
     */
    public function homepage() {
        return new Response("Hey hey");
    }
    
    /**
     * @Route("/questions/{slug}")
     */
    public function show($slug) {
        $slug = str_replace('-', ' ', $slug);
        return new Response(sprintf('Future page to show question "%s"', $slug));
    }
}