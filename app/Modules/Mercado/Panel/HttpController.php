<?php

namespace App\Modules\Mercado\Panel;

use App\Modules\Mercado\Panel\FormRequests\CambiarEstadosRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Kodear\Laravel\Repository\Exceptions\RepositoryException;

class HttpController extends Controller
{
    protected PanelService $service;

    /**
     * HttpController constructor.
     * @param PanelService $service
     */
    public function __construct(PanelService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $filtros = $request->input('filtros', []);

        if (!array_key_exists('estado', $filtros)) {
            $filtros['estado'] = 'todas';
        }

        $rs = $this->service->listar($filtros);

        return $this->jsonCollection($rs);
    }

    /**
     * @param $clave
     * @param Request $request
     * @return JsonResource
     * @throws RepositoryException
     */
    public function show($clave, Request $request)
    {
        $row = $this->service->getByClave($clave);
        return $this->json($row);
    }

    public function cambiarEstados(CambiarEstadosRequest $request)
    {
        $estado = $request->get('estado');
        $posiciones = $request->get('posiciones_ids');

        PanelService::cambiarEstados($posiciones, $estado);
        return response()->json(['data' => ['estado' => $estado]], 200);
    }
}
