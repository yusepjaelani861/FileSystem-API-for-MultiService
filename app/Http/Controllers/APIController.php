<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Resize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class APIController extends Controller
{
    public function list(Request $request)
    {
        $validator = $request->validate([
            'p' => 'integer|nullable',
            'app_id' => 'integer|required',
        ]);
        if ($request->p) {
            $files = Files::where('app_id', $request->app_id)->paginate($request->p);

            # Keterangan
            # $request->p = nilai pagination yang diinginkan, berbentuk integer

            return response()->json($files);
        } else {
            $files = Files::where('app_id', $request->app_id)->get();

            return response()->json($files);
        }
    }

    public function search(Request $request)
    {
        $validator = $request->validate([
            'q' => 'required|string',
        ]);

        $files = Files::where('name', 'like', '%'.$request->q.'%')->get();

        # Keterangan
        # $request->search = isi yang mau dicari dengan pencarian filter pada nama file

        return response()->json($files);
    }

    public function filter(Request $request)
    {
        $validator = $request->validate([
            'f' => 'required|string',
            'search' => 'required|string',
        ]);

        $files = Files::where($request->f, 'like', '%'.$request->search.'%')->get();

        # Keterangan
        # $request->f = nama yang mau di filter
        # $request->search = isi yang di filter

        return response()->json($files);
    }

    public function rename(Request $request)
    {
        $validator = $request->validate([
            'file_id' => 'required|string',
            'name' => 'required|string',
        ]);

        $files = Files::where('file_id', $request->file_id)->first();

        # Cek apakah ada extension file atau tidak pada nama file yang baru
        $cek = substr($request->name, strrpos($request->name, '.') + 1);
        if ($cek == $files->extension) {

            # Rename file pada database
            $files->name = $request->name;
            $files->save();
            # End of rename file pada database

            return response()->json([
                'status' => 'success',
                'message' => 'Nama file berhasil diubah',
            ]);
        } else {
            # Rename file pada database
            $files->name = $request->name . '.' . $files->extension;
            $files->save();
            # End of rename file pada database

            return response()->json([
                'status' => 'success',
                'message' => 'Nama file berhasil diubah',
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validator = $request->validate([
            'file_id' => 'required|string',
        ]);

        $files = Files::where('file_id', $request->file_id)->first();

        # Delete file asli pada storage
        Storage::disk('ftp')->deleteDirectory('files/' . $files->app_id . '/' . $files->file_id);

        # Delete file pada database resize
        $resize = Resize::where('file_id', $request->file_id)->delete();

        # Delete file pada database
        $files->delete();
        # End of delete file pada database

        return response()->json([
            'status' => 'success',
            'message' => 'File berhasil dihapus'
        ]);
    }

    public function upload(Request $request)
    {
        $validator = $request->validate([
            'app_id' => 'required|integer',
            'file.*' => 'required|file',
        ]);

        # Tipe Array jika lebih dari 1 file
        $data_set = array();
        $image_set = array();
        # End of Tipe Array jika lebih dari 1 file

        foreach ($request->file as $file) {
            $uniqid = uniqid();
            $data = [
                'app_id' => $request->app_id,
                'file_id' => $uniqid,
                'name' => $file->getClientOriginalName(),
                'path' => 'files/' . $request->app_id . '/' . $uniqid . '/' . $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'ftp',
                'url' => Storage::disk('ftp')->url('files/' . $request->app_id . '/' . $uniqid . '/' . $file->getClientOriginalName()),
            ];

            $data_set[] = $data;

            if (Storage::disk('ftp')->put('files/' . $request->app_id . '/' . $uniqid . '/' . $file->getClientOriginalName(), file_get_contents($file))) {
                Files::create($data);
                $image = Resize::where('file_id', $uniqid)->first();
                $image_set[] = $image;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File gagal diupload',
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File berhasil diupload',
            'data' => $data_set,
            'resize_image' => $image_set,
        ]);
    }

    public function download($file_id)
    {
        $files = Files::where('file_id', $file_id)->first();

        return response()->streamDownload(function () use ($files) {
            echo file_get_contents($files->url);
        }, $files->name, [
            'Content-Type' => $files->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $files->name . '"',
            'Content-Length' => $files->size,
        ]);
    }

    public function getImage($file_id, Request $request)
    {
        $validator = $request->validate([
            'width' => 'integer|nullable',
            'height' => 'integer|nullable',
        ]);

        $resize = Resize::where('file_id', $file_id)->first();
        $width = Resize::where('file_id', $file_id)->where('width', $request->input('width'))->first();
        $height = Resize::where('file_id', $file_id)->where('height', $request->input('height'))->first();
        $files = Files::where('file_id', $file_id)->first();
        
        if ($files != null) {
            if ($width != null) {
                if ($width->width == $request->input('width')) {
                    return response()->stream(function () use ($width) {
                        echo file_get_contents($width->url);
                    }, 200, [
                        'Content-Type' => $width->mime_type,
                        'Content-Disposition' => 'inline; filename="' . $width->name . '"',
                    ]);
                }
            } else if ($height != null) {
                if ($height->height == $request->input('height')) {
                    return response()->stream(function () use ($height) {
                        echo file_get_contents($height->url);
                    }, 200, [
                        'Content-Type' => $height->mime_type,
                        'Content-Disposition' => 'inline; filename="' . $height->name . '"',
                    ]);
                }
            } else if (!$request->input('width') && !$request->input('height')) {
                return response()->stream(function () use ($files) {
                    echo file_get_contents($files->url);
                }, 200, [
                    'Content-Type' => $files->mime_type,
                    'Content-Disposition' => 'inline; filename="' . $files->name . '"',
                ]);
            } else {
                $image = (new ImageController)->resizePreview($file_id, $request->input('width'), $request->input('height'), false);
    
                return $image;
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File tidak ditemukan',
            ]);
        }
        
    }
}
