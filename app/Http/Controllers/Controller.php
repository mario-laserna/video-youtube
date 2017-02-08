<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\File;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function uploadFileProcessor($input, $destinationPath, $currentFile=null)
    {
        $filename = "";
        if($input->isValid())
        {
            $extension = $input->getClientOriginalExtension();
            $tmpfile_name = pathinfo($input->getClientOriginalName(), PATHINFO_FILENAME); // file
            $filename =  str_replace(" ", "_", $tmpfile_name) .'_'.rand(1111, 9999) . '.' . $extension;

            if(!File::exists($destinationPath))
            {
                File::makeDirectory($destinationPath, $mode = 0777, true);
            }

            $input->move($destinationPath, $filename);

            //Borrar el archivo actual si tiene
            if ($currentFile != null && $currentFile != "") {
                File::delete($destinationPath . '/' . $currentFile);
            }
        }

        return $filename;
    }
}
