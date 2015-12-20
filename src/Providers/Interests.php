<?php

namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Request;

class Interests extends Provider
{
    /**
     * Follow interest by ID
     *
     * @param int $interestId
     * @return bool
     */
    public function follow($interestId)
    {
        $this->request->checkLoggedIn();

        $response = $this->request->followMethodCall(
            $interestId,
            Request::INTEREST_ENTITY_ID,
            UrlHelper::RESOURCE_FOLLOW_INTEREST
        );
        return $this->response->checkResponse($response);
    }

    /**
     * Unfollow interest by ID
     *
     * @param int $interestId
     * @return bool
     */
    public function unFollow($interestId)
    {
        $this->request->checkLoggedIn();

        $response = $this->request->followMethodCall(
            $interestId,
            Request::INTEREST_ENTITY_ID,
            UrlHelper::RESOURCE_UNFOLLOW_INTEREST
        );
        return $this->response->checkResponse($response);
    }
}
