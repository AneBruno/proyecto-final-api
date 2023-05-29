<?php

namespace App\Modules\Clientes\Empresas;

use App\Modules\Base\Emails\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class MailEmpresaHabilitada extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($empresa)
    {
        $this->subject = 'Bienvenido a Negocios de Granos';
        $this->empresa = $empresa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.empresa-habilitada', [
            'empresa' => $this->empresa,
        ]);
    }
}
