<?php

namespace App\Modules\Usuarios\Usuarios;

use App\Factories\ModelFilesServiceFactory;
use App\Mail\UsuarioNuevoMail;
use App\Mail\UsuarioHabilitadoMail;
use App\Modules\Usuarios\Roles\Rol;
use App\Helpers\RolesHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;

class UserService
{

    /**
     *
     * @param string $email
     * @return User | null
     */
    static public function getByEmail(string $email)
    {
        return User::getOne(['email' => $email]);
    }

    /**
     *
     * @param User $user
     * @param Rol $rol
     */
    static public function setRole(User $user, Rol $rol): User
    {
        return User::getById($user->id)->actualizarRol($rol->id);
    }

    /**
     *
     * @param int $usuario_id
     * @param int $rol_id
     */
    static public function actualizarDatosPorAdministrador(
    	int $usuario_id,
		int $rol_id
	): User
    {
        $usuario = User::getById($usuario_id);
        $usuario->actualizarRol($rol_id);

        return $usuario;
    }

    /**
     *
     * @param int $usuario_id
     * @param string $nombre
     * @param string $apellido
     * @param int $telefono
     * @return User
     */
    static public function actualizarDatosPersonales(
        int           $usuario_id,
        string        $nombre,
        string        $apellido,
        ?int          $telefono = null
    ): User {
        $user = User::getById($usuario_id)->actualizarDatosPersonales($nombre, $apellido, $telefono);

        return $user;
    }

    /**
     *
     * @param int $usuario_id
     * @param bool $habilitado
     * @return User
     */
    static public function habilitar(int $usuario_id, bool $habilitado): User{
        $user = User::getById($usuario_id);
        if ($habilitado) {
            $user->habilitar();
            Mail::to($user->email)->send(new UsuarioHabilitadoMail($user)); 

            return $user;

        } else {
            $user->deshabilitar();
            return $user;
        }
        //return $habilitado ? $user->habilitar() : $user->deshabilitar();
    }

    static public function listarAdministradores() {
    	/*return User::listar(1, 0, [
    		'rol_id' => RolesHelper::ADMINISTRADOR_DE_PLATAFORMA,
			'habilitado' => true,
			'!email' => config('magic.email')
		]);*/
        return User::where([
            'rol_id' => RolesHelper::ADMINISTRADOR_DE_PLATAFORMA,
            'habilitado' => true,
        ])
        ->where('email', '!=', config('magic.email'))
        ->get();
	}

    static public function enviarMail(User $user, Mailable $mail): void {

        Mail::to($user)->send($mail);
    }

}
