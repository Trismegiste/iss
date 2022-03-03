<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\CellularAutomaton;

/**
 * A map for RPG
 */
class RpgMap implements SvgPrintable
{

    protected $cell;
    protected $layer = [];
    protected $title = 'Battle Map';
    protected $parameter = [];

    public function __construct(CellularAutomaton $cell)
    {
        $this->cell = $cell;
    }

    public function setTitle(string $str): void
    {
        $this->title = $str;
    }

    public function setParameters(array $param): void
    {
        $this->parameter = $param;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getParameters(): array
    {
        return $this->parameter;
    }

    public function appendLayer(SvgPrintable $layer): void
    {
        $this->layer[] = $layer;
    }

    public function printSvg(): void
    {
        $side = $this->cell->getSize();
        echo '<?xml version="1.0" encoding="utf-8"?>';
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">\n";
        echo "<title>{$this->title}</title>\n";
        echo "<desc><![CDATA[" . json_encode($this->parameter) . "]]></desc>\n";
        echo "<rect x=\"0\" y=\"0\" width=\"$side\" height=\"$side\" fill=\"white\"/>\n";

        $this->cell->printSvg();

        foreach ($this->layer as $layer) {
            $layer->printSvg();
        }

        echo '</svg>';
    }

    /**
     * This static helper does not really belong here but in a repository.
     * On the other hand, this extraction is tightly bound to this class format in printSvg method
     * @param string $content
     * @return \stdClass
     */
    static public function extractMetadata(string $content): \stdClass
    {
        $doc = new \DOMDocument();
        $doc->loadXML($content);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('svg', 'http://www.w3.org/2000/svg');
        $title = trim($xpath->query('/svg:svg/svg:title')->item(0)->nodeValue);
        $desc = json_decode($xpath->query('/svg:svg/svg:desc')->item(0)->nodeValue, true);

        return (object) [
                    'tagTitle' => $title,
                    'tagDesc' => $desc
        ];
    }

}
