<?php

namespace App\Services\Packaging\Space;

class SpaceService
{
    private $areaMatrix = [];
    private $results = [];
    private $counter = ['counter1' => 0, 'counter2' => 0];
    private $parts = [];

    private $availableSpace = [];

    private $areas = [
        ['key' => 'free', 'name' => 'Freie Eingabe', 'length' => ['value' => '', 'unit' => ''], 'width' => ['value' => '', 'unit' => '']],
        ['key' => 'truck', 'name' => 'LKW 1 - 13,60 m x 2,44 m', 'length' => ['value' => 1360, 'unit' => 'cm'], 'width' => ['value' => 244, 'unit' => 'cm']],
        ['key' => 'truck2', 'name' => 'LKW 2 - 7,60 m x 2,45 m', 'length' => ['value' => 760, 'unit' => 'cm'], 'width' => ['value' => 245, 'unit' => 'cm']],
        ['key' => 'euro', 'name' => 'EU-Palette - 1,20 m x 0,80 m', 'length' => ['value' => 120, 'unit' => 'cm'], 'width' => ['value' => 80, 'unit' => 'cm']],
        ['key' => 'halfEuro', 'name' => 'halbe EU-Palette - 0,80 m x 0,60 m', 'length' => ['value' => 80, 'unit' => 'cm'], 'width' => ['value' => 60, 'unit' => 'cm']],
        ['key' => 'industry', 'name' => 'Industrie-Palette - 1,20 m x 1,00 m', 'length' => ['value' => 120, 'unit' => 'cm'], 'width' => ['value' => 100, 'unit' => 'cm']],
    ];

    private $areaInputs = [
        ['name' => "length", 'valueLabel' => 'Länge', 'unitLabel' => 'Einheit', 'units' => [['name' => 'mm', 'unit' => 'mm'], ['name' => 'cm', 'unit' => 'cm']], 'required' => true],
        ['name' => "width", 'valueLabel' => 'Breite', 'unitLabel' => 'Einheit', 'units' => [['name' => 'mm', 'unit' => 'mm'], ['name' => 'cm', 'unit' => 'cm']], 'required' => true]
    ];

    private $partInputs = [
        ['name' => "length", 'valueLabel' => 'Länge', 'unitLabel' => 'Einheit', 'units' => [['name' => 'mm', 'unit' => 'mm'], ['name' => 'cm', 'unit' => 'cm']], 'required' => true],
        ['name' => "width", 'valueLabel' => 'Breite', 'unitLabel' => 'Einheit', 'units' => [['name' => 'mm', 'unit' => 'mm'], ['name' => 'cm', 'unit' => 'cm']], 'required' => true],
        ['name' => "quantity", 'valueLabel' => 'Menge',  'required' => false]
    ];

    public function getAreas()
    {
        return [$this->areas, 200];
    }

    public function getAreaInputs()
    {
        return [$this->areaInputs, 200];
    }

    public function getPartInputs()
    {
        return [$this->partInputs, 200];
    }

    public function calculate($request): array
    {

        $this->availableSpace = $request['area'];

        $counterParts = 1;

        foreach ($request['parts'] as $key => $value) {
            $index = 'part' . $counterParts;
            $counterParts++;
            $this->parts[$index] = $request['parts'][$key];
        }

        foreach ($this->parts as $part => &$value) {
            $value['length']['value'] = $this->formatValues($value['length']['value'], $value['length']['unit']);
            $value['width']['value'] = $this->formatValues($value['width']['value'], $value['width']['unit']);

            $validation = $this->validateRequest($part, $value, 'part');
            if (!$validation[0]) {
                return [$validation[1], $validation[2]];
            }
        }

        $this->availableSpace['length']['value'] = $this->formatValues($this->availableSpace['length']['value'], $this->availableSpace['length']['unit']);
        $this->availableSpace['width']['value'] = $this->formatValues($this->availableSpace['width']['value'], $this->availableSpace['width']['unit']);

        $validation = $this->validateRequest('availabelSpace', $this->availableSpace, 'space');
        if (!$validation[0]) {
            return $validation;
        }

        $this->createAreaMatrix();

        foreach ($this->parts as $part) {
            $length = $part['length']['value'];
            $width = $part['width']['value'];
            $startQuantity = $part['quantity']['value'];

            $remainingQuantity = $this->calculateUtilization($length, $width, $startQuantity, 'counter1', 'area1');
            $this->calculateUtilization($width, $length, $remainingQuantity, 'counter1', 'area1');
            $remainingQuantity = $this->calculateUtilization($width, $length, $startQuantity, 'counter2', 'area2');
            $this->calculateUtilization($length, $width, $remainingQuantity, 'counter2', 'area2');
        }

        if ($this->counter['counter1'] >= $this->counter['counter2']) {
            $area = "area" . 1;
            $counter = "counter" . 1;
        } else {
            $area = "area" . 2;
            $counter = "counter" . 2;
        }

        if (isset($this->results[$area])) {
            return [['area' => ['length' => $this->availableSpace['length']['value'], 'width' => $this->availableSpace['width']['value']], 'totalParts' => $this->counter[$counter], 'partsDistrubution' =>  $this->results[$area]], 200];
        }
        return [['totalParts' => $this->counter[$counter], 'partsDistrubution' =>  'no result'], 200];
    }

