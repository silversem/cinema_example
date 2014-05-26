<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\Ticket;

use Silex\Application;

class Collection
{
    protected $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Getting ordered ticket list.
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList()
    {
        $sql = '
            SELECT distinct o.key, o.create_date
            FROM ticket_order o
            ORDER BY o.id
        ';

        $list = $this->app['db']->fetchAll($sql);

        $itemList = [];

        foreach ($list as $key=>$item) {
            $itemList[$key] = $item['key'] . ' | ' . $item['create_date'];
        }

        return $this->app->json($itemList);
    }


}