<?php

namespace blacksenator\NameParser\Mapper;

use blacksenator\NameParser\Part\AbstractPart;
use blacksenator\NameParser\Part\Title;
use blacksenator\NameParser\Part\LastnamePrefix;

/**
 * A generic mapper for name parts that consist of
 * multiple words (components)
 * This affects in Germany for example lastname prefixes
 * and (academic) titles, which are often consisting
 * of several words
 * See describing samples in: \Language\German.php:
 * LASTNAME_PREFIXES[], TITLES_DR[], OFFICIAL_TITLES[]
 * and JOB_TITLES[]
 *
 * @author Volker Püschel <kuffy@anasco.de>
 * @copyright 2019 Volker Püschel
 * @license MIT
 */

class MultipartMapper extends AbstractMapper
{
    protected $samples = [];
    protected $sampleType;
    protected $className;

    public function __construct(array $samples, string $sampleType)
    {
        $this->sampleType = $sampleType;

        if (strtolower($sampleType) == 'prefix') {
            $this->className = 'blacksenator\\NameParser\\Part\\Lastname' . ucfirst($sampleType);
        } else {
            $this->className = 'blacksenator\\NameParser\\Part\\' . ucfirst($sampleType);
        }

        $values = [];

        $samples = $this->sortArrayDescending($samples);
        foreach ($samples as $key => $sample) {       // fragmentation of the strings into words or abbreviations
            $fragments = explode(' ', $sample);
            $values[$key][$sampleType] = $sample;
            $values[$key]['fragments'] = $fragments;
        }
        $this->samples = $values;
    }

    /**
     * map composite name components in the parts array
     *
     * @param array $parts the name parts
     * @return array the mapped parts
     */
    public function map(array $parts): array
    {
        foreach ($this->samples as $sample) {
            $mappedParts = [];
            foreach ($sample['fragments'] as $fragment) {
                $result = array_search($fragment, $parts);
                if ($result !== false) {
                    $mappedParts[] = $result;
                } else {
                    continue(2);
                }
            }
            if (count($result = $this->mapParts($mappedParts, $parts))) {
                $parts = $result;                       // all sample fragments successful mapped to parts
                break;
            }
        }

        return $parts;
    }

    /**
     * map sample fragments to parts
     *
     * @param array $mappedParts
     * @param array $parts
     * @return array
     */
    public function mapParts(array $mappedParts, array $parts): array
    {
        $className = $this->className;
        foreach ($mappedParts as $key) {
            if ($parts[$key] instanceof AbstractPart) {
                return [];
            }
            $parts[$key] = new $className($parts[$key]);
        }

        return $parts;
    }

    /**
     * get sampleType
     *
     * @return string
     */
    public function getSampleType()
    {
        return $this->sampleType;
    }
}