    public function validateRequest($key, $value, $kind)
    {

        $length = $value['length']['value'];
        $lengthUnit = $value['length']['unit'];
        $width = $value['width']['value'];
        $widthUnit = $value['width']['unit'];

        if (!$length || !$lengthUnit || !$width || !$widthUnit) {
            return [false, "Error: Value or Unit of $key are empty.", 422];
        }

        $units = ['mm', 'cm', 'm'];

        $lengthUnitExists = in_array($lengthUnit, $units);
        $widthUnitExists = in_array($widthUnit, $units);

        if (!$lengthUnitExists || !$widthUnitExists) {
            return [false, "Error: Unit of $key does not exist", 422];
        }

        if (array_key_exists('quantity', $value)) {
            if ($value['quantity']['value'] == false) {
                return [false, "Error: Quantity of $key is empty", 422];
            }
            if (!is_numeric($value['quantity']['value']) && $value['quantity']['value'] !== 'max') {
                return [false, "Error: Quantity of $key is not valid", 422];
            }
        }

        return ['true'];
    }



    private function formatValues($value, $unit)
    {
        $value = str_replace(",", ".", $value);

        switch ($unit) {
            case 'mm':
                $value = (float)$value / 10;
                break;
            case 'cm':
                $value = (float)$value;
                break;
            case 'm':
                $value = (float)$value * 100;
                break;
            default:
                $value = 'empty';
                break;
        }

        if (is_float($value)) {
            $value = (int)ceil($value);
        }

        return $value;
    }

    public function createAreaMatrix()
    {

        for ($i = 1; $i <= $this->availableSpace['length']['value']; $i++) {
            for ($y = 1; $y <= $this->availableSpace['width']['value']; $y++) {
                $coordinates = 'X' . $i . 'Y' . $y;
                $this->areaMatrix['area1'][$coordinates] = ['isFree' => true];
            }
        }

        $this->areaMatrix['area2'] = $this->areaMatrix['area1'];
    }

    public function calculateUtilization($length, $width, $quantity, $counter, $area)
    {
        $unitCounter = 0;

        foreach ($this->areaMatrix[$area] as $key => $value) {
            if ($this->areaMatrix[$area][$key]['isFree'] === true) {
                $posOfY = strpos($key, 'Y');
                $x = str_replace('X', '', substr($key, 0, $posOfY));
                $y = str_replace('Y', '', substr($key, $posOfY));

                if (($x + $length - 1) <= $this->availableSpace['length']['value'] && ($y + $width - 1) <= $this->availableSpace['width']['value']) {
                    $x1 = ('X' . ($x) . 'Y' . $y);
                    $x2 = ('X' . ($x + $length - 1) . 'Y' . $y);
                    $y1 = ('X' . ($x) . 'Y' . ($y + $width - 1));
                    $y2 = ('X' . ($x + $length - 1) . 'Y' . ($y + $width - 1));

                    if (
                        $this->areaMatrix[$area][$x1]['isFree'] === true &&
                        $this->areaMatrix[$area][$x2]['isFree'] === true &&
                        $this->areaMatrix[$area][$y1]['isFree'] === true &&
                        $this->areaMatrix[$area][$y2]['isFree'] === true

                    ) {
                        for ($i = $x; $i <= ($x + $length - 1); $i++) {
                            $index = 'X' . $i . 'Y' . $y;
                            $this->areaMatrix[$area][$index]['isFree'] = false;
                            for ($a = $y; $a <= ($y + $width - 1); $a++) {
                                $index2 = 'X' . $i . 'Y' . $a;
                                $this->areaMatrix[$area][$index2]['isFree'] = false;
                            }
                        }
                        $this->counter[$counter]++;
                        $unitCounter++;

                        $part = $this->counter[$counter];

                        $this->results[$area][] = ['part' => $part, 'position' => ['1' => $x1, '2' => $x2, '3' => $y1, '4' => $y2], 'size' => ["length" => $length, "width" => $width]];
                    }
                }
            }
            if ($unitCounter == $quantity && $quantity !== 'max') {
                break;
            }
        }
        if ($quantity === 'max') {
            return 'max';
        } else {
            return $quantity - $unitCounter;
        }
    }
}
