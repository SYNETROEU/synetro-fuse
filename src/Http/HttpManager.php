<?php

declare(strict_types=1);

namespace Synetro\Fuse\Http;

use Illuminate\Http\Client\Factory as HttpClient;

class HttpManager
{
    public function __construct(
        protected HttpClient $http,
    ) {}

    public function get(string $url, array $query = []): Response
    {
        return $this->http->get($url, $query);
    }

    public function post(string $url, array $data = []): Response
    {
        return $this->http->post($url, $data);
    }

    public function put(string $url, array $data = []): Response
    {
        return $this->http->put($url, $data);
    }

    public function delete(string $url): Response
    {
        return $this->http->delete($url);
    }
}
