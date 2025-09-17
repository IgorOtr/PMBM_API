<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use Illuminate\Support\Facades\Http;


class AnimalController extends Controller
{
    public function listCastracoes(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $castracoes = Animal::where('user_cpf', $request->user()->cpf)->where('service', 'castracao-animal')->orderBy('id', 'desc')->get();

        return response()->json([
            'castracoes' => $castracoes
        ]);
    }

    public function listDenuncias(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $denuncias = Animal::where('user_cpf', $request->user()->cpf)->where('service', 'denuncia-animal')->orderBy('id', 'desc')->get();

        return response()->json([
            'denuncias' => $denuncias
        ]);
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


    public function storeCastracao(Request $request)
    {

        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $request->validate([
            'animal_name' => 'required|string',
            'animal_raca' => 'required|string',
            'animal_sexo' => 'required|string',
            'animal_weight' => 'required|integer',
            'animal_age' => 'required|integer'
        ]);

        $data = [
            'animal_name' => $request->animal_name,
            'animal_raca' => $request->animal_raca,
            'animal_sexo' => $request->animal_sexo,
            'animal_weight' => $request->animal_weight,
            'animal_age' => $request->animal_age,
        ];

        $animal = new Animal();
        $animal->user_input = $data;
        $animal->user_name = $request->user()->name;
        $animal->user_cpf = $request->user()->cpf;
        $animal->user_email = $request->user()->email;
        $animal->user_phone = $request->user()->telefone;
        $animal->user_address = $request->user()->rua . ', ' . $request->user()->numero . ', ' . $request->user()->bairro . ', ' . $request->user()->cidade . ' - ' . $request->user()->estado . ', ' . $request->user()->cep;
        $animal->latitude = $this->getLat($animal->user_address);
        $animal->longitude = $this->getLng($animal->user_address);
        $animal->service = 'castracao-animal';

        if ($animal->save()) {

            return response()->json([
                'message' => 'Solicitação registrada com sucesso!',
                'chamado' => $animal
            ], 201);
        } else {

            return response()->json([
                'error' => 'Erro ao registrar sua Solicitação. Por favor, tente novamente.'
            ], 400);
        }
    }

    public function storeDenuncia(Request $request)
    {

        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $request->validate([
            'animal_subject' => 'required|string|max:255',
            'animal_description' => 'required|string',
            'animal_address' => 'required|string',
        ]);

        if ($request->hasFile('animal_image')) {

            $image = $request->file('animal_image');
            $image_name = 'animals_' . md5(time()) . '.' . $image->getClientOriginalExtension();

            $caminho = $image->storeAs('uploads', $image_name, 'public');

            if ($caminho) {

                $imagem = $image_name;
            }

        } else {

            $imagem = "Sem Imagem";
        }

        $data = [
            'animal_subject' => $request->animal_subject,
            'animal_description' => $request->animal_description,
            'animal_address' => $request->animal_address,
            'animal_image' => $imagem,
        ];

        $animal = new Animal();
        $animal->user_input = $data;
        $animal->user_name = $request->user()->name;
        $animal->user_cpf = $request->user()->cpf;
        $animal->user_email = $request->user()->email;
        $animal->user_phone = $request->user()->telefone;
        $animal->latitude = $this->getLat($request->animal_address);
        $animal->longitude = $this->getLng($request->animal_address);
        $animal->user_address = $request->user()->rua . ', ' . $request->user()->numero . ', ' . $request->user()->bairro . ', ' . $request->user()->cidade . ' - ' . $request->user()->estado . ', ' . $request->user()->cep;
        $animal->service = 'denuncia-animal';

        if ($animal->save()) {

            return response()->json([
                'message' => 'Denúncia registrada com sucesso!',
                'chamado' => $animal], 201);

        } else {

            return response()->json([
                'error' => 'Erro ao registrar a denúncia. Por favor, tente novamente.'
            ]);
        }
    }
}
