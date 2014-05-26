<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

use Symfony\Component\HttpFoundation\Request;
use Models\FilmSeance\Collection as FilmSeanceCollection;
use Models\Hall\Collection as HallCollection;

$index = $app['controllers_factory'];


$index->get('/{cinemaId}/schedule/', function(Request $request, $cinemaId) use ($app)
{
    $hall = $request->get('hall');

    if (empty($cinemaId)) {
        return $this->app->json(['error' => 'Empty cinema.'], 500);
    }

    $filmSeanceCollection = new FilmSeanceCollection($app);

    return $app->json($filmSeanceCollection->getSchedule($cinemaId, $hall));
});

$index->get('/{cinemaId}/halls/', function($cinemaId) use ($app)
{
    if (empty($cinemaId)) {
        return $this->app->json(['error' => 'Empty cinema.'], 500);
    }

    $hallCollection = new HallCollection($app);

    return $app->json($hallCollection->getList($cinemaId, true));
});

return $index;
