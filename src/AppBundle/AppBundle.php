<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle.
 */
class AppBundle extends Bundle
{
    /**
     * @SWG\Info(
     *   title="Warehouse API",
     *   description="REST API which allows to manage small warehouse",
     *   version="1.0.0",
     *   @SWG\Contact(
     *     name="Lunches API Team",
     *   )
     * )
     * @SWG\Swagger(
     *   basePath="/",
     *   schemes={"http","https"},
     *   produces={"application/json"},
     *   consumes={"application/json"},
     * )
     * @SWG\Definition(
     *     definition="Error",
     *     required={"code", "message"},
     *     @SWG\Property(
     *         property="code",
     *         type="integer",
     *     ),
     *     @SWG\Property(
     *         property="message",
     *         type="string"
     *     ),
     *     @SWG\Property(
     *         property="errors",
     *         type="array",
     *         @SWG\Items(ref="#definitions/Error")
     *     )
     * )
     */
}
