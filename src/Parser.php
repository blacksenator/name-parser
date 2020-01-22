<?php

namespace TheIconic\NameParser;

use TheIconic\NameParser\Language\German;
use TheIconic\NameParser\Language\English;
use TheIconic\NameParser\Mapper\NicknameMapper;
use TheIconic\NameParser\Mapper\SalutationMapper;
use TheIconic\NameParser\Mapper\SuffixMapper;
use TheIconic\NameParser\Mapper\InitialMapper;
use TheIconic\NameParser\Mapper\LastnameMapper;
use TheIconic\NameParser\Mapper\FirstnameMapper;
use TheIconic\NameParser\Mapper\MiddlenameMapper;
use TheIconic\NameParser\Mapper\CompanyMapper;
use TheIconic\NameParser\Mapper\ExtensionMapper;
use TheIconic\NameParser\Mapper\MultipartMapper;

class Parser
{
    /**
     * @var string
     */
    protected $whitespace = " \r\n\t";

    /**
     * @var array
     */
    protected $mappers = [];

    /**
     * @var array
     */
    protected $languages = [];

    /**
     * @var array
     */
    protected $nicknameDelimiters = [];

    /**
     * @var int
     */
    protected $maxSalutationIndex = 0;

    /**
     * @var int
     */
    protected $maxCombinedInitials = 2;

    /**
     * characters similar to apostrophes and the typographic apostroph are mapped to U+0027
     * @var string
     */
    protected $apostrophMapping = [
        "\x60" => "\x27",               // U+0060 GRAVE ACCENT
        "\xc2\xb4" => "\x27",           // U+00B4 ACUTE ACCENT
        "\xca\xb9" => "\x27",           // U+02B9 MODIFIER LETTER PRIME
        "\xca\xbb" => "\x27",           // U+02BB MODIFIER LETTER TURNED COMMA Hawaiian and for the transliteration of Arabic and Hebrew
        "\xca\xbc" => "\x27",           // U+02BC MODIFIER LETTER APOSTROPHE
        "\xca\xbd" => "\x27",           // U+02BD MODIFIER LETTER REVERSED COMMA
        "\xca\xbe" => "\x27",           // U+02BE MODIFIER LETTER RIGHT HALF RING Arabic and Hebrew
        "\xca\xbf" => "\x27",           // U+02BF MODIFIER LETTER LEFT HALF RING Arabic and Hebrew
        "\xcb\x88" => "\x27",           // U+02C8 MODIFIER LETTER VERTICAL LINE Stress accent or dynamic accent
        "\xcb\x8a" => "\x27",           // U+02CA MODIFIER LETTER ACUTE ACCENT
        "\xcd\xb4" => "\x27",           // U+0374 GREEK NUMERAL SIGN Also known as Greek dexia keraia
        "\xce\x84" => "\x27",           // U+0384 GREEK TONOS
        "\xd5\x9a" => "\x27",           // U+055A ARMENIAN APOSTROPHE
        "\xe1\xbe\xbd" => "\x27",       // U+1FBD GREEK KORONIS
        "\xe1\xbe\xbf" => "\x27",       // U+1FBF GREEK PSILI
        "\xe2\x80\x98" => "\x27",       // U+2018 LEFT SINGLE QUOTATION MARK
        "\xe2\x80\x99" => "\x27",       // U+2019 RIGHT SINGLE QUOTATION MARK
        "\xe2\x80\x9b" => "\x27",       // U+201B SINGLE HIGH-REVERSED-9 QUOTATION MARK
        "\xe2\x80\xb2" => "\x27",       // U+2032 PRIME
        "\xe2\x80\xb5" => "\x27",       // U+2035 REVERSED PRIME
        "\xea\x9e\x8b" => "\x27",       // U+A78B LATIN CAPITAL LETTER SALTILLO
        "\xea\x9e\x8c" => "\x27",       // U+A78C LATIN SMALL LETTER SALTILLO
        "\xef\xbc\x87" => "\x27",       // U+FF07 FULLWIDTH APOSTROPHE
    ];

    public function __construct(array $languages = [])
    {
        if (empty($languages)) {
            $languages = [new German()];
        }

        $this->languages = $languages;
    }

    /**
     * split full names into the following parts:
     * - prefix / salutation  (Mr., Mrs., etc)
     * - given name / first name
     * - middle initials
     * - surname / last name
     * - suffix (II, Phd, Jr, etc)
     * - extension (Germany: nobility predicate is part of lastname)
     * - title (Germany: academic titles are usually used as name parts between salutation and given name)
     * - company (the string contains typical characteristics for a company name and is returned identically)
     *
     * @param string $name
     * @return Name
     */
    public function parse($name): Name
    {
        $name = $this->normalize($name);

        $segments = explode(',', $name);

        if (1 < count($segments)) {
            return $this->parseSplitName($segments[0], $segments[1], $segments[2] ?? '');
        } else {
            $mapped = $this->getCompany($name);
            if (count($mapped)) {
                return new Name($mapped);
            }
        }

        $parts = explode(' ', $name);

        foreach ($this->getMappers() as $mapper) {
            $parts = $mapper->map($parts);
        }

        return new Name($parts);
    }

