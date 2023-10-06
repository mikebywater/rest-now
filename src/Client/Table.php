<?php


namespace Now\Client;


class Table
{

    public $client;
    protected $headers;
    protected $auth;

    protected $whereFields;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->client = $auth->client;
    }

    protected function getHeaders()
    {
        if (!$this->headers) {
            $this->headers =  [
              'Authorization' => 'Bearer ' . $this->auth->getToken(),
               'Accept'        => 'application/json',
            ];
        }
        return $this->headers;
    }

    public function all($table)
    {
        $response = $this->client->get('/api/now/table/' . $table, ['headers' => $this->getHeaders()]);

        return json_decode($response->getBody());
    }

    public function find($table,$id)
    {
        $response = $this->client->get('/api/now/table/' . $table . '/' . $id, ['headers' => $this->getHeaders()]);

        return json_decode($response->getBody());
    }

    public function create($table,$data)
    {
        $response = $this->client->post('/api/now/table/' . $table, ['headers' => $this->getHeaders(), 'body' => json_encode($data)]);
        return json_decode($response->getBody());
    }

    public function update($table,$data, $id)
    {
        $response = $this->client->patch('/api/now/table/'  . $table . '/' . $id, ['headers' => $this->getHeaders(), 'body' => json_encode($data)]);
        return json_decode($response->getBody());
    }

    public function delete($table, $id)
    {
        $response = $this->client->delete('/api/now/table/'  . $table . '/' . $id, ['headers' => $this->getHeaders()]);
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
        $response = $this->client->get($query, ['headers' => $this->getHeaders()]);
        unset($this->whereFields[$table]);
        return json_decode($response->getBody());
    }

    public function where($table, $field, $value, $operator = '=', $limit = 1)
    {
        $response = $this->client->get('/api/now/table/'  . $table .
            '?sysparm_query=' . $field . $operator . $value .
            '&sysparm_limit=' . $limit, ['headers' => $this->getHeaders()]);

        return json_decode($response->getBody());
    }

    public function whereWithDisplayValues($table, $field, $value, $operator = '=', $limit = 1)
    {
        $response = $this->client->get(
            '/api/now/table/' . $table .
            '?sysparm_query=' . $field . $operator . $value .
            '&sysparm_display_value=true' .
            '&sysparm_limit=' . $limit,
            ['headers' =>  $this->getHeaders()]
        );
        return json_decode($response->getBody());
    }

}
