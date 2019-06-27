<?php

namespace App\Controller;

use Cake\Controller\Controller;
use TusPhp\Tus\Server as TusServer;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TusController extends Controller
{
    /**
     * Create tus server.
     *
     * @return HttpResponse
     * @throws \ReflectionException
     */
    public function server()
    {
        $server = new TusServer();

        $server
            ->setApiPath('/tus') // tus server endpoint.
            ->setUploadDir(WWW_ROOT . 'files'); // uploads dir, make sure it exists and is accessible.

        $response = $server->serve();

        return $response->send();
    }
}
