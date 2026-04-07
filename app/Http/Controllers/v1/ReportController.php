<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Http\Resources\ReportResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;


class ReportController extends Controller
{

    /**
     * Store the report
     * 
     * @authenticated
     * 
     * @return JsonResponse Returns the created report.
     */
    public function store(Request $request, Item $item): JsonResponse
    {

        $user_id = auth('sanctum')->user()->id;
        
        if($item->user_id === $user_id){
            return $this->forbidden('You cannot report your own item');
        }

        $report = Report::where('user_id', $user_id)->where('item_id', $item->id)->first();
        if($report){
            return $this->forbidden('You have already reported this item');
        }

        $request->validate([
            'reason' => 'required|string',
            'message' => 'nullable|string|max:500',
        ]);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'reason' => $request->reason,
            'message' => $request->message,
        ]);

        return $this->success(new ReportResource($report), 'Report submitted successfully');
    }
}
