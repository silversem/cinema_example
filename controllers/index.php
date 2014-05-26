<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

use Models\Cinema\Collection as CinemaCollection;
use Models\Hall\Collection as HallCollection;
use Models\Film\Collection as FilmCollection;
use Models\FilmSeance\Collection as FilmSeanceCollection;

$index = $app['controllers_factory'];

$index->get('/', function() use ($app)
{
    $cinemaCollection = new CinemaCollection($app);
    $cinemaChoiceList = $cinemaCollection->getList();

    $hallCollection = new HallCollection($app);
    $hallChoiceList = $hallCollection->getList($cinemaChoiceList->getValues()[0]);

    $filmCollection = new FilmCollection($app);
    $filmChoiceList = $filmCollection->getList();

    $filmSeanceCollection = new FilmSeanceCollection($app);
    $filmSeanceChoiceList = $filmSeanceCollection->getList();

    $getScheduleForm = $app['form.factory']->createBuilder('form')
        ->add('cinemaName', 'choice', array(
            'choice_list' => $cinemaChoiceList,
        ))
        ->add('hallName', 'choice', array(
            'required' => false,
            'empty_value' => 'Choose an option',
            'choice_list' => $hallChoiceList,
        ))
        ->getForm()
    ;

    $getFilmForm = $app['form.factory']->createBuilder('form')
        ->add('filmName', 'choice', array(
            'choice_list' => $filmChoiceList,
        ))
        ->getForm()
    ;

    $getFilmSeanceForm = $app['form.factory']->createBuilder('form')
        ->add('filmSeanceName', 'choice', array(
            'choice_list' => $filmSeanceChoiceList,
        ))
        ->getForm()
    ;

    $rejectTicketForm = $app['form.factory']->createBuilder('form')
        ->add('key', 'text')
        ->getForm()
    ;

    return $app['twig']->render('index.twig', array(
        'get_schedule_form' => $getScheduleForm->createView(),
        'get_film_form' => $getFilmForm->createView(),
        'get_film_seance_form' => $getFilmSeanceForm->createView(),
        'reject_ticket_form' => $rejectTicketForm->createView()
    ));
});

return $index;

?>