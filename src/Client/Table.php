<?php


namespace Now\Client;


class Table
{

    public $client;

    protected $whereFields;

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

    public function whereMultiple($table, $field, $value, $operator = '=', $joinOperator = '^')
    {
        if(empty($this->whereFields[$table])) {
            $this->whereFields[$table][] = ['field' => $field, 'operator' => $operator, 'value' => $value, 'joinOperator' => ''];
        } else {
            $this->whereFields[$table][] = ['field' => $field, 'operator' => $operator, 'value' => $value, 'joinOperator' => $joinOperator];
        }
        return $this;
    }

    public function whereResult($table, $limit = 1) {
        $query = '/api/now/table/'  . $table . '?sysparm_query=';
        foreach($this->whereFields[$table] as $condition) {
            $strCondition = $condition['joinOperator'] . $condition['field'] . $condition['operator'] . $condition['value'];
            $query .= $strCondition;
        }
        $query .= '&sysparm_limit=' . $limit;
        $response = $this->client->get($query , ['headers' => $this->headers]);
        unset($this->whereFields[$table]);
        return json_decode($response->getBody());
    }

    public function where($table, $field, $value, $operator = '=', $limit = 1)
    {
        $response = $this->client->get('/api/now/table/'  . $table .
            '?sysparm_query=' . $field . $operator . $value .
            '&sysparm_limit=' . $limit , ['headers' => $this->headers]);
        
        return json_decode($response->getBody());
    }


}