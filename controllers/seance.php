<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

use Symfony\Component\HttpFoundation\Request;
use Models\FilmSeance\Collection as FilmSeanceCollection;

$index = $app['controllers_factory'];


$index->get('/{seanceId}/places/', function(Request $request, $seanceId) use ($app)
{
    if (empty($seanceId)) {
        return $this->app->json(['error' => 'Empty seance.'], 500);
    }

    $filmSeanceCollection = new FilmSeanceCollection($app);

    return $app->json($filmSeanceCollection->getAvailablePlaces($seanceId));
})->assert('seanceId', '\d+');

return $index;
