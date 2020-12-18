<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Packaging\Space\SpaceService;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    public function __construct()
    {
        $this->spaceService = new SpaceService;
    }

    public function getAreas()
    {
        $result = $this->spaceService->getAreas();

        return  response()->json($result[0], $result[1]);
    }

    public function getAreaInputs()
    {
        $result = $this->spaceService->getAreaInputs();

        return  response()->json($result[0], $result[1]);
    }

    public function getPartInputs()
    {
        $result = $this->spaceService->getPartInputs();

        return  response()->json($result[0], $result[1]);
    }


    public function calculate(Request $request)
    {
        $obj['area'] = $request->input('area');
        $obj['parts'] = $request->input('parts');

        $result = $this->spaceService->calculate($obj);

        return response()->json($result[0], $result[1]);
    }
}
