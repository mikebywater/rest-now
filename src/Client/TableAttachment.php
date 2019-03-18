<?php
/**
 * Created by PhpStorm.
 * User: satishtamang
 * Date: 11/03/19
 * Time: 11:10
 */

namespace Now\Client;


class TableAttachment
{
    protected $client;
    protected $headers;
    protected $findHeaders;

    public function __construct(Auth $auth)
    {
        $authToken = $auth->getToken();

        $this->headers =  [
            'Authorization' => 'Bearer ' . $authToken,
            'Accept'        => 'application/json',
        ];
        $this->findHeaders = [
            'Authorization' => 'Bearer ' . $authToken,
            'Accept'        => '*/*'
        ];

        $this->client = $auth->client;
    }

    public function find($sysId){

        $response = $this->client->get('/api/now/attachment/' . $sysId . '/file', ['headers' => $this->findHeaders]);
        return $response->getBody()->getContents();
    }

    public function findMeta($sysId){
        $response = $this->client->get('api/now/attachment/'.$sysId , ['headers' => $this->headers]);
        return json_decode($response->getBody());
    }

    public function upload($table, $data, $id)
    {
        $response = $this->client->post('/api/now/attachment/file?table_name=' . $table . '&table_sys_id='.$id.'&file_name='.$data['filename'],[
            'headers' => array_merge($this->headers, ['Content-Type' => $data['mimeType']]),
            'body' => $data['contents']
        ]);
        return json_decode($response->getBody());
    }

    public function delete($sys_id){
        $response = $this->client->get('/api/now/attachment/'.$sys_id, ['headers' => $this->headers]);
        return json_decode($response->getBody());
    }

}