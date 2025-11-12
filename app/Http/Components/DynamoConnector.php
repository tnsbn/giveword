<?php

namespace App\Http\Components;

use Aws\DynamoDb\DynamoDbClient;
use Exception;

use function env;

class DynamoConnector
{
    public static function onCloud(): bool
    {
        return !env('DYNAMODB_LOCAL');
    }

    /**
     * @throws Exception
     */
    public static function getDnm(): DynamoDbClient
    {
        try {
            return new DynamoDbClient(
                [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'table' => 'cache',
                'region' => env('AWS_DEFAULT_REGION'),
                ]
            );
        } catch (Exception $ex) {
            throw new Exception('Can not connect to DynamoDB');
        }
    }
}
