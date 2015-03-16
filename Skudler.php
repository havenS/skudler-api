<?php 
// Login
/**
* 
*/
class Skudler
{
    public $error;

    protected $server = 'http://localhost:3000/api/';
    protected $auth;
    protected $email;
    protected $password;

    public function __construct($email, $password)
    {
        $this->email    = $email;
        $this->password = $password;
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

    public function getTriggers($site, $onlyResponse = true)
    {
        $resource = 'triggers';

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

    public function addSubscriber($triggerId, $subscriberInfo, $onlyResponse = true)
    {
        $resource = 'subscribers';
        $data = array(
            'triggerId'         => $triggerId,
            'firstname'         => isset($subscriberInfo['firstname'])      ? $subscriberInfo['firstname']      : '',
            'lastname'          => isset($subscriberInfo['lastname'])       ? $subscriberInfo['lastname']       : '',
            'email'             => isset($subscriberInfo['email'])          ? $subscriberInfo['email']          : '',
            'reference_date'    => isset($subscriberInfo['reference_date']) ? $subscriberInfo['reference_date'] : ''
        );

        return $this->getResource('POST', $resource, $onlyResponse, $data);
    }

    public function addSubscription($triggerId, $subscriberInfo, $onlyResponse = true)
    {
        $resource = 'subscriptions';
        $data = array(
            'triggerId'         => $triggerId,
            'firstname'         => isset($subscriberInfo['firstname'])      ? $subscriberInfo['firstname']      : '',
            'lastname'          => isset($subscriberInfo['lastname'])       ? $subscriberInfo['lastname']       : '',
            'email'             => isset($subscriberInfo['email'])          ? $subscriberInfo['email']          : '',
            'reference_date'    => isset($subscriberInfo['reference_date']) ? $subscriberInfo['reference_date'] : ''
        );

        return $this->getResource('POST', $resource, $onlyResponse, $data);
    }





    protected function getResource($method, $resource, $onlyResponse = true, $data = array())
    {
        if(empty($this->auth)) {

            $loginCall = $this->call('POST', 'login');

            if (isset($loginCall->status) && $loginCall->status == 'success') {
                $this->auth = array(
                    'token'     => $loginCall->data->authToken,
                    'userId'    => $loginCall->data->userId
                );
            } else {
                return $loginCall->status;
            }

        }

        $call = $this->call($method, $resource, $data);
        if(isset($call->status)) {
            if ($call->status == 'success'){
                return $onlyResponse ? $call->data : $call;
            } else {
                $this->error = $call->message;
            }
        }

    }

    protected function call($method, $resource, $data = array())
    {
        $header = "Content-type: application/x-www-form-urlencoded\r\n";

        if(empty($this->auth)){
            $data['user']       = $this->email;
            $data['password']   = $this->password;
        }else {
            $header .=  "X-User-Id: {$this->auth['userId']}\r\n" .
                        "X-Auth-Token: {$this->auth['token']}\r\n";
        }

        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => $method,
                'content' => http_build_query($data),
            ),
        );

        $context        = stream_context_create($options);
        $jsonResponse   = file_get_contents($this->server.$resource, false, $context);

        return json_decode($jsonResponse);
    }

}