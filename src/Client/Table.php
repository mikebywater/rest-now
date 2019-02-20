<?php


namespace Now\Client;


class Table
{

    public $client;

    public function __construct(Auth $auth)
    {

        $this->headers =  [
          'Authorization' => 'Bearer ' . $auth->getToken(),
           'Accept'        => 'application/json',
       ];
        $this->client = $auth->client;
    }

    public function all($table)
    {
        $response = $this->client->get('/api/now/table/' . $table, ['headers' => $this->headers]);

        return json_decode($response->getBody());
    }

    public function find($table,$id)
    {
        $response = $this->client->get('/api/now/table/' . $table . '/' . $id, ['headers' => $this->headers]);

        return json_decode($response->getBody());
    }

    public function create($table,$data)
    {
        $response = $this->client->post('/api/now/table/' . $table, ['headers' => $this->headers, 'body' => json_encode($data)]);
        return json_decode($response->getBody());
    }

    public function update($table,$data, $id)
    {
        $response = $this->client->patch('/api/now/table/'  . $table . '/' . $id, ['headers' => $this->headers, 'body' => json_encode($data)]);
        return json_decode($response->getBody());
    }

    public function delete($table, $id)
    {
        $response = $this->client->delete('/api/now/table/'  . $table . '/' . $id, ['headers' => $this->headers]);
        return json_decode($response->getBody());
    }

    public function where($table, $field, $value, $operator = '=')
    {
        $response = $this->client->get('/api/now/table/'  . $table . '/?sysparm_query=' . $field . $operator . $value, ['headers' => $this->headers]);
        return json_decode($response->getBody());
    }


}