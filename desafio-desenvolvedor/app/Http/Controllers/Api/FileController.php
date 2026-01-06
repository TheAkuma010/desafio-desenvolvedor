<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        // lógica para upload de arquivo + evitar arquivos duplicados
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:20480', // 20MB
        ]);

        $file = $request->file('file');

        // gerar hash do conteúdo do arquivo
        $hash = md5_file($file->getRealPath());

        if (FileUpload::where('file_hash', $hash)->exists()) {
            return response()->json([
                'message' => 'Este arquivo já foi enviado anteriormente.'
            ], 409);
        }

        // salvar arquivo no disco
        $path = $file->storeAs('uploads', $hash . '.' . $file->getClientOriginalExtension());

        $upload = FileUpload::create([
            'filename' => $file->getClientOriginalName(),
            'file_hash' => $hash,
            'status' => 'PENDING',
        ]);

        return response()->json([
            'message' => 'Arquivo enviado com sucesso. Processamento em andamento.',
            'upload_id' => $upload->id,
        ], 201);
    }
}
