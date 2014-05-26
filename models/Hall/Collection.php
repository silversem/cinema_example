<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\Hall;

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
     * Getting list of halls
     * @param bool $cinemaId
     * @param bool $refresh
     * @return array|SimpleChoiceList $choiceList
     */
    public function getList($cinemaId = false, $refresh = false)
    {
        $sql = '
            SELECT h.id, h.name
            FROM hall h
        ';
        if (!empty($cinemaId)) {
            $sql .= " where h.cinema_id = $cinemaId ";
        }
        $sql .= ' order by h.id';

        $list = $this->app['db']->fetchAll($sql);

        $choices = [];

        foreach ($list as $item) {
            $choices[$item['id']] = $item['name'];
        }

        if ($refresh === true) {
            return $choices;
        }

        $choiceList = new SimpleChoiceList($choices);

        return $choiceList;
    }


}