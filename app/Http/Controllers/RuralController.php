<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Rural;
use App\Http\Controllers\ChamadoController;


class RuralController extends Controller
{

    public function index(Request $request) 
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $chamados = Rural::where('user_cpf', $request->user()->cpf)->orderBy('id', 'desc')->get();

        return response()->json([
            'chamados-rurais' => $chamados
        ]);
    }

    public function store(Request $request)
    {

        if (!$request->user()) {
            return response()->json(['error' => 'Você precisa estar logado para abrir um chamado.'], 401);
        }

        $request->validate([
            'ticket_service' => 'required|string|max:255',
            'ticket_subject' => 'required|string|max:255',
            'ticket_address' => 'required|string|max:255',
            'ticket_description' => 'required',
        ]);

        $chamadoController = new ChamadoController();

        $lat = $chamadoController->getLat($request->ticket_address);
        $lgn = $chamadoController->getLng($request->ticket_address);

        $rural = Rural::create([
            'rand_id' => rand(0, 999999),
            'user_name' => $request->user()->name,
            'user_cpf' => $request->user()->cpf,
            'user_phone' => $request->user()->telefone,
            'user_email' => $request->user()->email,
            'user_address' => $request->user()->rua . ', ' . $request->user()->numero . ', ' . $request->user()->bairro . ', ' . $request->user()->cidade . ' - ' . $request->user()->estado . ', ' . $request->user()->cep,
            'ticket_service' => $request->ticket_service,
            'ticket_subject' => $chamadoController->formatString($request->ticket_subject),
            'ticket_title' => $request->ticket_subject,
            'ticket_address' => $request->ticket_address,
            'ticket_latitude' => $lat,
            'ticket_longitude' => $lgn,
            'ticket_description' => $request->ticket_description,
        ]);

        return response()->json([
            'message' => 'Chamado aberto com sucesso!',
            'chamado-rural' => $rural
        ], 201);

    }
}