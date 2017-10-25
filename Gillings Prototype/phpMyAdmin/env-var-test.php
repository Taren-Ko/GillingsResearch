<?php
/**
 * Created by patric
 * Date: 10/04/2017
 * Time: 04:15 PM
 */


echo getenv(strtoupper(str_replace(‘-‘, ‘_’, getenv(‘DATABASE_SERVICE_NAME’))).’_SERVICE_HOST’);