    /**
     * handles split-parsing of comma-separated name parts
     *
     * @param string $first - the name part left of the comma
     * @param string $second - the name part right of the comma
     * @param string $third
     * @return Name
     */
    protected function parseSplitName($first, $second, $third): Name
    {
        $parts = array_merge(
            $this->getFirstSegmentParser()->parse($first)->getParts(),
            $this->getSecondSegmentParser()->parse($second)->getParts(),
            $this->getThirdSegmentParser()->parse($third)->getParts()
        );

        return new Name($parts);
    }

    /**
     * @return Parser
     */
    protected function getFirstSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new ExtensionMapper($this->getSamples('Extensions')),
            new MultipartMapper($this->getSamples('Titles'), 'title'),
            new MultipartMapper($this->getSamples('LastnamePrefixes'), 'prefix'),
            new SalutationMapper($this->getSamples('Salutations'), $this->getMaxSalutationIndex()),
            new SuffixMapper($this->getSamples('Suffixes'), false, 2),
            new LastnameMapper($this->getSamples('LastnamePrefixes'), true),
            new FirstnameMapper(),
            new MiddlenameMapper(),
        ]);

        return $parser;
    }

    /**
     * @return Parser
     */
    protected function getSecondSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new ExtensionMapper($this->getSamples('Extensions')),
            new MultipartMapper($this->getSamples('Titles'), 'title'),
            new MultipartMapper($this->getSamples('LastnamePrefixes'), 'prefix'),
            new SalutationMapper($this->getSamples('Salutations'), $this->getMaxSalutationIndex()),
            new SuffixMapper($this->getSamples('Suffixes'), true, 1),
            new NicknameMapper($this->getNicknameDelimiters()),
            new InitialMapper($this->getMaxCombinedInitials(), true),
            new FirstnameMapper(),
            new MiddlenameMapper(true),
        ]);

        return $parser;
    }

    protected function getThirdSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new SuffixMapper($this->getSamples('Suffixes'), true, 0),
        ]);

        return $parser;
    }

    /**
     * get the mappers for this parser
     *
     * @return array
     */
    public function getMappers(): array
    {
        if (empty($this->mappers)) {
            $this->setMappers([
                new ExtensionMapper($this->getSamples('Extensions')),
                new MultipartMapper($this->getSamples('Titles'), 'title'),
                new MultipartMapper($this->getSamples('LastnamePrefixes'), 'prefix'),
                new NicknameMapper($this->getNicknameDelimiters()),
                new SalutationMapper($this->getSamples('Salutations'), $this->getMaxSalutationIndex()),
                new SuffixMapper($this->getSamples('Suffixes')),
                new InitialMapper($this->getMaxCombinedInitials()),
                new LastnameMapper($this->getSamples('LastnamePrefixes')),
                new FirstnameMapper(),
                new MiddlenameMapper(),
            ]);
        }

        return $this->mappers;
    }

    /**
     * get name as company if parts matches company identifiers
     *
     * @param string $name
     * @return array
     */
    protected function getCompany(string $name): array
    {
        $mapper = new CompanyMapper($this->getSamples('Companies'));

        return $mapper->map([$name]);
    }

    /**
     * set the mappers for this parser
     *
     * @param array $mappers
     * @return Parser
     */
    public function setMappers(array $mappers): Parser
    {
        $this->mappers = $mappers;

        return $this;
    }

    /**
     * normalize the name
     *
     * @param string $name
     * @return string
     */
    protected function normalize(string $name): string
    {
        $whitespace = $this->getWhitespace();

        $name = trim(strtr($name, $this->apostrophMapping));

        return preg_replace('/[' . preg_quote($whitespace) . ']+/', ' ', $name);
    }

    /**
     * get a string of characters that are supposed to be treated as whitespace
     *
     * @return string
     */
    public function getWhitespace(): string
    {
        return $this->whitespace;
    }

    /**
     * set the string of characters that are supposed to be treated as whitespace
     *
     * @param string $whitespace
     * @return Parser
     */
    public function setWhitespace($whitespace): Parser
    {
        $this->whitespace = $whitespace;

        return $this;
    }

    /**
     * @return array
     */
    protected function getSamples(string $sampleName): array
    {
        $samples = [];
        $method = sprintf('get%s', $sampleName);
        foreach ($this->languages as $language) {
            $samples += call_user_func_array([$language, $method], []);
        }

        return $samples;
    }

    /**
     * @return array
     */
    public function getNicknameDelimiters(): array
    {
        return $this->nicknameDelimiters;
    }

    /**
     * @param array $nicknameDelimiters
     * @return Parser
     */
    public function setNicknameDelimiters(array $nicknameDelimiters): Parser
    {
        $this->nicknameDelimiters = $nicknameDelimiters;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxSalutationIndex(): int
    {
        return $this->maxSalutationIndex;
    }

    /**
     * @param int $maxSalutationIndex
     * @return Parser
     */
    public function setMaxSalutationIndex(int $maxSalutationIndex): Parser
    {
        $this->maxSalutationIndex = $maxSalutationIndex;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCombinedInitials(): int
    {
        return $this->maxCombinedInitials;
    }

    /**
     * @param int $maxCombinedInitials
     * @return Parser
     */
    public function setMaxCombinedInitials(int $maxCombinedInitials): Parser
    {
        $this->maxCombinedInitials = $maxCombinedInitials;

        return $this;
    }
}
