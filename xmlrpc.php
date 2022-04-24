<?php

Class XmlRpc
{
    public $uid = null;
    public $error = false;

    public function __construct(
        $url, $database, $username, $password, $company_id, $debug = false)
    {
        $this->url = $url;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->company_id = $company_id;
        $this->debug = $debug;
        $url_auth = $this->url.'/xmlrpc/2/common';
        $this->DebugDump(
            array(
                'url' => $url_auth,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'company_id' => $company_id
            ),
            'request'
        );
        $this->encoding = array(
            'output_type' => 'xml',
            'verbosity' => 'pretty',
            'escaping' => array('markup'),
            'version' => 'xmlrpc',
            'encoding' => 'utf-8'
        );
        $request = xmlrpc_encode_request(
            'authenticate',
            array(
                $this->database,
                $this->username,
                $this->password,
                array()
            ),
            $this->encoding
        );
        $response = $this->DoCurl($url_auth, $request);
        $response = xmlrpc_decode($response, 'utf-8');
        $this->DebugDump($response, 'response');
        if (isset($response['faultString'])) {
            $this->HandleFaultString($response);
            return false;
        }
        if (empty($response)) {
            $this->error = 'Authorization failed';
        }
        $this->uid = $response;
        return $this->uid;
    }

    public function ExecKw($model, $method, $parm_list, $parm_dict = [])
    {
        $url_exec = $this->url.'/xmlrpc/2/object';
        if ($this->company_id &&
            !isset($parm_dict['context']['allowed_company_ids']))
        {
            $parm_dict['context']['allowed_company_ids'] =
                [(int)$this->company_id];
        }
        $this->DebugDump(
            array(
                'model' => $model, 
                'method' => $method,
                'parm_list' => $parm_list,
                'parm_dict' => $parm_dict
            ),
            'request'
        );
        if ($model == null) {
            // Just get the UID
            return $this->uid;
        }
        $request = xmlrpc_encode_request(
            'execute_kw',
            array(
                $this->database,
                $this->uid,
                $this->password,
                $model,
                $method,
                $parm_list,
                $parm_dict
            ),
            $this->encoding
        );
        $response = $this->DoCurl($url_exec, $request);
        $response = xmlrpc_decode($response, 'utf-8');
        $this->DebugDump($response, 'response');
        if (isset($response['faultString'])) {
            $this->HandleFaultString($response);
            $response = [];
        } else {
            $this->oa_fault_code = 0;
        }
        return $response;
    }

    public function HandleFaultString($response)
    {
        $fault = explode("\n", $response['faultString']);
        foreach ($fault as $f) {
            if ($f == 'Traceback (most recent call last):') {
                continue;
            }
            if ($f == 'During handling of the above exception, another exception occurred:') {
                continue;
            }
            if ($f == '' || substr($f, 0, 1) == ' ') {
                continue;
            }
            $this->error = $f;
        }
    }

    public function DoCurl($url, $request)
    {

        $header[] = "Content-type: text/xml";
        $header[] = "Content-length: ".strlen($request);

        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $data = curl_exec($ch);      
        $info = curl_getinfo($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            if ($error) {
                $this->error = $error;
            } else {
                $this->error = "Connection failed";
            }
            curl_close($ch);
            return false;
        } else {
            curl_close($ch);
            return $data;
        }
    }

    public function DebugDump($data, $text = '')
    {
        if ($this->debug) {
            if ($text) {
                $text .= ":\n";
            }
            printf("<pre>%s%s</pre>\n", $text, print_r($data, true));
        }
    }

    public function Cookie($name)
    {
        if (isset($_COOKIE[$name])) {
            $val = $_COOKIE[$name];
        } else {
            $val = '';
        }
        return $val;
    }
}
