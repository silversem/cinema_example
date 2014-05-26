<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

use Symfony\Component\HttpFoundation\Request;
use Models\Ticket\Item as TicketItem;
use Models\Ticket\Collection as TicketCollection;

$index = $app['controllers_factory'];


$index->post('/buy/', function(Request $request) use ($app)
{
    $seance = $request->get('session');
    $places = $request->get('places');

    if (empty($seance)) {
        return $app->json(['error' => 'Empty seance.'], 500);
    }

    if (empty($places)) {
        return $app->json(['error' => 'Empty places.'], 500);
    }

    $ticketItem = new TicketItem($app);

    return $ticketItem->save($seance, $places);
});

$index->post('/reject/', function(Request $request) use ($app)
{
    $key = $request->get('key');

    if (empty($key)) {
        return $app->json(['error' => 'Empty seance key.'], 500);
    }
    $ticketItem = new TicketItem($app);

    return $ticketItem->delete($key);
});

$index->get('/list/', function() use ($app)
{
    $ticketCollection = new TicketCollection($app);

    return $ticketCollection->getList();
});

return $index;
