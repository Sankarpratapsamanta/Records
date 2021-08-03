<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
// use Illuminate\Contracts\Foundation\Application;
// use Illuminate\Contracts\View\Factory;
// use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Model\Sales;
// use App\jobs\SalesCsvProcess;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class salesController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }
    public function delete()
    {
        Storage::deleteDirectory('chunks');
        return "deleted";
    }
    public function uploadLargeFiles(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

            // $disk = Storage::disk(config('filesystems.default'));
            $disk = Storage::disk(config('s3'));

            $path = $disk->putFileAs('tapan', $file, $fileName);
            // dd($path);
            // echo $path = Storage::disk('s3')->putFileAs('tapan', $request->file('image'), time().'.'.$extension, 'public');
            // delete chunked file
            unlink($file->getPathname());
            return "successfully uploaded";
            // return [
            //     'path' => asset('storage/' . $path),
            //     'filename' => $fileName
            // ];
        }

        // otherwise return percentage informatoin
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
}
