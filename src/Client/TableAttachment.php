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
    protected $auth;
    protected $headers;
    protected $findHeaders;

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

    protected function getFindHeaders()
    {
        if (!$this->findHeaders) {
            $this->findHeaders = [
                'Authorization' => 'Bearer ' . $this->auth->getToken(),
                'Accept'        => '*/*'
            ];
        }
        return $this->findHeaders;
    }

    public function find($sysId){

        $response = $this->client->get('/api/now/attachment/' . $sysId . '/file', ['headers' => $this->getFindHeaders()]);
        return $response->getBody()->getContents();
    }

    public function findMeta($sysId){
        $response = $this->client->get('api/now/attachment/'.$sysId , ['headers' => $this->getHeaders()]);
        return json_decode($response->getBody());
    }

    public function upload($table, $data, $id)
    {
        $response = $this->client->post('/api/now/attachment/file?table_name=' . $table . '&table_sys_id='.$id.'&file_name='.$data['filename'],[
            'headers' => array_merge($this->getHeaders(), ['Content-Type' => $data['mimeType']]),
            'body' => $data['contents']
        ]);
        return json_decode($response->getBody());
    }

    public function delete($sys_id){
        $response = $this->client->get('/api/now/attachment/'.$sys_id, ['headers' => $this->getHeaders()]);
        return json_decode($response->getBody());
    }

}
