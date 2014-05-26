<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\FilmSeance;

use Silex\Application;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class Collection
{
    protected $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Getting film-seance schedule.
     * @param int $cinemaId
     * @param bool $hall
     * @return array
     */
    public function getSchedule($cinemaId, $hall = false)
    {
        $sql = '
            SELECT c.name as cinema_name, h.name as hall_name,
                   fs.id as film_seance_id,s.`time` as seance_time, f.name as film_name
            FROM film_seance fs
            left join hall h on fs.hall_id = h.id
            left join cinema c on h.cinema_id = c.id
            left join seance s on fs.seance_id = s.id
            left join film f on fs.film_id = f.id
            where c.id = \'' . $this->app->escape($cinemaId) . '\'
                  and s.`time` >=  (DATE_SUB(NOW(), INTERVAL 3 HOUR))
        ';
        if (!empty($hall)) {
            $sql .= ' and h.id = \'' . $this->app->escape($hall) . '\' ';
        }
        $sql .= ' order by s.time, h.name';

        return $this->app['db']->fetchAll($sql);
    }

    /**
     * Getting available places for seance.
     * @param int $seanceId
     * @return array
     */
    public function getAvailablePlaces($seanceId)
    {
        $sql = '
            SELECT p.id as place_id, p.number as place_number
            FROM film_seance fs
            left join hall h on fs.hall_id = h.id
            left join place p on h.id = p.hall_id
            left join ticket_order o on o.film_seance_id = fs.id and o.place_id = p.id
            where fs.id = \'' . $this->app->escape($seanceId) . '\' and o.place_id is null
            order by p.number
        ';

        return $this->app['db']->fetchAll($sql);
    }

    /**
     * Getting seance list
     * @return SimpleChoiceList $choiceList
     */
    public function getList()
    {
        $sql = '
            SELECT fs.id AS film_seance_id, f.name AS film_name,
             s.`time` AS seance_time, h.name AS hall_name, c.name AS cinema_name
            FROM film_seance fs
            LEFT JOIN hall h ON fs.hall_id = h.id
            LEFT JOIN cinema c ON h.cinema_id = c.id
            LEFT JOIN seance s ON fs.seance_id = s.id
            LEFT JOIN film f ON fs.film_id = f.id
            WHERE s.`time` >=  (DATE_SUB(NOW(), INTERVAL 3 HOUR))
            ORDER BY f.name, s.time, h.name
        ';

        $list = $this->app['db']->fetchAll($sql);

        $choices = [];

        foreach ($list as $item) {
            $choices[$item['film_seance_id']] =
                $item['film_name'] . ' | ' .
                $item['seance_time'] . ' | ' .
                $item['hall_name'] . ' | ' .
                $item['cinema_name']
            ;
        }

        $choiceList = new SimpleChoiceList($choices);

        return $choiceList;
    }

}