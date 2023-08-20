<?php

use Phalcon\Mvc\Controller;

class ErrorController extends Controller
{
    
    public function notFoundAction()
    {
        $this->response->setStatusCode(404, 'Not Found');
        $this->response->redirect('/');
    }
}
