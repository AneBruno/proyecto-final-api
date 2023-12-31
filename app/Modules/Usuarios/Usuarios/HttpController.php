<?php

namespace App\Modules\Usuarios\Usuarios;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Usuarios\User;
use App\Modules\Usuarios\Roles\RolHelper;
use Illuminate\Http\Request;
use App\Mail\UsuarioHabilitadoMail;

class HttpController extends Controller {

    public function index(Request $request) {
        $collection = User::listar(
            $request->get('page' ,    0),
            $request->get('limit'  , 10),
            $request->get('filtros', []),
            $request->get('ordenes', [])
        );

        return UserResource::collection($collection);
    }

    public function show($id): UserResource {
        $user = User::getById($id);
        return new UserResource($user);
    }

    public function update() {

    }

    public function actualizarDatosPersonales(ActualizarDatosPersonalesRequest $request): UserResource {
        $user = UserService::actualizarDatosPersonales(
            $request->user()->getKey(),
            $request->post('nombre'    ),
            $request->post('apellido'  ),
            $request->post('telefono'  )
        );

        return new UserResource($user);
    }

    public function actualizarDatosPorAdministrador(int $id, ActualizarDatosPorAdministradorRequest $request): UserResource {
        $this->validarAdministrador();

        $user = UserService::actualizarDatosPorAdministrador($id,
            $request->get('rol_id'),
        );

        return new UserResource($user);
    }

    /**
     *
     * @param int $id
     * @param Request $request
     * @return UserResource
     */
    public function habilitar(int $id, HabilitarRequest $request): UserResource {
        $this->validarAdministrador();

        $user = UserService::habilitar($id, $request->boolean('habilitar'));
        
        return new UserResource($user);
    }

}
  /*if ($user->habilitado == true){
            UserService::enviarMail($user, new UsuarioHabilitadoMail($user));
        }*/