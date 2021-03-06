<?php

namespace blacksenator\NameParser\Mapper;

use blacksenator\NameParser\Part\Company;

/**
 * Mapper to identify a string as a company name
 * @author Volker Püschel <kuffy@anasco.de>
 * @copyright 2019 Volker Püschel
 * @license MIT
 */

class CompanyMapper extends AbstractMapper
{
    protected $companies = [];

    public function __construct(array $companies)
    {
        $this->companies = $this->sortArrayDescending($companies);
    }

    /**
     * map companies in the full name
     *
     * @param array $parts = the fullname
     * @return array
     */
    public function map(array $parts): array
    {
        if (count($parts) <> 1) {
            return [];
        }

        foreach ($this->companies as $key => $company) {
            if (strpos($parts[0], $company) !== false ||
                strpos($parts[0], $key) !== false) {
                return [new Company($parts[0])];
            }
        }

        return [];
    }
}
