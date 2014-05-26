<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\Cinema;

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
     * Getting a list of cinemas
     * @return SimpleChoiceList $choiceList
     */
    public function getList()
    {
        $sql = '
            SELECT c.id, c.name
            FROM cinema c
            order by c.id
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