<?php

namespace Findologic\Search\Example;

abstract class ExampleBaseItem
{
    const DEFAULT_USER_GROUP = '';
    const SPECIFIC_USER_GROUP = 'LNrLF7BRVJ0toQ==';

    /**
     * @var string
     */
    public $id;

    /**
     * @var array[]
     */
    public $ordernumbers;

    /**
     * @var string[]
     */
    public $names;

    /**
     * @var string[]
     */
    public $summaries;

    /**
     * @var string[]
     */
    public $descriptions;

    /**
     * @var float[]
     */
    public $prices;

    /**
     * @var string[]
     */
    public $urls;

    /**
     * @var array[]
     */
    public $keywords;

    /**
     * @var int[]
     */
    public $bonuses;

    /**
     * @var int[]
     */
    public $salesFrequencies;

    /**
     * @var string[]
     */
    public $dateAddeds;

    /**
     * @var int[]
     */
    public $sorts;

    /**
     * @var string[]
     */
    public $userGroups;

    /**
     * @var array[]
     */
    public $images;

    /**
     * @var array[]
     */
    public $attributes;

    /**
     * @var array[]
     */
    public $properties;
}
