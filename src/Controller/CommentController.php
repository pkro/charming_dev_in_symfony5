<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/comments/{id}/vote/{direction<up|down>}", methods="POST")
     */
    public function commentVote($id, $direction) {
        // todo: use id to query db
        $currentVoteCount = $direction == 'up' ?  rand(7, 100) : rand(0,5);
    
        return $this->json(['votes' => $currentVoteCount]);
        
    }
}