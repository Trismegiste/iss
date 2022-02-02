<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

/**
 * Dumps svg content to the standard stream
 */
interface SvgPrintable
{

    public function printSvg(): void;
}
