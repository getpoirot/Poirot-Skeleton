<?php
namespace Module\Foundation\HttpSapi\Response;

use Poirot\Http\HttpResponse;

class ResponseJson
    extends HttpResponse
{
    /**
     * ResponseJson constructor.
     *
     * @param array|\Traversable $data
     * @param array              $headers
     */
    function __construct($data, array $headers = array())
    {
        if ($data instanceof \Traversable)
            $data = \Poirot\Std\cast($data)->toArray();

        if (!is_array($data))
            throw new \InvalidArgumentException(sprintf(
                'Data must be array or instance of \Traversable; given (%s).'
                , \Poirot\Std\flatten($data)
            ));

        $headers['Content-Type'] = 'application/json';
        $this->setHeaders($headers);
        $this->setStatusCode(200);

        $body = json_encode($data);
        $this->setBody($body);

        parent::__construct();
    }
}
