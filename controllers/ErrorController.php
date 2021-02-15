<?php
/**
 * Author: --=={{ *** MARSEL ALAMDAR *** }}==--
 * Date: 28/08/2017
 * Test APP For BeeJee
 *
 */
class ErrorController extends Controller
{
    public function process($params)
    {
        // HTTP header
        header("HTTP/1.0 404 Not Found");

        // HTML header
        $this->head['title'] = 'Error 404';

        // Sets the template
        $this->view = 'error';
    }
}