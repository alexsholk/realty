<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gearman")
 */
class GearmanController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        die('gear!');
    }
}