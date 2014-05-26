<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

use Models\Film\Collection as FilmCollection;

$index = $app['controllers_factory'];


$index->get('/{filmId}/schedule/', function($filmId) use ($app)
{
    if (empty($filmId)) {
        return $this->app->json(['error' => 'Empty film.'], 500);
    }

    $filmCollection = new FilmCollection($app);

    return $app->json($filmCollection->getSchedule($filmId));
});

return $index;
