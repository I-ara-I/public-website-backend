<?php

namespace App\Services\Packaging;

class SpecificationService
{
  private $request = [
    "material" => '',
    "shape" => '',
    "length" => 0,
    "lengthUnit" => '',
    "width" => 0,
    "widthUnit" => '',
    "height" => 0,
    "heightUnit" => '',
    "side_fold" => 0,
    "side_foldUnit" => '',
    "strength" => 0,
    "strengthUnit" => '',
    "specificWeight" => 0,
    "thickness" => 0,
    "thicknessUnit" => '',
    "grammage" => 0,
    "grammageUnit" => '',
    "volumeWeight" => 0,
    "volumeWeightUnit" => '',
    "price" => 0,
    "priceUnit" => ''
  ];

  private $results = [];


  private $materials = [
    ['material' => 'foil', 'label' => 'Folie'],
    ['material' => 'solid', 'label' => 'Vollmaterial'],
    ['material' => 'foam', 'label' => 'Schaum']
  ];

  private $shapes = [
    ['shape' => 'sheet', 'label' => 'Zuschnitt'],
    ['shape' => 'flat_bag', 'label' => 'Flachbeutel'],
    ['shape' => 'side_gusset_bag', 'label' => 'Seitenfaltenbeutel']
  ];

  private $inputs = [
    'material' => [
      'foil' => [
        ['name' => "strength", "value" => "", 'label' => "Stärke", 'required' => false, 'selectedUnit' => '', 'units' => [
          ['unit' => 'micrometer', 'label' => 'µm'],
          ['unit' => 'millimeter', 'label' => 'mm']
        ]],
        ['name' => "specificWeight", "value" => "0,92", 'label' => "spezifisches Gewicht", 'required' => false]
      ],
      'solid' => [
        ['name' => "thickness", "value" => "", 'label' => "Dicke", 'required' => false, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => "grammage", "value" => "", 'label' => "Grammatur", 'required' => false, 'selectedUnit' => '', 'units' => [
          ['unit' => 'gram', 'label' => 'g/m²'],
          ['unit' => 'kilogram', 'label' => 'kg/m²']
        ]]
      ],
      'foam' => [
        ['name' => "thickness", "value" => "", 'label' => "Dicke", 'required' => false, 'selectedUnit' => '',  'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => "volumeWeight", "value" => "", 'label' => "Raumgewicht", 'required' => false, 'selectedUnit' => '', 'units' => [
          ['unit' => 'kilogram', 'label' => 'kg/m³'],
          ['unit' => 'gram', 'label' => 'g/m³']
        ]]
      ]
    ],
    'shape' => [
      'sheet' => [
        ['name' => 'length', 'value' => "", 'label' => 'Länge', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => 'width', 'value' => "", 'label' => 'Breite', 'required' => true, 'selectedUnit' => '',  'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]]
      ],
      'flat_bag' => [
        ['name' => 'length', 'value' => "", 'label' => 'Länge', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => 'width', 'value' => "", 'label' => 'Breite', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]]
      ],
      'side_gusset_bag' => [
        ['name' => 'width', 'value' => "", 'label' => 'Breite', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => 'side_fold', 'value' => "", 'label' => 'gesamte Seitenfalte', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]],
        ['name' => 'height', 'value' => "", 'label' => 'Höhe', 'required' => true, 'selectedUnit' => '', 'units' => [
          ['unit' => 'millimeter', 'label' => 'mm'],
          ['unit' => 'centimeter', 'label' => 'cm'],
          ['unit' => 'meter', 'label' => 'm']
        ]]
      ]
    ],
    'price' => [['name' => 'price', 'value' => "", 'label' => 'Preis', 'required' => false, 'selectedUnit' => '', 'units' => [
      ['unit' => 'piece', 'label' => 'Stück'],
      ['unit' => 'squareMeter', 'label' => 'm²'],
      ['unit' => 'kilogram', 'label' => 'kg'],
      ['unit' => 'cubicMeter', 'label' => 'm³'],
    ]]]
  ];

  private $placeholder = false;

  public function getMaterials()
  {
    return $this->materials;
  }

  public function getShapes()
  {
    return $this->shapes;
  }

  public function getInputs()
  {
    return $this->inputs;
  }

  public function getResults($material, $shape, $inputs)
  {
    $isValid = $this->validation($material, $shape, $inputs);
    if (!$isValid['validMaterial'] || !$isValid['validShape']) {
      return [['message' => 'invalid material or shape'], 422];
    } else {
      $this->setRequestValues($material, $shape, $inputs);
      $this->replaceComma();
      $this->formatValues();
      $this->calculateResults();
      $this->formatResults();
      return [$this->results, 200];
    }
  }

  public function getEmptyResults()
  {
    $array = ['unit', 'surface', 'weight', 'volume'];

    $emptyResults = [
      'unit' => $this->placeholder,
      'surface' => $this->placeholder,
      'weight' => $this->placeholder,
      'volume' => $this->placeholder,
      'price' => $this->placeholder,

    ];

    foreach ($array as $key) {
      $newArray[$key] = $emptyResults;
    }

    return $newArray;
  }

  private function validation($material, $shape)
  {
    $validMaterial = false;
    $validShape = false;

    foreach ($this->materials as $key => $value) {
      $inArray = in_array($material, $this->materials[$key]);
      if ($inArray !== false) {
        $validMaterial = true;
      }
    }

    foreach ($this->shapes as $key => $value) {
      $inArray = in_array($shape, $this->shapes[$key]);
      if ($inArray !== false) {
        $validShape = true;
      }
    }

    return ['validMaterial' => $validMaterial, 'validShape' => $validShape];
  }

  private function setRequestValues($material, $shape, $inputs)
  {
    $this->request['material'] = $material;
    $this->request['shape'] = $shape;
    foreach ($inputs as $array) {
      foreach ($array as $key => $value) {
        if ($key === 'name') {
          $this->request[$value] = $array['value'];
          $selectedUnit = $value . 'Unit';
          if (isset($array['selectedUnit'])) {
            $this->request[$selectedUnit] = $array['selectedUnit'];
          }
        }
      }
    }
  }

  private function replaceComma()
  {
    foreach ($this->request as $key => $value) {
      $newValue = str_replace(",", ".", $value);
      $this->request[$key] = $newValue;
    }
  }

  private function formatValues()
  {
    foreach ($this->request as $key => $value) {
      if (strpos($key, "Unit")) {
        $unit = str_replace("Unit", "", $key);
        switch ($value) {
          case 'micrometer':
            $this->request[$unit] = (float)$this->request[$unit] / 1000000;
            break;
          case 'millimeter':
            $this->request[$unit] = (float)$this->request[$unit] / 1000;
            break;
          case 'centimeter':
            $this->request[$unit] = (float)$this->request[$unit] / 100;
            break;
          case 'meter':
            $this->request[$unit] = (float)$this->request[$unit];
            break;
          case 'gram':
            $this->request[$unit] = (float)$this->request[$unit] / 1000;
            break;
          case 'kilogram':
            $this->request[$unit] = (float)$this->request[$unit];
            break;
        }
      }
    }
  }

  private function formatResults()
  {
    foreach ($this->results as $arrayKey => $arrayValue) {
      foreach ($arrayValue as $key => $value) {
        if (is_numeric($value)) {
          $newValue = number_format($value, 5, ',', '.');
          while (substr($newValue, -1) === "0") {
            $newValue = rtrim($newValue, "0");
          }
          while (substr($newValue, -1) === ",") {
            $newValue = rtrim($newValue, ",");
          }
          $this->results[$arrayKey][$key] = $newValue;
        }
      }
    }
    $this->results['unit']['unit'] = $this->placeholder;
    $this->results['surface']['surface'] = $this->placeholder;
    $this->results['weight']['weight'] = $this->placeholder;
    $this->results['volume']['volume'] = $this->placeholder;
  }

  private function calculateResults()
  {
    switch ($this->request['shape']) {
      case 'sheet':
        $this->calculateSurface($this->request['shape']);
        $this->calculateWeight($this->request['material']);
        $this->calculateVolume($this->request['material']);
        $this->calculatePrice();
        break;
      case 'flat_bag':
        $this->calculateSurface($this->request['shape']);
        $this->calculateWeight($this->request['material']);
        $this->calculateVolume($this->request['material']);
        $this->calculatePrice();
        break;
      case 'side_gusset_bag':
        $this->calculateSurface($this->request['shape']);
        $this->calculateWeight($this->request['material']);
        $this->calculateVolume($this->request['material']);
        $this->calculatePrice();
        break;
    }
  }

  private function calculateSurface($shape)
  {
    switch ($shape) {
      case 'sheet':
        if ($this->request['length'] && $this->request['width']) {
          $this->results['unit']['surface'] = $this->request['length'] * $this->request['width'];
          $this->results['surface']['unit'] = 1 / $this->results['unit']['surface'];
        }
        break;
      case 'flat_bag':
        if ($this->request['length'] && $this->request['width']) {
          $this->results['unit']['surface'] = $this->request['length'] * $this->request['width'] * 2;
          $this->results['surface']['unit'] = 1 / $this->results['unit']['surface'];
        }
        break;
      case 'side_gusset_bag':
        if ($this->request['width'] &&  $this->request['side_fold'] && $this->request['height']) {
          $this->results['unit']['surface'] = (($this->request['width'] + $this->request['side_fold']) * $this->request['height']) * 2;
          $this->results['surface']['unit'] = 1 / $this->results['unit']['surface'];
        }
        break;
    }

    $this->results['unit']['surface'] = (isset($this->results['unit']['surface']) && $this->results['unit']['surface'] == true) ? $this->results['unit']['surface'] : $this->placeholder;
    $this->results['surface']['unit'] = (isset($this->results['surface']['unit']) && $this->results['surface']['unit'] == true) ? $this->results['surface']['unit'] : $this->placeholder;
  }

  private function calculateWeight($material)
  {
    switch ($material) {
      case 'foil':
        if ($this->request['strength'] && $this->request['specificWeight']) {
          $this->results['unit']['weight'] = $this->results['unit']['surface'] * $this->request['strength'] * 1000 * $this->request['specificWeight'];
          $this->results['surface']['weight'] = 1 * $this->request['strength'] * 1000 * $this->request['specificWeight'];
          $this->results['weight']['unit'] = 1 / $this->results['unit']['weight'];
          $this->results['weight']['surface'] = 1 / $this->results['surface']['weight'];
        } else {
          $this->results['unit']['weight'] = $this->placeholder;
          $this->results['surface']['weight'] = $this->placeholder;
          $this->results['weight']['unit'] = $this->placeholder;
          $this->results['weight']['surface'] = $this->placeholder;
        }
        break;
      case 'solid':
        if ($this->request['grammage']) {
          $this->results['unit']['weight'] = $this->results['unit']['surface'] * $this->request['grammage'];
          $this->results['surface']['weight'] = 1 * $this->request['grammage'];
          $this->results['weight']['unit'] = 1 / $this->results['unit']['weight'];
          $this->results['weight']['surface'] = 1 / $this->results['surface']['weight'];
        }
        break;
      case 'foam':
        if ($this->request['volumeWeight'] && $this->request['thickness']) {
          $this->results['unit']['weight'] = $this->results['unit']['surface'] * $this->request['volumeWeight'] * $this->request['thickness'];
          $this->results['surface']['weight'] = 1 * $this->request['volumeWeight'] * $this->request['thickness'];
          $this->results['weight']['unit'] = 1 / $this->results['unit']['weight'];
          $this->results['weight']['surface'] = 1 / $this->results['surface']['weight'];
        }
        break;
    }

    $this->results['unit']['weight'] = (isset($this->results['unit']['weight']) && $this->results['unit']['weight'] == true) ? $this->results['unit']['weight'] : $this->placeholder;
    $this->results['surface']['weight'] = (isset($this->results['surface']['weight']) && $this->results['surface']['weight'] == true) ? $this->results['surface']['weight'] : $this->placeholder;
    $this->results['weight']['unit'] = (isset($this->results['weight']['unit']) && $this->results['weight']['unit'] == true) ? $this->results['weight']['unit'] : $this->placeholder;
    $this->results['weight']['surface'] = (isset($this->results['weight']['surface']) && $this->results['weight']['surface'] == true) ? $this->results['weight']['surface'] : $this->placeholder;
  }

  private function calculateVolume($material)
  {
    switch ($material) {
      case 'foil':
        if ($this->request['strength']) {
          $this->results['unit']['volume'] = $this->results['unit']['surface'] * $this->request['strength'];
          $this->results['volume']['unit'] = 1 / $this->results['unit']['volume'];
          $this->results['surface']['volume'] = 1 * $this->request['strength'];
          $this->results['volume']['surface'] = 1 / $this->results['surface']['volume'];
        }
        if ($this->request['strength'] && $this->request['specificWeight']) {
          $this->results['weight']['volume'] = $this->results['weight']['unit'] * $this->results['unit']['volume'];
          $this->results['volume']['weight'] = 1 /  $this->results['weight']['volume'];
        }
        break;
      case 'solid':
        if ($this->request['thickness']) {
          $this->results['unit']['volume'] = $this->results['unit']['surface'] * $this->request['thickness'];
          $this->results['volume']['unit'] = 1 / $this->results['unit']['volume'];
          $this->results['surface']['volume'] = 1 * $this->request['thickness'];
          $this->results['volume']['surface'] = 1 / $this->results['surface']['volume'];
        }
        if ($this->request['thickness'] && $this->request['grammage']) {
          $this->results['weight']['volume'] = $this->results['weight']['unit'] * $this->results['unit']['volume'];
          $this->results['volume']['weight'] = 1 /  $this->results['weight']['volume'];
        }
        break;
      case 'foam':
        if ($this->request['thickness']) {
          $this->results['unit']['volume'] = $this->results['unit']['surface'] * $this->request['thickness'];
          $this->results['volume']['unit'] = 1 / $this->results['unit']['volume'];
          $this->results['surface']['volume'] = 1 * $this->request['thickness'];
          $this->results['volume']['surface'] = 1 / $this->results['surface']['volume'];
        }
        if ($this->request['thickness'] && $this->request['volumeWeight']) {
          $this->results['weight']['volume'] = $this->results['weight']['unit'] * $this->results['unit']['volume'];
          $this->results['volume']['weight'] = 1 /  $this->results['weight']['volume'];
        }
        break;
    }

    $this->results['unit']['volume'] = (isset($this->results['unit']['volume']) && $this->results['unit']['volume'] == true) ? $this->results['unit']['volume'] : $this->placeholder;
    $this->results['volume']['unit'] = (isset($this->results['volume']['unit']) && $this->results['volume']['unit'] == true) ? $this->results['volume']['unit'] : $this->placeholder;
    $this->results['surface']['volume'] = (isset($this->results['surface']['volume']) && $this->results['surface']['volume'] == true) ? $this->results['surface']['volume'] : $this->placeholder;
    $this->results['volume']['surface'] = (isset($this->results['volume']['surface']) && $this->results['volume']['surface'] == true) ? $this->results['volume']['surface'] : $this->placeholder;
    $this->results['weight']['volume'] = (isset($this->results['weight']['volume']) && $this->results['weight']['volume'] == true) ? $this->results['weight']['volume'] : $this->placeholder;
    $this->results['volume']['weight'] = (isset($this->results['volume']['weight']) && $this->results['volume']['weight'] == true) ? $this->results['volume']['weight'] : $this->placeholder;
  }

  private function calculatePrice()
  {
    if ($this->request['price']) {
      switch ($this->request['priceUnit']) {
        case 'piece':
          $this->results['unit']['price'] = (float)$this->request['price'];
          $this->results['surface']['price'] = 1 / $this->results['unit']['surface'] * $this->request['price'];
          if ($this->results['unit']['weight']) {
            $this->results['weight']['price'] = 1 / $this->results['unit']['weight'] * $this->request['price'];
          }
          if ($this->results['unit']['volume']) {
            $this->results['volume']['price'] = 1 / $this->results['unit']['volume'] * $this->request['price'];
          }
          break;
        case 'squareMeter':
          $this->results['surface']['price'] = (float)$this->request['price'];
          $this->results['unit']['price'] = $this->results['unit']['surface'] * $this->request['price'];
          if ($this->results['weight']['surface']) {
            $this->results['weight']['price'] = $this->results['weight']['surface'] * $this->request['price'];
          }
          if ($this->results['volume']['surface']) {
            $this->results['volume']['price'] = $this->results['volume']['surface'] * $this->request['price'];
          }
          break;
        case 'kilogram':
          $this->results['weight']['price'] = (float)$this->request['price'];
          if ($this->results['unit']['weight']) {
            $this->results['unit']['price'] = $this->results['unit']['weight'] * $this->request['price'];
          }
          if ($this->results['surface']['weight']) {
            $this->results['surface']['price'] = $this->results['surface']['weight'] * $this->request['price'];
          }
          if ($this->results['volume']['weight']) {
            $this->results['volume']['price'] = $this->results['volume']['weight'] * $this->request['price'];
          }
          break;
        case 'cubicMeter':
          $this->results['volume']['price'] = (float)$this->request['price'];
          if ($this->results['unit']['volume']) {
            $this->results['unit']['price'] = $this->results['unit']['volume'] * $this->request['price'];
          }
          if ($this->results['surface']['volume']) {
            $this->results['surface']['price'] = $this->results['surface']['volume'] * $this->request['price'];
          }
          if ($this->results['weight']['volume']) {
            $this->results['weight']['price'] = $this->results['weight']['volume'] * $this->request['price'];
          }
          break;
      }
    }

    $this->results['unit']['price'] = (isset($this->results['unit']['price']) && $this->results['unit']['price'] == true) ? $this->results['unit']['price'] : $this->placeholder;
    $this->results['surface']['price'] = (isset($this->results['surface']['price']) && $this->results['surface']['price'] == true) ? $this->results['surface']['price'] : $this->placeholder;
    $this->results['weight']['price'] = (isset($this->results['weight']['price']) && $this->results['weight']['price'] == true) ? $this->results['weight']['price'] : $this->placeholder;
    $this->results['volume']['price'] = (isset($this->results['volume']['price']) && $this->results['volume']['price'] == true) ? $this->results['volume']['price'] : $this->placeholder;
  }
}
