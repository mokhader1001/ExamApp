<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('setting');

        // Only block if Maintenance Mode is ON and user is a Student
        // Teachers should always have access to manage the site
        if (get_setting('maintenance_mode') === 'on' && session()->get('role') === 'student') {
            return redirect()->to(site_url('maintenance'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
