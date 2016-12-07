<?php

namespace YHSPY\Essl;

class Exception extends \Exception
{
    const FILE_NOT_FOUND = 1001;

    const MALFORMED_CERTIFICATE = 2001;

    const CONNECTION_PROBLEM = 3001;

    const INVALID_HOST = 4001;
}