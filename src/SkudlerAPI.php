<?php
namespace Skudler;
/**
 *
 */
class SkudlerAPI
{

    protected $server = 'http://localhost:3000/api/';
//    protected $server = 'http://www.skudler.com/api/';
    protected $apiKey;
    protected $token;

    public function __construct($apiKey = '', $token = '')
    {
        $this->apiKey   = $apiKey;
        $this->token    = $token;
    }

    public function getSites($onlyResponse = true)
    {
        $resource = 'sites';

        return $this->getResource('GET', $resource, $onlyResponse);
    }

    public function getEvents($site, $onlyResponse = true)
    {
        $resource = 'events';

        $data = array('siteId' => $site);

        return $this->getResource('GET', $resource, $onlyResponse, $data);
    }

    public function getActions($site, $onlyResponse = true)
    {
        $resource = 'actions';

        $data = array('siteId' => $site);

        return $this->getResource('GET', $resource, $onlyResponse, $data);
    }

    public function getSchedules($onlyResponse = true)
    {
        $resource = 'schedules';

        return $this->getResource('GET', $resource, $onlyResponse);
    }

    public function getSubscribers($onlyResponse = true)
    {
        $resource = 'subscribers';

        return $this->getResource('GET', $resource, $onlyResponse);
    }

    public function addSubscription($eventId, $subscriberInfo, $onlyResponse = true)
    {
        $resource = 'subscriptions';
        $data = array(
            'eventId'           => $eventId,
            'reference_date'    => isset($subscriberInfo['reference_date']) ? $subscriberInfo['reference_date'] : date('Y-m-d H:i:s')
        );

        foreach($subscriberInfo as $key => $value)
            $data[$key] = $value;

        return $this->getResource('POST', $resource, $onlyResponse, $data);
    }

    public function deleteSubscription($eventId, $subscriberInfo, $onlyResponse = true)
    {
        $resource = 'subscriptions';
        $data = array(
            'eventId'       => $eventId,
            'remove'   => true
        );

        foreach($subscriberInfo as $key => $value)
            $data[$key] = $value;

        return $this->getResource('POST', $resource, $onlyResponse, $data);
    }





    protected function getResource($method, $resource, $onlyResponse = true, $data = array())
    {
        $call = $this->call($method, $resource, $data);

        if(isset($call->status)) {
            if ($call->status == 'success'){
                return $onlyResponse ? $call->data : $call;
            } else {
                return $onlyResponse ? $call->message : $call;
            }
        }

    }

    protected function call($method, $resource, $data = array())
    {
        $header = "Content-type: application/x-www-form-urlencoded\r\n";

        $header .=  "X-Api-Key: {$this->apiKey}\r\n" .
            "X-Api-Token: {$this->token}\r\n";

        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => $method,
                'content' => http_build_query($data),
            ),
        );

        $context        = stream_context_create($options);
        $jsonResponse   = @file_get_contents($this->server.$resource, false, $context);

        return json_decode($jsonResponse);
    }

}