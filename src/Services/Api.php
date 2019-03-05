<?php

namespace Submtd\LaravelApi\Services;

use Illuminate\Support\Facades\Response;

class Api
{
    /**
     * @var $data
     * stores data that is returned to the client
     */
    protected $data = [];

    /**
     * @var $errors
     * stores errors
     */
    protected $errors = [];

    /**
     * @var $status
     * http status code
     */
    protected $status = 200;

    /**
     * set the data property
     * @param array|object $data
     * @return Api
     */
    public function setData($data)
    {
        if (is_array($data)) {
            $this->data = $data;
            return $this;
        }
        if (!method_exists($data, 'toArray')) {
            throw new \Exception('$data must be an array or an object implementing the toArray method.', 400);
        }
        $this->data = $data->toArray();
        return $this;
    }

    /**
     * add a data value
     * @param string $key - item key
     * @param mixed $value - item value
     * @return Api
     */
    public function addData(string $key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * get the data property
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * add an error to the errors property
     * @param mixed $code
     * @param string $description
     * @return Api
     */
    public function addError($code, string $description)
    {
        $this->errors[] = [
            'status' => (string) $code,
            'title' => config('laravel-api.errorTitles.' . $code, 'Unknown error.'),
            'detail' => $description,
        ];
        $this->setStatus($code);
        return $this;
    }

    /**
     * get the errors property
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * set the status code
     * @param mixed $status
     * @return Api
     */
    public function setStatus($status)
    {
        if (!is_numeric($status)) {
            return $this;
        }
        if ($status < 100 || $status > 599) {
            return $this;
        }
        $this->status = (int) $status;
        return $this;
    }

    /**
     * get the status code
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * respond
     * @return Response
     */
    public function respond()
    {
        if (!empty($this->getErrors())) {
            return Response::json([
                'errors' => $this->getErrors(),
            ], $this->getStatus());
        }
        return Response::json([
            'data' => $this->getData(),
        ], $this->getStatus());
    }
}
