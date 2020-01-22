<?php

namespace blacksenator\NameParser\Mapper;

use blacksenator\NameParser\Language\English;
use blacksenator\NameParser\Part\Salutation;
use blacksenator\NameParser\Part\Firstname;
use blacksenator\NameParser\Part\Lastname;

class SalutationMapperTest extends AbstractMapperTest
{
    /**
     * @return array
     */
    public function provider()
    {
        return [
            [
                'input' => [
                    'Mr.',
                    'Pan',
                ],
                'expectation' => [
                    new Salutation('Mr.', 'Mr.'),
                    'Pan',
                ],
            ],
            [
                'input' => [
                    'Mr',
                    'Peter',
                    'Pan',
                ],
                'expectation' => [
                    new Salutation('Mr', 'Mr.'),
                    'Peter',
                    'Pan',
                ],
            ],
            [
                'input' => [
                    'Mr',
                    new Firstname('James'),
                    'Miss',
                ],
                'expectation' => [
                    new Salutation('Mr', 'Mr.'),
                    new Firstname('James'),
                    'Miss',
                ],
            ],
        ];
    }

    protected function getMapper()
    {
        $english = new English();

        return new SalutationMapper($english->getSalutations());
    }
}
