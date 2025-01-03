<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\PromotionRule;

class PromotionController extends Controller
{
    public function addPromotion(Request $request){
        $validatedData=$request->validate([
            'title'=>'required|string|max:225',
            'promotion_description'=>'nullable|string|max:225',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'categories'=>'required|string|max:225',
            'promotion_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rules'=>'nullable|array',
            'rules.*.min_price'=>'required_with:rules|numeric|min:0',
            'rules.*.discount_percentage'=>'required_with:rules|numeric|min:0|max:100'
        ]);

        try{
            //Handle image upload if provided
            $imagePath=null;
            if($request->hasFile('promotion_image')){
                $imagePath=$request->file('promotion_image')->store('images/promotion','public'); 
            }

            //create promotion data
            $promotion=Promotion::create([
                'title'=>$validatedData['title'],
                'promotion_description'=>$validatedData['promotion_description'] ?? null,
                'promotion_image'=>$imagePath,
                'start_date'=>$validatedData['start_date'],
                'end_date'=>$validatedData['end_date'],
                'categories'=>$validatedData['categories'],
            ]);

            //Add rules if provided
            if(!empty($validateData['rules'])){
                foreach($validatedData['rules'] as $rule){
                    PromotionRule::create([
                        'promotion_id'=>$promotion->id,
                        'min_price'=>$rule['min_price'],
                        'discount_percentage'=>$rule['discount_percentage'],
                    ]);
                }
            }

            return response()->json([
                'message'=>'Promotion added successfully',
                'promotion'=>$promotion,
            ],201);
        }catch(\Exception $e){
            return response()->json([
                'message'=>'Error adding promotion',
                'error'=>$e->getMessage(),
            ],500);
        }

    }

    //get all promotions
    public function getAllPromotions(){
        try{
            $currentDate=now();
            $promotions=Promotion::where('start_date','<=',$currentDate)
            ->where('end_date','>=',$currentDate)
            ->with('rules')
            ->get();

            return response()->json([
                'message'=>'Available promotions retrieved successfully',
                'promotions'=>$promotions
            ],201);
        }catch(\Exception $e){
            return response()->json([
                'message'=>'Error retrieving promotions',
                'error'=>$e->getMessage(),
            ],500);
        }

    }

    public function getPromotionById($id){
        try{
            $promotion = Promotion :: with('rules')->find($id);

            if(!$promotion){
                return response()->json([
                    'message'=>'promotion not found',
                ],404);
            }

            return response()->json([
                'message'=>'Promotion retrieved successfully',
                'promotion'=>$promotion,
            ],200);
            
        }catch(\Exception $e){
            return response()->json([
                'message'=>'Error retrieving promotion',
                'error'=>$e->getMessage(),     
            ],500);
        }
    }

}
