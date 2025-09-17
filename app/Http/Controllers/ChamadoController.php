<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chamado;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class ChamadoController extends Controller
{

    public function index(Request $request) 
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $chamados = Chamado::where('user_cpf', $request->user()->cpf)->orderBy('id', 'desc')->get();

        return response()->json([
            'chamados' => $chamados
        ]);
    }

    public static function formatString(string $string): string
    {
        // 1. Converte a string para minúsculas
        $lowerString = Str::lower($string);

        // 2. Transforma caracteres acentuados em não acentuados
        $unaccentedString = Str::ascii($lowerString);

        // 3. Substitui espaços e outros caracteres não alfanuméricos por hífens
        $slug = Str::slug($unaccentedString, '-');

        return $slug;
    }

    public function getLat($endereco)
    {
        $apiKey = env('GOOGLE_MAPS_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $endereco,
            'key' => $apiKey
        ]);

        $data = $response->json();

        if ($data['status'] === 'OK') {

            $location = $data['results'][0]['geometry']['location'];

            return $location['lat'];
        }

        return null;
    }

    public function getLng($endereco)
    {
        $apiKey = env('GOOGLE_MAPS_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $endereco,
            'key' => $apiKey
        ]);

        $data = $response->json();

        if ($data['status'] === 'OK') {

            $location = $data['results'][0]['geometry']['location'];

            return $location['lng'];
        }

        return null;
    }


    public function store(Request $request)
    {
        // Garante que o usuário está logado via token Sanctum
        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        // Validação
        $request->validate([
            'ticket_subject' => 'required|string|max:255',
            'ticket_address' => 'required|string|max:255',
            'ticket_description' => 'required|string',
        ]);

        // Upload do arquivo
        if ($request->hasFile('ticket_file_name')) {

            $file = $request->file('ticket_file_name');
            $fileName = md5(time()) . '.' . $file->getClientOriginalExtension();

            $caminho = $file->storeAs('uploads', $fileName, 'public');

            if ($caminho) {

                $imagem = $fileName;
            }

        } else {

            $imagem = "Sem Imagem";
        }

        $chamado = Chamado::create([
            'rand_id' => rand(0, 999999),
            'user_name' => $request->user()->name,
            'user_cpf' => $request->user()->cpf,
            'user_phone' => $request->user()->telefone,
            'user_email' => $request->user()->email,
            'user_address' => $request->user()->rua . ', ' . $request->user()->numero . ', ' . $request->user()->bairro . ', ' . $request->user()->cidade . ' - ' . $request->user()->estado . ', ' . $request->user()->cep,
            'ticket_subject' => $this->formatString($request->ticket_subject),
            'ticket_title' => $request->ticket_subject,
            'ticket_address' => $request->ticket_address,
            'ticket_latitude' => $this->getLat($request->ticket_address),
            'ticket_longitude' => $this->getLng($request->ticket_address),
            'ticket_description' => $request->ticket_description,
            'ticket_file_name' => $imagem,
            'ticket_service' => 'smmu',
        ]);

        return response()->json([
            'message' => 'Chamado aberto com sucesso!',
            'chamado' => $chamado
        ], 201);

    }
}
