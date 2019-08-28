<?php

namespace App\Controller;

use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Polygon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $x_lat  = $request->get('x_lat', 53.70158461260564);
        $x_long = $request->get('x_long', 27.235107421875004);
        $y_lat  = $request->get('y_lat', 54.093630810050485);
        $y_long = $request->get('y_long', 27.888793945312504);

        /*
         * y_ ------- y
         * |          |
         * x -------- x_
         */
        $polygon = new Polygon();
        $polygon->addPoint($x  = new Coordinate($x_lat, $x_long));
        $polygon->addPoint($x_ = new Coordinate($x_lat, $y_long));
        $polygon->addPoint($y  = new Coordinate($y_lat, $y_long));
        $polygon->addPoint($y_ = new Coordinate($y_lat, $x_long));

        $calc = new Vincenty();
        $distance = $calc->getDistance($x, $y) / 10 ** 3;
        $height   = $calc->getDistance($x, $y_) / 10 ** 3;
        $width    = $calc->getDistance($y_ , $y) / 10 ** 3 . '...' . $calc->getDistance($x, $x_) / 10 ** 3;
        $area     = $polygon->getArea() / 10 ** 6;

        return $this->render('default/index.html.twig', [
            'x_lat'  => $x_lat,
            'x_long' => $x_long,
            'y_lat'  => $y_lat,
            'y_long' => $y_long,
            'area'   => $area,
            'distance' => $distance,
            'height' => $height,
            'width' => $width,
        ]);
    }
}