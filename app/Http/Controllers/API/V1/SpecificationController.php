<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecificationRequest;
use App\Services\Packaging\SpecificationService;

class SpecificationController extends Controller
{
    public function __construct(SpecificationService $specificationService)
    {
        $this->service = $specificationService;
    }

    public function getMaterials()
    {
        return $this->service->getMaterials();
    }

    public function getShapes()
    {
        return $this->service->getShapes();
    }

    public function getInputs()
    {
        return $this->service->getInputs();
    }

    public function getResults(SpecificationRequest $request)
    {

        $material =  $request->input('material');
        $shape = $request->input('shape');
        $inputs = $request->input('inputs');

        $result = $this->service->getResults($material, $shape, $inputs);

        return  response()->json($result[0], $result[1]);
    }

    public function getEmptyResults()
    {
        return $this->service->getEmptyResults();
    }
}
