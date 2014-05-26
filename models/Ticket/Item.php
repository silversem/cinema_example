<?php
/**
 * User: Pavlov Semyen
 * Date: 5/26/14
 */

namespace Models\Ticket;

use Silex\Application;

class Item
{
    protected $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Saving tickets order.
     * @param int $seance
     * @param string $places (f.e.:'1,2,3')
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @throws Exception
     */
    public function save($seance, $places)
    {
        if (empty($places)) {
            return $this->app->json(['error' => 'Empty places.'], 500);
        }

        if ($this->checkPlacesIsFree($seance, $places) === true) {
            $sqlParts = '';
            $places = explode(',',$places);

            $key = uniqid('ticket_');

            foreach ($places as $placeId) {
                $sqlParts[] = "('$seance', '$placeId', '$key')";
            }

            if (!empty($sqlParts)) {
                $sql = 'insert into ticket_order (`film_seance_id`, `place_id`, `key`) values ' . implode(',', $sqlParts);
                $this->app['db']->beginTransaction();
                try {
                    $this->app['db']->executeQuery($sql);
                    $this->app['db']->commit();

                    return $this->app->json(['key' => $key, 'msg' => 'Successfully reserved.']);
                } catch (Exception $e) {
                    $this->app['db']->rollback();
                    throw $e;
                }
            }
        }

        return $this->app->json(['error' => 'Some of places is not free.'], 500);
    }

    /**
     * Is selected place free?
     * @param int $seance
     * @param string $placeIds (f.e.:'1,2,3')
     * @return bool
     */
    private function checkPlacesIsFree($seance, $placeIds)
    {
        $sql = '
            SELECT count(1) as cnt
            FROM film_seance fs
            left join hall h on fs.hall_id = h.id
            left join place p on h.id = p.hall_id
            left join ticket_order o on o.film_seance_id = fs.id and o.place_id = p.id
            where fs.id = \'' . $this->app->escape($seance) . '\' and
                  p.id in (' . $this->app->escape($placeIds) . ') and o.place_id is not null
        ';

        $cnt = $this->app['db']->fetchColumn($sql, [], 0);

        return ((int)$cnt > 0) ? false : true;
    }

    /**
     * Checking for allowed period to remove.
     * @param string $key
     * @return bool
     */
    private function checkDateToRemove($key)
    {
        $sql = "
            SELECT TIMESTAMPDIFF(MINUTE,now(),min(s.time)) as diff
            FROM ticket_order o
            left join film_seance fs on o.film_seance_id = fs.id
            left join seance s on fs.seance_id = s.id
            WHERE o.`key` = '$key'
        ";

        $diff = $this->app['db']->fetchColumn($sql, [], 0);

        return (((int)$diff < 60) and ((int)$diff >= 0)) ? true : false;
    }

    /**
     * Check tickets for key exists.
     * @param string $key
     * @return bool
     */
    private function checkTicketExists($key)
    {
        $sql = "
            SELECT count(1) as cnt
            FROM ticket_order o
            WHERE o.`key` = '$key'
        ";

        $cnt = $this->app['db']->fetchColumn($sql, [], 0);

        return ((int)$cnt > 0) ? true : false;
    }

    /**
     * Deleting tickets for selected key.
     * @param string $key
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @throws Exception
     */
    public function delete($key)
    {
        if ($this->checkTicketExists($key) === false) {
            return $this->app->json(['error' => 'Failed to reject ticket. Ticket doesn\'t exists.'], 500);
        }

        if ($this->checkDateToRemove($key) === false) {
            return $this->app->json(['error' => 'Failed to reject ticket. Out of 1 hour period before seance.'], 500);
        }

        if (!empty($key)) {
            $sql = "delete from ticket_order where `key` = '$key'";
            $this->app['db']->beginTransaction();
            try {
                $this->app['db']->executeQuery($sql);
                $this->app['db']->commit();

                return $this->app->json(['key' => $key, 'msg' => 'Ticket order was deleted.']);
            } catch (Exception $e) {
                $this->app['db']->rollback();
                throw $e;
            }
        }

        return $this->app->json(['error' => 'Failed to reject ticket.'], 500);
    }

}