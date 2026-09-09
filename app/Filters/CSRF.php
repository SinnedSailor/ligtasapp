<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Security\Security;

class CSRF implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        /** @var Security $security */
        $security = service('security');

        try {
            $security->verify($request);
        } catch (SecurityException $e) {
            $isAjax = $request->isAJAX() || $request->hasHeader('X-Requested-With');
            
            $secConfig = config('Security');
            $posted = $request->getPost($secConfig->tokenName);
            $header = $request->header($secConfig->headerName)?->getValue();
            $sessionHash = session()->get($secConfig->tokenName);

            log_message('error', sprintf(
                '[CSRF REJECTED] URI: %s | AJAX: %s | Posted: %s | Header: %s | Session Hash: %s | Session ID: %s',
                (string) $request->getUri()->getPath(),
                $isAjax ? 'YES' : 'NO',
                $posted ?? 'NULL',
                $header ?? 'NULL',
                $sessionHash ?? 'NULL',
                session_id()
            ));

            if ($isAjax) {
                return service('response')->setJSON([
                    'success'    => false,
                    'csrf_error' => true,
                    'message'    => 'Security token expired or mismatch. A new token has been generated. Please try again.',
                    'csrf_token' => csrf_hash(),
                ])->setStatusCode(403);
            }

            if ($security->shouldRedirect()) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            throw $e;
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
