<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\Sections;
use App\Models\Classes;
use App\MyClasses\Filter;
use File;
use Illuminate\Support\Facades\Validator;


class Edit_Student extends Controller
{
   
    public function update(Request $request, $id)
    {
        $filter=new Filter();
        

        // $input = file_get_contents('php://input');
        // $input = json_decode(file_get_contents("php://input"),true);
        
        //  parse_str($input, $request);
        //error_log(print_r($request,TRUE));
         error_log(print_r($request->all(),TRUE));
        $student=Students::where('id',$id)->first();
         $mail=$student->email;
         error_log($mail);
         error_log($request->all()['email']);

         if($mail!=$request->all()['email'])
         $validator = $filter->index($request);
        else
        $validator = Validator::make($request->all(), [
            
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            'picture'=>'required|image',
            'phone_number' => 'required|string|max:50',
        'section_name' => 'required'
            
        ]);        
        
  
         
        if ($validator->fails()) {
            //  Session::flash('error', $validator->messages()->first());
            //  return redirect()->back()->withInput();
             return response()->json([
                'status'=> 400,
                'message'=>$validator->messages()->first()
             ], 400); 
        }
       
        else{
        $section=Sections::where('name',$request->all()['section_name']);
        $section_id=$section->first()->id;


        $picture = $request->file('picture');
        // error_log(print_r($request->file('picture')->getClientOriginalName(),TRUE));
        $new_picture=time().$student->first_name.'-'.$student->last_name;
        // $new_picture=time().$picture->getClientOriginalName();
         if(File::exists($student->picture)){
            File::delete($student->picture);
        }
         $picture->move(public_path().'/uploads/students/',$new_picture);


        Students::where('id', $id)
                ->update(['first_name' => $request->all()['first_name'],
                'last_name' => $request->all()['last_name'],
                'email'=>$request->all()['email'],
                'phone_number'=>$request->all()['phone_number'],
                'section_id'=>$section_id,
                'picture'=>'uploads/students/'.$new_picture
                        ]
                        );


        return response()->json([
            'status'=> 200,
            'message'=>"Edited successfully"
         ], 200); 
         
    }}

}
