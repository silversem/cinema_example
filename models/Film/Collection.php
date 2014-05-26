<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\Film;

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
     * Getting film schedule.
     * @param int $filmId
     * @return array
     */
    public function getSchedule($filmId)
    {
        $sql = '
            SELECT c.name as cinema_name, h.name as hall_name,
                   fs.id as film_seance_id,s.`time` as seance_time, f.name as film_name
            FROM film_seance fs
            left join hall h on fs.hall_id = h.id
            left join cinema c on h.cinema_id = c.id
            left join seance s on fs.seance_id = s.id
            left join film f on fs.film_id = f.id
            where f.id = \'' . $this->app->escape($filmId) . '\'
                  and s.`time` >=  (DATE_SUB(NOW(), INTERVAL 3 HOUR))
            order by s.time, c.name, h.name
        ';

        return $this->app['db']->fetchAll($sql);
    }

    /**
     * Getting films list
     * @return SimpleChoiceList $choiceList
     */
    public function getList()
    {
        $sql = '
            SELECT f.id, f.name
            FROM film f
            order by f.id
        ';

        $list = $this->app['db']->fetchAll($sql);

        $choices = [];

        foreach ($list as $item) {
            $choices[$item['id']] = $item['name'];
        }

        $choiceList = new SimpleChoiceList($choices);

        return $choiceList;
    }
}