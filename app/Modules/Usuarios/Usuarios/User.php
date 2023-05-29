<?php

namespace App\Modules\Usuarios\Usuarios;

use App\Modules\Usuarios\Roles\Rol;
use App\Modules\Usuarios\Roles\RolHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use App\Tools\ModelRepository;
use Laravel\Passport\HasApiTokens;

class User extends ModelRepository implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;

    protected $table = "usuarios";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'nombre',
        'apellido',
        'telefono',
        'avatar',
        'rol_id',
        'oficina_id',
        'habilitado',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'nombreCompleto',
    ];

    /**
     * @param int ...$roles
     * @return bool
     */
    public function hasAnyRol(int ...$roles)
    {
        return in_array($this->rol_id, $roles);
    }

    /**
     * @return BelongsTo
     */
    public function rol()
    {
        return $this->belongsTo(\App\Modules\Usuarios\Roles\Rol::class);
    }

    /**
     * @return BelongsTo
     */
    public function oficina()
    {
        return $this->belongsTo(\App\Modules\Oficinas\Oficina::class);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    static public function crear(string $email, string $nombre, string $apellido, int $rol_id): self {
        $row           = new static;
        $row->email    = $email;
        $row->nombre   = $nombre;
        $row->apellido = $apellido;
        $row->rol_id   = $rol_id;

        $row->insertar();

        return $row;
    }

    public function actualizarDatosPersonales(string $nombre, string $apellido, bool $suscripto_notificaciones, ?int $telefono = null) {
        $this->nombre     = $nombre;
        $this->apellido   = $apellido;
        $this->suscripto_notificaciones = $suscripto_notificaciones;
        $this->telefono   = $telefono;
        $this->guardar();
        return $this;
    }

    public function actualizarRol(int $rol_id) {
        $this->rol_id = $rol_id;
        $this->guardar();
        return $this;
    }

    public function actualizarOficina(?int $oficina_id) {
        $this->oficina_id = $oficina_id;
        $this->guardar();
        return $this;
    }

	public function actualizarPermisosGestionSaldos (
		int $aprobacionCbu,
		int $aprobacionGerenciaComercial,
		int $aprobacionDptoCreditos,
		int $aprobacionDptoFinanzas,
		int $confirmacionPagos
	) {
		$this->aprobacion_cbu = $aprobacionCbu;
		$this->aprobacion_gerencia_comercial = $aprobacionGerenciaComercial;
		$this->aprobacion_dpto_creditos = $aprobacionDptoCreditos;
		$this->aprobacion_dpto_finanzas = $aprobacionDptoFinanzas;
		$this->confirmacion_pagos = $confirmacionPagos;

		$this->guardar();
	}

    public function habilitar(): self {
        $this->habilitado = true;
        $this->guardar();
        return $this;
    }

    public function deshabilitar(): self {
        $this->habilitado = false;
        $this->guardar();
        return $this;
    }

    public function getNombreCompletoAttribute(): string {
        return trim($this->nombre) . ' ' . trim($this->apellido);
    }

    static public function aplicarFiltros(Builder $query, array $filtros) {

        parent::aplicarFiltros($query, $filtros);

        foreach($filtros as $nombre => $valor) {
            if ($nombre === 'ids') {
                $valor = is_array($valor) ? $valor : [$valor];
                $query->whereIn('id', $valor);
            }
            if (in_array($nombre, ['rol_id', 'oficina_id'])) {
                $query->whereIn($nombre, is_array($valor)?$valor:[$valor]);
            }
            if (in_array($nombre, [
            	'email',
				'habilitado',
				'aprobacion_cbu',
				'aprobacion_gerencia_comercial',
				'confirmacion_pagos'
			]) && strlen("{$valor}")>0) {
                $query->where($nombre, $valor);
            }

            if (in_array($nombre, ['!email'])) {
            	$query->where(substr($nombre, 1), '!=', $valor);
			}

            if ($nombre === 'busqueda' && $valor) {
                $query->where(function(Builder $query) use ($valor) {
                   $query
                        ->orWhere('nombre',   'like', "%{$valor}%")
                        ->orWhere('apellido', 'like', "%{$valor}%")
                        ->orWhere('email',    'like', "%{$valor}%");
                });
            }
        }
    }

    static public function aplicarOrdenes(Builder $query, array $ordenes) {
        parent::aplicarOrdenes($query, $ordenes);

        foreach($ordenes as $columna => $valor) {
            if ($columna == 'rol') {
                $roles = implode(', ', $valor);
                $query->orderByRaw("field(rol_id, {$roles}) asc");
            }
            if ($columna == 'nombre_completo') {
                $query->orderBy('nombre', $valor);
                $query->orderBy('apellido', $valor);
            }

            if ($columna == 'id_matches') {
                $query->orderByRaw("field(id, {$valor}) desc");
            }
        }
    }

    /**
     * @deprecated usar esAdministradorPlataforma()
     */
    public function isAdministradorPlataforma(): bool {
        return $this->rol_id === RolHelper::ADMINISTRADOR_PLATAFORMA;
    }

    public function esAdministradorPlataforma(): bool {
        return $this->rol_id === RolHelper::ADMINISTRADOR_PLATAFORMA;
    }
    
    public function esResponsableComercial(): bool {
        return $this->rol_id === RolHelper::RESPONSABLE_COMERCIAL;
    }
    
    public function esComercial(): bool {
        return $this->rol_id === RolHelper::COMERCIAL;
    }
    
    public function esRepresentante(): bool {
        return $this->rol_id === RolHelper::REPRESENTATE;
    }
    
    public function esAdministrativo(): bool {
        return $this->rol_id === RolHelper::ADMINISTRATIVO;
    }
    
    public function esSoporte(): bool {
        return $this->rol_id === RolHelper::SOPORTE_EQUIPO_NDG;
    }
}