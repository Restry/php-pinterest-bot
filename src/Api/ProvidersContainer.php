<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Exceptions\WrongProvider;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\Providers\Core\ProviderWrapper;

class ProvidersContainer
{
    const PROVIDERS_NAMESPACE = 'seregazhuk\\PinterestBot\\Api\\Providers\\';

    /**
     * References to the request that travels
     * through the application.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * A array containing the cached providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Gets provider object by name. If there is no such provider
     * in providers array, it will try to create it, then save
     * it, and then return.
     *
     * @param string $provider
     *
     * @throws WrongProvider
     *
     * @return Provider
     */
    public function getProvider($provider)
    {
        $provider = strtolower($provider);

        // Check if an instance has already been initiated. If not
        // build it and then add to the providers array.
        if (!isset($this->providers[$provider])) {
            $this->addProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * Creates provider by class name, and if success saves
     * it to providers array. Provider class must exist in PROVIDERS_NAMESPACE.
     *
     * @param string $provider
     * @throws WrongProvider
     */
    protected function addProvider($provider)
    {
        $className = $this->resolveProviderClass($provider);

        $this->providers[$provider] = $this->buildProvider($className);
    }

    /**
     * Build Provider object.
     *
     * @param string $className
     * @throws WrongProvider
     * @return ProviderWrapper
     */
    protected function buildProvider($className)
    {
        $provider = new $className($this);

        return new ProviderWrapper($provider);
    }

    /**
     * Proxies call to Request object and returns message from
     * the error object.
     *
     * @return string|null
     */
    public function getLastError()
    {
        $error = $this->response->getLastError();

        if(isset($error['message'])) return $error['message'];

        if(isset($error['code'])) return $error['code'];

        return null;
    }

    /**
     * Simply proxies call to Response object.
     *
     * @return array|null
     */
    public function getClientInfo()
    {
        return $this->response->getClientInfo();
    }

    /**
     * Returns HttpClient object for setting user-agent string or
     * other CURL available options.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->request->getHttpClient();
    }

    /**
     * @param string $provider
     * @return string
     * @throws WrongProvider
     */
    protected function resolveProviderClass($provider)
    {
        $className = self::PROVIDERS_NAMESPACE . ucfirst($provider);

        $isProvider = !is_subclass_of($className, Provider::class, true);

        if (!class_exists($className) || $isProvider) {
            throw new WrongProvider("Provider $className not found.");
        }

        return $className;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
